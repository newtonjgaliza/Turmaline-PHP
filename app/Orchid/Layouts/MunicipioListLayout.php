<?php

namespace App\Orchid\Layouts;

use App\Models\Municipio;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class MunicipioListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    protected $target = 'municipios';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('nome', 'Nome do Município')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('created_at', 'Criado em')
                ->render(fn (Municipio $municipio) => 
                    $municipio->created_at ? $municipio->created_at->format('d/m/Y H:i') : ''
                ),

            TD::make('updated_at', 'Atualizado em')
                ->render(fn (Municipio $municipio) => 
                    $municipio->updated_at ? $municipio->updated_at->format('d/m/Y H:i') : ''
                ),

            TD::make('Ações')
                ->align(TD::ALIGN_CENTER)
                ->width('150px')
                ->render(fn (Municipio $municipio) =>
                    Link::make('Editar')
                        ->icon('pencil')
                        ->route('platform.municipio.edit', $municipio)
                ),
        ];
    }
}
