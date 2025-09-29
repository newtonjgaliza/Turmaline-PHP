<?php

namespace App\Orchid\Screens;

use App\Models\Municipio;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

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
            ]),
        ];
    }

    public function save(Municipio $municipio)
    {
        $municipio->fill(request()->get('municipio'))->save();

        Toast::info('Município salvo com sucesso!');
        return redirect()->route('platform.municipio');
    }
}
