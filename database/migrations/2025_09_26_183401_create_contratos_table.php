<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contratos')) {
            // Cria a tabela se não existir
            Schema::create('contratos', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                $table->boolean('managementunitname')->nullable();
                $table->integer('points_managementunitname')->nullable();

                $table->boolean('managementunitid')->nullable();
                $table->integer('points_managementunitid')->nullable();

                $table->boolean('contractorname')->nullable();
                $table->integer('points_contractorname')->nullable();

                $table->boolean('identificationnumber')->nullable();
                $table->integer('points_identificationnumber')->nullable();

                $table->boolean('publicationdate')->nullable();
                $table->integer('points_publicationdate')->nullable();

                $table->boolean('validitydate')->nullable();
                $table->integer('points_validitydate')->nullable();

                $table->boolean('contractamount')->nullable();
                $table->integer('points_contractamount')->nullable();

                $table->boolean('object')->nullable();
                $table->integer('points_object')->nullable();

                $table->boolean('contractid')->nullable();
                $table->integer('points_contractid')->nullable();

                $table->unsignedBigInteger('municipio_id')->nullable(); // FK
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios') // sempre no plural
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, atualiza colunas se necessário
            Schema::table('contratos', function (Blueprint $table) {
                $columns = Schema::getColumnListing('contratos');

                $campos = [
                    'managementunitname',
                    'managementunitid',
                    'contractorname',
                    'identificationnumber',
                    'publicationdate',
                    'validitydate',
                    'contractamount',
                    'object',
                    'contractid',
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
        Schema::dropIfExists('contratos');
    }
};
