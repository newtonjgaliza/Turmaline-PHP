<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class MunicipioController extends Controller
{
    /**
     * Retorna a lista de municípios que correspondem ao termo de busca
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscar(Request $request)
    {
        $termo = $request->input('q', '');
        
        // Carrega o arquivo GeoJSON
        $filePath = base_path('paraiba.geojson');
        
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Arquivo de municípios não encontrado.'], 404);
        }
        
        $content = File::get($filePath);
        $data = json_decode($content, true);
        
        // Filtra os municípios que correspondem ao termo de busca
        $municipios = [];
        
        if (isset($data['features']) && is_array($data['features'])) {
            foreach ($data['features'] as $feature) {
                if (isset($feature['properties']['name'])) {
                    $nome = $feature['properties']['name'];
                    $id = $feature['properties']['id'] ?? null;
                    
                    // Verifica se o nome do município contém o termo de busca (case insensitive)
                    if (empty($termo) || stripos($nome, $termo) !== false) {
                        $municipios[] = [
                            'id' => $id,
                            'nome' => $nome,
                            'geometria' => $feature['geometry'] ?? null
                        ];
                    }
                }
            }
        }
        
        // Ordena os municípios por nome
        usort($municipios, function($a, $b) {
            return strcmp($a['nome'], $b['nome']);
        });
        
        return response()->json($municipios);
    }
}
