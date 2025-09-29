<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('despesa_extraorcamentaria')) {
            // Cria a tabela se não existir
            Schema::create('despesa_extraorcamentaria', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY
                $table->boolean('paymentamount')->nullable();
                $table->boolean('managementunitname')->nullable();
                $table->boolean('managementunitid')->nullable();
                $table->boolean('extrabudgetexpenditureid')->nullable();
                $table->boolean('extrabudgetexpenditurenomenclature')->nullable();
                $table->boolean('movedate')->nullable();
                $table->boolean('extrabudgetexpenditure')->nullable();
                $table->boolean('tabid')->nullable();
                $table->boolean('tabdate')->nullable();
                $table->boolean('creditorname')->nullable();
                $table->boolean('identificationnumber')->nullable();
                $table->boolean('tabhistory')->nullable();
                $table->unsignedBigInteger('municipio_id')->nullable();
                $table->timestamps();

                // Define a chave estrangeira
                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Atualiza colunas se a tabela já existir
            Schema::table('despesa_extraorcamentaria', function (Blueprint $table) {
                $columns = Schema::getColumnListing('despesa_extraorcamentaria');

                $campos = [
                    'paymentamount',
                    'managementunitname',
                    'managementunitid',
                    'extrabudgetexpenditureid',
                    'extrabudgetexpenditurenomenclature',
                    'movedate',
                    'extrabudgetexpenditure',
                    'tabid',
                    'tabdate',
                    'creditorname',
                    'identificationnumber',
                    'tabhistory',
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
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('despesa_extraorcamentaria');
    }
};
