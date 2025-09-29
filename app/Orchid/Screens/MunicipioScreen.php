<?php

namespace App\Orchid\Screens;

use App\Models\Municipio;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Orchid\Screen\Repository;

class MunicipioScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Municípios';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Gerenciamento de municípios';

    /**
     * Query data.
     *
     * @return array
     */
    /**
     * Query data.
     *
     * @return array
     */
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'municipios' => \App\Models\Municipio::query()
                ->filters()
                ->defaultSort('nome')
                ->paginate(20),
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('Adicionar'))
                ->icon('plus')
                ->route('platform.municipio.edit'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]
     */
    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            \App\Orchid\Layouts\MunicipioListLayout::class,
            
            // You can add more layouts here if needed
            // Layout::modal('modalExample', [
            //     Layout::rows([
            //         Input::make('example')
            //             ->title('Example')
            //             ->placeholder('Example')
            //     ])
            // ]),
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Municipio $municipio
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Municipio  $municipio
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Municipio $municipio)
    {
        $municipio->delete();

        Toast::info(__('Município removido com sucesso!'));

        return redirect()->route('platform.municipio');
    }


























}