<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource;
    public function services(){
        return $this->hasMany(Services::class);
    }
    public function hosting(){
        return $this->hasMany(Host::class);
    }

}
