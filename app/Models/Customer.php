<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource, Filterable;
    public function services(){
        return $this->hasMany(Services::class);
    }
    public function domains(){
        return $this->hasMany(Domain::class);
    }
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'website',
        'phone',
        'notes',

    ];
    protected $allowedFilters = [
        'id',
        'firstname',
        'lastname',
        'email',
        'website',
    ];
    protected $allowedSorts = [
        'id',
        'firstname',
        'lastname',
        'email',
        'website',
        'updated_at',
        'created_at',
    ];

}
