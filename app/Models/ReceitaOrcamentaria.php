<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceitaOrcamentaria extends Model
{
    protected $table = 'receita_orcamentaria';

    protected $fillable = [
        'managementunitname', 'points_managementunitname',
        'managementunitid', 'points_managementunitid',
        'budgetrevenuesource', 'points_budgetrevenuesource',
        'budgetrevenuedescription', 'points_budgetrevenuedescription',
        'predictedamount', 'points_predictedamount',
        'collectionamount', 'points_collectionamount',
        'municipio_id',
    ];
}
