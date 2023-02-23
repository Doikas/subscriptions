<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource, Filterable;
    public function subscription(){
        return $this->hasMany(Subscription::class);
    }
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'website',
        'phone',
        'pronunciation',
        'notes',

    ];
    protected $allowedFilters = [
        'id',
        'firstname',
        'lastname',
        'email',
        'pronunciation',
        'website',
    ];
    protected $allowedSorts = [
        'id',
        'firstname',
        'lastname',
        'email',
        'pronunciation',
        'phone',
        'website',
        'updated_at',
        'created_at',
    ];

}
