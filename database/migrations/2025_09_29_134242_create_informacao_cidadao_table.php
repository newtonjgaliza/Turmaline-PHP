<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('informacao_cidadao')) {
            Schema::create('informacao_cidadao', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                $table->boolean('tmsic')->nullable();
                $table->boolean('tmsearch')->nullable();
                $table->boolean('tmexecute')->nullable();

                $table->unsignedBigInteger('municipio_id'); // FK
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios') // tabela no plural
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            Schema::table('informacao_cidadao', function (Blueprint $table) {
                $columns = Schema::getColumnListing('informacao_cidadao');

                $campos = [
                    'tmsic',
                    'tmsearch',
                    'tmexecute',
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
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('informacao_cidadao');
    }
};
