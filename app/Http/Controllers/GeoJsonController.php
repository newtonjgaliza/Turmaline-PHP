<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class GeoJsonController extends Controller
{
    /**
     * Retorna o conteúdo do arquivo GeoJSON da Paraíba.
     *
     * @return \Illuminate\Http\Response
     */
    public function paraiba()
    {
        $filePath = base_path('paraiba.geojson');
        
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'Arquivo GeoJSON não encontrado.'], 404);
        }
        
        $content = File::get($filePath);
        
        return Response::make($content, 200, [
            'Content-Type' => 'application/geo+json',
            'Content-Disposition' => 'inline; filename="paraiba.geojson"',
        ]);
    }
}
