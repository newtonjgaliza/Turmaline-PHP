<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeoJsonController;
use App\Http\Controllers\MunicipioController;

Route::get('/', function () {
    return view('welcome');
});

// Rota para a página de avaliação
Route::get('/avaliacao', function () {
    return view('avaliacao');
});

// Rota para a página de ranking
Route::get('/ranking', function () {
    return view('ranking');
});

// Rota para servir o arquivo GeoJSON
Route::get('/geojson/paraiba', [GeoJsonController::class, 'paraiba']);

// Rota para buscar municípios
Route::get('/api/municipios/buscar', [MunicipioController::class, 'buscar'])->name('municipios.buscar');
