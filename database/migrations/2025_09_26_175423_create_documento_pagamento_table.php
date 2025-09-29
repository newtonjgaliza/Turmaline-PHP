<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('documento_pagamento')) {
            // Cria a tabela se não existir
            Schema::create('documento_pagamento', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY
                $table->boolean('managementunitname')->nullable();
                $table->boolean('managementunitid')->nullable();
                $table->boolean('bankoperationid')->nullable();
                $table->boolean('bankaccountnumber')->nullable();
                $table->boolean('paymentdate')->nullable();
                $table->boolean('identificationnumber')->nullable();
                $table->boolean('creditorname')->nullable();
                $table->boolean('paymentamount')->nullable();
                $table->boolean('fundingsource')->nullable();
                $table->boolean('paymenthistory')->nullable();
                $table->unsignedBigInteger('municipio_id')->nullable(); // FK
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios') // tabela correta no plural
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, atualiza colunas se necessário
            Schema::table('documento_pagamento', function (Blueprint $table) {
                $columns = Schema::getColumnListing('documento_pagamento');

                $campos = [
                    'managementunitname',
                    'managementunitid',
                    'bankoperationid',
                    'bankaccountnumber',
                    'paymentdate',
                    'identificationnumber',
                    'creditorname',
                    'paymentamount',
                    'fundingsource',
                    'paymenthistory',
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
        Schema::dropIfExists('documento_pagamento');
    }
};
