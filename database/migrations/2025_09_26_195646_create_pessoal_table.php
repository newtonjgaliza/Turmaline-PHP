<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pessoal')) {
            // Cria a tabela se não existir
            Schema::create('pessoal', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                $table->boolean('emploeyeename')->nullable();
                $table->integer('points_emploeyeename')->nullable();

                $table->boolean('identificationnumber')->nullable();
                $table->integer('points_identificationnumber')->nullable();

                $table->boolean('employmentcontrattype')->nullable();
                $table->integer('points_employmentcontrattype')->nullable();

                $table->boolean('employeeposition')->nullable();
                $table->integer('points_employeeposition')->nullable();

                $table->boolean('employeesalary')->nullable();
                $table->integer('points_employeesalary')->nullable();

                $table->unsignedBigInteger('municipio_id')->nullable(); // FK
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, adiciona colunas que faltarem
            Schema::table('pessoal', function (Blueprint $table) {
                $columns = Schema::getColumnListing('pessoal');

                $campos = [
                    'emploeyeename',
                    'identificationnumber',
                    'employmentcontrattype',
                    'employeeposition',
                    'employeesalary',
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
        Schema::dropIfExists('pessoal');
    }
};
