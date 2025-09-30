<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('procedimentos_licitatorios')) {
            // Cria a tabela se não existir
            Schema::create('procedimentos_licitatorios', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                $table->boolean('notice')->nullable();
                #$table->integer('points_notice')->nullable();

                $table->boolean('bigmodality')->nullable();
                #$table->integer('points_bigmodality')->nullable();

                $table->boolean('managementunitname')->nullable();
                #$table->integer('points_managementunitname')->nullable();

                $table->boolean('managementunitid')->nullable();
                #$table->integer('points_managementunitid')->nullable();

                $table->boolean('publicationdate')->nullable();
                #$table->integer('points_publicationdate')->nullable();

                $table->boolean('realizationdate')->nullable();
                #$table->integer('points_realizationdate')->nullable();

                $table->boolean('bitid')->nullable();
                #$table->integer('points_bitid')->nullable();

                $table->boolean('object')->nullable();
                #$table->integer('points_object')->nullable();

                $table->boolean('biddername')->nullable();
                #$table->integer('points_biddername')->nullable();

                $table->boolean('identificationnumber')->nullable();
                #$table->integer('points_identificationnumber')->nullable();

                $table->boolean('bidderproposalamount')->nullable();
                #$table->integer('points_bidderproposalamount')->nullable();

                $table->unsignedBigInteger('municipio_id')->nullable(); // FK
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios') // corrige o nome no plural
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, adiciona colunas que faltarem
            Schema::table('procedimentos_licitatorios', function (Blueprint $table) {
                $columns = Schema::getColumnListing('procedimentos_licitatorios');

                $campos = [
                    'notice',
                    'bigmodality',
                    'managementunitname',
                    'managementunitid',
                    'publicationdate',
                    'realizationdate',
                    'bitid',
                    'object',
                    'biddername',
                    'identificationnumber',
                    'bidderproposalamount',
                    'municipio_id'
                ];

                foreach ($campos as $campo) {
                    if (!in_array($campo, $columns)) {
                        if ($campo === 'municipio_id') {
                            $table->unsignedBigInteger('municipio_id')->nullable();
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
        Schema::dropIfExists('procedimentos_licitatorios');
    }
};
