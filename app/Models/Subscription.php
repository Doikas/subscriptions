<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Subscription extends Model
{
    use HasFactory, AsSource, Filterable;
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function services(){
        return $this->belongsTo(Service::class);
    }
}
