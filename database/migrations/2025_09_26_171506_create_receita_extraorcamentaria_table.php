<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('receita_extraorcamentaria')) {
            // Cria a tabela se não existir
            Schema::create('receita_extraorcamentaria', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                $table->boolean('managementunitname')->nullable();
                $table->integer('points_managementunitname')->nullable();

                $table->boolean('managementunitid')->nullable();
                $table->integer('points_managementunitid')->nullable();

                $table->boolean('realiizedamount')->nullable();
                $table->integer('points_realiizedamount')->nullable();

                $table->boolean('extrabudgetrevenuesource')->nullable();
                $table->integer('points_extrabudgetrevenuesource')->nullable();

                $table->boolean('extrabudgetrevenuedescription')->nullable();
                $table->integer('points_extrabudgetrevenuedescription')->nullable();

                $table->boolean('extrabudgetrevenueid')->nullable();
                $table->integer('points_extrabudgetrevenueid')->nullable();

                $table->boolean('nomenclature')->nullable();
                $table->integer('points_nomenclature')->nullable();

                $table->boolean('extrabudgetrevenuehistory')->nullable();
                $table->integer('points_extrabudgetrevenuehistory')->nullable();

                $table->unsignedBigInteger('municipio_id'); // FK para municipios
                $table->timestamps();

                // Define a chave estrangeira
                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, atualiza colunas se necessário
            Schema::table('receita_extraorcamentaria', function (Blueprint $table) {
                $columns = Schema::getColumnListing('receita_extraorcamentaria');

                $campos = [
                    'managementunitname',
                    'managementunitid',
                    'realiizedamount',
                    'extrabudgetrevenuesource',
                    'extrabudgetrevenuedescription',
                    'extrabudgetrevenueid',
                    'nomenclature',
                    'extrabudgetrevenuehistory',
                    'municipio_id'
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
                            $table->boolean($campo)->nullable();
                            $table->integer("points_{$campo}")->nullable();
                        }
                    } else {
                        // Se o campo já existe mas o points não, adiciona apenas o points
                        $pointsCampo = "points_{$campo}";
                        if (!in_array($pointsCampo, $columns)) {
                            $table->integer($pointsCampo)->nullable();
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('receita_extraorcamentaria');
    }
};
