<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('history')) {
            Schema::create('history', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY
                $table->integer('total_points'); // total de pontos
                $table->unsignedBigInteger('municipio_id'); // FK para municipios
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios') // tabela no plural
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            Schema::table('history', function (Blueprint $table) {
                $columns = Schema::getColumnListing('history');

                $campos = [
                    'total_points',
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
                            $table->integer($campo);
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
