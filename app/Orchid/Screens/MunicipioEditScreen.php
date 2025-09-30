<?php

namespace App\Orchid\Screens;

use App\Models\Municipio;
use App\Models\ReceitaOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Attachment\File;

class MunicipioEditScreen extends Screen
{
    public $name = 'Editar Município';
    public $description = 'Criar ou editar município';
    public $exists = false;

    public function query(Municipio $municipio): array
    {
        $this->exists = $municipio->exists;

        if ($this->exists) {
            $this->name = 'Editar Município';
        }

        return [
            'municipio' => $municipio,
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Salvar')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('municipio.nome')
                    ->title('Nome do Município')
                    ->required(),
                
                // Categoria: Receita Orçamentária
                CheckBox::make('categorias.receita_orcamentaria')
                    ->title('Receita Orçamentária')
                    ->placeholder('Upload de dados de Receita Orçamentária')
                    ->sendTrueOrFalse(),
                
                Upload::make('receita_orcamentaria_csv')
                    ->title('Arquivo CSV - Receita Orçamentária')
                    ->groups('receita_orcamentaria')
                    ->acceptedFiles('.csv')
                    ->maxFiles(1)
                    ->path('csv_uploads/' . date('Y/m/d'))
                    ->help('Faça o upload do arquivo CSV com os campos de Receita Orçamentária')
                    ->canSee($this->exists),
                
                // Adicione mais categorias aqui seguindo o mesmo padrão
                // Exemplo para próxima categoria:
                /*
                CheckBox::make('categorias.despesa_orcamentaria')
                    ->title('Despesa Orçamentária')
                    ->placeholder('Habilitar upload de dados de Despesa Orçamentária')
                    ->sendTrueOrFalse(),
                
                Upload::make('despesa_orcamentaria_csv')
                    ->title('Arquivo CSV - Despesa Orçamentária')
                    ->groups('despesa_orcamentaria')
                    ->acceptedFiles('.csv')
                    ->maxFiles(1)
                    ->storage('public/csv_uploads')
                    ->help('Faça o upload do arquivo CSV com os campos de Despesa Orçamentária')
                    ->canSee($this->exists),
                */
            ]),
        ];
    }

    public function save(Municipio $municipio, Request $request)
    {
        // Salva os dados básicos do município
        $municipio->fill($request->get('municipio'))->save();

        // Processa o upload do CSV de Receita Orçamentária, se enviado
        if ($request->hasFile('receita_orcamentaria_csv')) {
            $this->processarCsvReceitaOrcamentaria($municipio, $request->file('receita_orcamentaria_csv')[0]);
        }

        // Adicione processamento para outras categorias aqui

        Toast::info('Município e dados atualizados com sucesso!');
        return redirect()->route('platform.municipio.edit', $municipio->id);
    }

    /**
     * Processa o arquivo CSV de Receita Orçamentária
     */
    private function processarCsvReceitaOrcamentaria(Municipio $municipio, $file)
    {
        try {
            Log::info('Iniciando processamento do CSV para o município ID: ' . $municipio->id);
            
            // Verifica se o arquivo foi enviado corretamente
            if (!$file || !$file->isValid()) {
                $error = 'Arquivo inválido ou não enviado corretamente.';
                Log::error($error);
                throw new \Exception($error);
            }
            
            // Verifica se o arquivo tem conteúdo
            if ($file->getSize() === 0) {
                $error = 'O arquivo CSV está vazio.';
                Log::error($error);
                throw new \Exception($error);
            }
            
            // Lê o conteúdo do arquivo
            $csvContent = file_get_contents($file->getPathname());
            Log::info('Conteúdo bruto do arquivo:', ['primeiros_100_caracteres' => substr($csvContent, 0, 100) . '...']);
            
            $lines = explode("\n", $csvContent);
            $csvData = array_map('str_getcsv', $lines);
            
            // Remove linhas vazias
            $csvData = array_values(array_filter($csvData, function($row) {
                return !empty(array_filter($row, function($value) {
                    return $value !== null && $value !== '';
                }));
            }));
            
            // Log do conteúdo do CSV para depuração
            Log::info('Conteúdo do CSV processado:', ['data' => $csvData]);
            
            if (empty($csvData)) {
                throw new \Exception('O arquivo CSV está vazio após remoção de linhas vazias.');
            }
            
            // Remove o cabeçalho
            $header = array_shift($csvData);
            
            // Log do cabeçalho para depuração
            Log::info('Cabeçalho do CSV:', ['header' => $header]);
            
            if (empty($header)) {
                throw new \Exception('Cabeçalho do CSV não encontrado.');
            }
            
            // Normaliza os cabeçalhos
            $header = array_map('trim', $header);
            $header = array_map('strtolower', $header);
            
            // Encontra os índices das colunas (insensível a maiúsculas/minúsculas)
            $propriedadesIndex = array_search('propriedades', $header);
            $encontradasIndex = array_search('encontradas', $header);
            
            if ($propriedadesIndex === false || $encontradasIndex === false) {
                $errorMsg = 'Formato de CSV inválido. Cabeçalhos necessários: PROPRIEDADES, ENCONTRADAS. Cabeçalhos encontrados: ' . implode(', ', $header);
                Log::error($errorMsg);
                throw new \Exception($errorMsg);
            }
            
            // Busca ou cria um registro para o município
            $receita = ReceitaOrcamentaria::firstOrNew(['municipio_id' => $municipio->id]);
            Log::info('Dados atuais do registro no banco antes das alterações:', $receita->toArray());
            
            // Mapeamento direto dos campos do CSV para os campos do modelo
            // Os nomes já estão em minúsculas e correspondem exatamente
            $camposMapeados = [
                'managementunitname' => 'managementunitname',
                'managementunitid' => 'managementunitid',
                'budgetrevenuesource' => 'budgetrevenuesource',
                'budgetrevenuedescription' => 'budgetrevenuedescription',
                'predictedamount' => 'predictedamount',
                'collectionamount' => 'collectionamount'
            ];
            
            $alteracoes = [];
            
            // Processa cada linha do CSV
            foreach ($csvData as $linha => $row) {
                if (count($row) < 2) {
                    Log::warning("Linha $linha ignorada - não tem colunas suficientes", $row);
                    continue; // Pula linhas inválidas
                }
                
                $campo = strtolower(trim($row[$propriedadesIndex]));
                $valor = isset($row[$encontradasIndex]) ? trim($row[$encontradasIndex]) : '';
                $encontrado = strtoupper($valor) === 'SIM';
                
                Log::info("Processando linha $linha - Campo: $campo, Valor: $valor, Encontrado: " . ($encontrado ? 'Sim' : 'Não'));
                
                // Converte o nome do campo para minúsculas para garantir a correspondência
                $campoLower = strtolower(trim($campo));
                
                // Verifica se o campo está no mapeamento
                if (isset($camposMapeados[$campoLower])) {
                    $campoModelo = $camposMapeados[$campoLower];
                    $receita->$campoModelo = $encontrado;
                    $alteracoes[$campoModelo] = $encontrado;
                    Log::info("Campo mapeado: $campo -> $campoModelo = " . ($encontrado ? 'true' : 'false'));
                } else {
                    Log::warning("Campo não mapeado no CSV: $campoLower");
                }
            }
            
            if (empty($alteracoes)) {
                Log::warning('Nenhuma alteração foi feita nos dados.');
            } else {
                Log::info('Alterações a serem salvas:', $alteracoes);
                
                // Tenta salvar as alterações
                try {
                    $salvou = $receita->save();
                    
                    if ($salvou) {
                        Log::info('Dados salvos com sucesso!', $receita->toArray());
                        
                        // Verifica se os dados foram realmente salvos no banco
                        $dadosAtualizados = ReceitaOrcamentaria::where('municipio_id', $municipio->id)->first();
                        Log::info('Dados confirmados no banco de dados:', $dadosAtualizados ? $dadosAtualizados->toArray() : 'Registro não encontrado');
                    } else {
                        Log::error('Falha ao salvar os dados no banco.');
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar CSV de Receita Orçamentária: ' . $e->getMessage());
            throw $e; // Re-lança a exceção para ser tratada pelo Laravel
        }
    }
}
