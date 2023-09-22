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
        'phone',
        'pronunciation',
        'notes',
        'fullname',

    ];
    protected $allowedFilters = [
        'id',
        'firstname',
        'lastname',
        'email',
        'pronunciation',
        'fullname',
    ];
    protected $allowedSorts = [
        'id',
        'firstname',
        'lastname',
        'email',
        'pronunciation',
        'phone',
        'fullname',
        'updated_at',
        'created_at',
    ];
    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }
    

}
