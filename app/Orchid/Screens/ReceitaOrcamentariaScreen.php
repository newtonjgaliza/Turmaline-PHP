<?php

namespace App\Orchid\Screens;

use App\Models\ReceitaOrcamentaria;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;

class ReceitaOrcamentariaScreen extends Screen
{
    public $name = 'Receita Orçamentária';
    public $description = 'Gerenciar pontos da Receita Orçamentária';

    public function query(): iterable
    {
        return [
            'receitas' => ReceitaOrcamentaria::paginate(),
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Adicionar')->icon('plus')->route('platform.receita.edit'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('receitas', [
                'id' => 'ID',
                'municipio_id' => 'Município',
                'points_managementunitname' => 'Pontos Unit Name',
                'points_budgetrevenuesource' => 'Pontos Revenue Source',
                'points_predictedamount' => 'Pontos Previsto',
                'points_collectionamount' => 'Pontos Coletado',
            ]),
        ];
    }
}
