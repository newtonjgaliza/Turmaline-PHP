<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ReceitaOrcamentariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter todos os municípios
        $municipios = Municipio::all();

        foreach ($municipios as $municipio) {
            // Verificar se já existe um registro para este município
            $existe = DB::table('pontos_receita_orcamentaria')
                ->where('municipio_id', $municipio->id)
                ->exists();

            if (!$existe) {
                // Inserir os valores dos pontos na nova tabela
                // Valores 1 indicam que o campo está ativo
                DB::table('pontos_receita_orcamentaria')->insert([
                    'municipio_id' => $municipio->id,
                    'managementunitname' => 15, // Pontuação para managementunitname
                    'managementunitid' => 0,    // Pontuação para managementunitid
                    'budgetrevenuesource' => 5,  // Pontuação para budgetrevenuesource
                    'budgetrevenuedescription' => 5, // Pontuação para budgetrevenuedescription
                    'predictedamount' => 15,     // Pontuação para predictedamount
                    'collectionamount' => 15,    // Pontuação para collectionamount
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Dados de pontos para Receita Orçamentária foram semeados com sucesso na nova tabela!');
    }
}
