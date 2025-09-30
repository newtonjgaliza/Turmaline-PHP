<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pontos_receita_orcamentaria')) {
            // Cria a tabela se não existir
            Schema::create('pontos_receita_orcamentaria', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY
                $table->integer('managementunitname')->nullable(false);
                $table->integer('managementunitid')->nullable(false);
                $table->integer('budgetrevenuesource')->nullable(false);
                $table->integer('budgetrevenuedescription')->nullable(false);
                $table->integer('predictedamount')->nullable(false);
                $table->integer('collectionamount')->nullable(false);
                $table->unsignedBigInteger('municipio_id');
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, adiciona colunas que faltarem
            Schema::table('pontos_receita_orcamentaria', function (Blueprint $table) {
                $columns = Schema::getColumnListing('pontos_receita_orcamentaria');

                $campos = [
                    'managementunitname',
                    'managementunitid',
                    'budgetrevenuesource',
                    'budgetrevenuedescription',
                    'predictedamount',
                    'collectionamount',
                    'municipio_id',
                ];

                foreach ($campos as $campo) {
                    if (!in_array($campo, $columns)) {
                        if ($campo === 'municipio_id') {
                            $table->unsignedBigInteger('municipio_id');
                            $table->foreign('municipio_id')
                                  ->references('id')
                                  ->on('municipios')
                                  ->onDelete('cascade')
                                  ->onUpdate('cascade');
                        } else {
                            $table->integer($campo)->nullable(false);
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos_receita_orcamentaria');
    }
};
