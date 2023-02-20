<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Service extends Model
{
    use HasFactory, AsSource;
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
