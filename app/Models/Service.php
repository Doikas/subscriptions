<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Service extends Model
{
    use HasFactory, AsSource, Filterable;
    public function subscription(){
        return $this->hasMany(Subscription::class);
    }
    protected $fillable = [
        
        'name',
        'slug',
        'description',
        'expiration',
    ];
    protected $allowedFilters = [
        'id',
        'name',
        'slug',
        'description',
        'expiration',
    ];
    protected $allowedSorts = [
        'id',
        'name',
        'slug',
        'description',
        'expiration',
    ]; 
}
