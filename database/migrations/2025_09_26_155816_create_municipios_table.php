<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('municipios')) {
            // Se não existir, cria a tabela
            Schema::create('municipios', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY
                $table->string('nome', 150); // VARCHAR(150) NOT NULL
                $table->timestamps(); // created_at e updated_at
            });
        } else {
            // Se já existir, faz atualizações necessárias
            Schema::table('municipios', function (Blueprint $table) {
                if (!Schema::hasColumn('municipios', 'nome')) {
                    $table->string('nome', 150);
                }
                // Aqui você pode adicionar outras colunas futuras sem erro
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
