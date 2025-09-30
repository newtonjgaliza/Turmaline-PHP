<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('receita_orcamentaria')) {
            // Cria a tabela se não existir
            Schema::create('receita_orcamentaria', function (Blueprint $table) {
                $table->id(); // SERIAL PRIMARY KEY

                // Campos + pontos
                $table->boolean('managementunitname')->nullable();
                #$table->integer('points_managementunitname')->nullable();

                $table->boolean('managementunitid')->nullable();
                #$table->integer('points_managementunitid')->nullable();

                $table->boolean('budgetrevenuesource')->nullable();
                #$table->integer('points_budgetrevenuesource')->nullable();

                $table->boolean('budgetrevenuedescription')->nullable();
                #table->integer('points_budgetrevenuedescription')->nullable();

                $table->boolean('predictedamount')->nullable();
                #$table->integer('points_predictedamount')->nullable();

                $table->boolean('collectionamount')->nullable();
                #$table->integer('points_collectionamount')->nullable();

                // Relacionamento
                $table->unsignedBigInteger('municipio_id');
                $table->timestamps();

                $table->foreign('municipio_id')
                      ->references('id')
                      ->on('municipios')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } else {
            // Se já existir, atualiza colunas se necessário
            Schema::table('receita_orcamentaria', function (Blueprint $table) {
                $columns = Schema::getColumnListing('receita_orcamentaria');

                // Management Unit Name
                if (!in_array('managementunitname', $columns)) {
                    $table->boolean('managementunitname')->nullable();
                }
                if (!in_array('points_managementunitname', $columns)) {
                    $table->integer('points_managementunitname')->nullable();
                }

                // Management Unit ID
                if (!in_array('managementunitid', $columns)) {
                    $table->boolean('managementunitid')->nullable();
                }
                if (!in_array('points_managementunitid', $columns)) {
                    $table->integer('points_managementunitid')->nullable();
                }

                // Budget Revenue Source
                if (!in_array('budgetrevenuesource', $columns)) {
                    $table->boolean('budgetrevenuesource')->nullable();
                }
                if (!in_array('points_budgetrevenuesource', $columns)) {
                    $table->integer('points_budgetrevenuesource')->nullable();
                }

                // Budget Revenue Description
                if (!in_array('budgetrevenuedescription', $columns)) {
                    $table->boolean('budgetrevenuedescription')->nullable();
                }
                if (!in_array('points_budgetrevenuedescription', $columns)) {
                    $table->integer('points_budgetrevenuedescription')->nullable();
                }

                // Predicted Amount
                if (!in_array('predictedamount', $columns)) {
                    $table->boolean('predictedamount')->nullable();
                }
                if (!in_array('points_predictedamount', $columns)) {
                    $table->integer('points_predictedamount')->nullable();
                }

                // Collection Amount
                if (!in_array('collectionamount', $columns)) {
                    $table->boolean('collectionamount')->nullable();
                }
                if (!in_array('points_collectionamount', $columns)) {
                    $table->integer('points_collectionamount')->nullable();
                }

                // FK municipio_id
                if (!in_array('municipio_id', $columns)) {
                    $table->unsignedBigInteger('municipio_id');
                    $table->foreign('municipio_id')
                          ->references('id')
                          ->on('municipios')
                          ->onDelete('cascade')
                          ->onUpdate('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('receita_orcamentaria');
    }
};
