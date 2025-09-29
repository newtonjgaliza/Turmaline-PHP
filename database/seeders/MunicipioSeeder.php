<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        // Path to the GeoJSON file
        $json = File::get(public_path('paraiba.geojson'));
        $data = json_decode($json, true);
        
        // Check if the JSON was decoded successfully
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Error decoding JSON: ' . json_last_error_msg());
            return;
        }
        
        // Check if the features exist in the GeoJSON
        if (!isset($data['features']) || !is_array($data['features'])) {
            $this->command->error('Invalid GeoJSON format: missing features array');
            return;
        }
        
        $municipios = [];
        $nomesUnicos = [];
        
        // Extract unique municipio names
        foreach ($data['features'] as $feature) {
            if (isset($feature['properties']['name'])) {
                $nome = trim($feature['properties']['name']);
                $nomeLower = mb_strtolower($nome, 'UTF-8');
                
                // Only add if not already in the array (case-insensitive check)
                if (!in_array($nomeLower, $nomesUnicos, true)) {
                    $nomesUnicos[] = $nomeLower;
                    $municipios[] = [
                        'nome' => $nome,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        // Sort municipios alphabetically by name using a custom collation for Portuguese
        usort($municipios, function($a, $b) {
            $collator = new \Collator('pt_BR');
            return $collator->compare($a['nome'], $b['nome']);
        });
        
        // Clear existing data if needed
        if (!app()->environment('production')) {
            Municipio::truncate();
        }
        
        // Insert municipios into the database in chunks
        $chunks = array_chunk($municipios, 50);
        $totalInserted = 0;
        
        foreach ($chunks as $chunk) {
            $inserted = Municipio::insert($chunk);
            $totalInserted += count($chunk);
            $this->command->info(sprintf('Inserted %d municipios...', $totalInserted));
        }
        
        $this->command->info(sprintf('\nSuccessfully inserted %d municipios in total', $totalInserted));
    }
}
