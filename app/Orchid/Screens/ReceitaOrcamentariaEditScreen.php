<?php

namespace App\Orchid\Screens;

use App\Models\ReceitaOrcamentaria;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Actions\Button;

class ReceitaOrcamentariaEditScreen extends Screen
{
    public $name = 'Editar Receita Orçamentária';
    public $description = 'Alterar pontos';

    public function query(ReceitaOrcamentaria $receita): iterable
    {
        return [
            'receita' => $receita,
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Salvar')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Switcher::make('receita.managementunitname')
                    ->title('Management Unit Name'),

                Input::make('receita.points_managementunitname')
                    ->title('Pontos Management Unit Name'),

                Switcher::make('receita.managementunitid')
                    ->title('Management Unit ID'),

                Input::make('receita.points_managementunitid')
                    ->title('Pontos Management Unit ID'),

                Switcher::make('receita.budgetrevenuesource')
                    ->title('Revenue Source'),

                Input::make('receita.points_budgetrevenuesource')
                    ->title('Pontos Revenue Source'),

                Switcher::make('receita.budgetrevenuedescription')
                    ->title('Revenue Description'),

                Input::make('receita.points_budgetrevenuedescription')
                    ->title('Pontos Revenue Description'),

                Switcher::make('receita.predictedamount')
                    ->title('Valor Previsto'),

                Input::make('receita.points_predictedamount')
                    ->title('Pontos Valor Previsto'),

                Switcher::make('receita.collectionamount')
                    ->title('Valor Coletado'),

                Input::make('receita.points_collectionamount')
                    ->title('Pontos Valor Coletado'),
            ]),
        ];
    }

    public function save(ReceitaOrcamentaria $receita, $request)
    {
        $receita->fill($request->get('receita'))->save();

        \Orchid\Alert\Alert::info('Registro salvo com sucesso!');
        return redirect()->route('platform.receita');
    }
}
