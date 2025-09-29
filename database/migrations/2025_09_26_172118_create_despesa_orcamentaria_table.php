<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('despesa_orcamentaria')) {
            // Cria a tabela se não existir
            Schema::create('despesa_orcamentaria', function (Blueprint $table) {
                $table->id();

                $table->boolean('fixedamount')->nullable();
                $table->integer('points_fixedamount')->nullable();

                $table->boolean('paymentamount')->nullable();
                $table->integer('points_paymentamount')->nullable();

                $table->boolean('managementunitname')->nullable();
                $table->integer('points_managementunitname')->nullable();

                $table->boolean('managementunitid')->nullable();
                $table->integer('points_managementunitid')->nullable();

                $table->boolean('budgetexpenditurefunction')->nullable();
                $table->integer('points_budgetexpenditurefunction')->nullable();

                $table->boolean('budgetexpendituresubfunction')->nullable();
                $table->integer('points_budgetexpendituresubfunction')->nullable();

                $table->boolean('budgetexpenditureprogram')->nullable();
                $table->integer('points_budgetexpenditureprogram')->nullable();

                $table->boolean('budgetexpenditureaction')->nullable();
                $table->integer('points_budgetexpenditureaction')->nullable();

                $table->boolean('economiccategory')->nullable();
                $table->integer('points_economiccategory')->nullable();

                $table->boolean('budgetnature')->nullable();
                $table->integer('points_budgetnature')->nullable();

                $table->boolean('budgetexpendituremodality')->nullable();
                $table->integer('points_budgetexpendituremodality')->nullable();

                $table->boolean('budgetexpenditureelement')->nullable();
                $table->integer('points_budgetexpenditureelement')->nullable();

                $table->boolean('commitedexpendituredate')->nullable();
                $table->integer('points_commitedexpendituredate')->nullable();

                $table->boolean('creditorname')->nullable();
                $table->integer('points_creditorname')->nullable();

                $table->boolean('identificationnumber')->nullable();
                $table->integer('points_identificationnumber')->nullable();

                $table->boolean('bitid')->nullable();
                $table->integer('points_bitid')->nullable();

                $table->boolean('commitedvalue')->nullable();
                $table->integer('points_commitedvalue')->nullable();

                $table->boolean('commitedexpenditurehistory')->nullable();
                $table->integer('points_commitedexpenditurehistory')->nullable();

                $table->unsignedBigInteger('municipio_id')->nullable();
                $table->timestamps();

                // Chave estrangeira
                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Atualiza colunas se a tabela já existir
            Schema::table('despesa_orcamentaria', function (Blueprint $table) {
                $columns = Schema::getColumnListing('despesa_orcamentaria');

                $campos = [
                    'fixedamount',
                    'paymentamount',
                    'managementunitname',
                    'managementunitid',
                    'budgetexpenditurefunction',
                    'budgetexpendituresubfunction',
                    'budgetexpenditureprogram',
                    'budgetexpenditureaction',
                    'economiccategory',
                    'budgetnature',
                    'budgetexpendituremodality',
                    'budgetexpenditureelement',
                    'commitedexpendituredate',
                    'creditorname',
                    'identificationnumber',
                    'bitid',
                    'commitedvalue',
                    'commitedexpenditurehistory',
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
        Schema::dropIfExists('despesa_orcamentaria');
    }
};
