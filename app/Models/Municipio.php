<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;

class Municipio extends Model
{
    use AsSource, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'municipios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome'
    ];

    /**
     * The attributes that can be filtered.
     *
     * @var array
     */
    protected $allowedFilters = [
        'nome' => Like::class,
        'created_at' => WhereDateStartEnd::class,
        'updated_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes that can be sorted.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'nome',
        'created_at',
        'updated_at'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}