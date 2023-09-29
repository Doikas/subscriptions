<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class CustomerMapping extends Model
{
    use HasFactory, AsSource;
    protected $table = 'customer_mapping';
    protected $fillable = [
        'old_customer_id',
        'new_customer_id',
    ];
}
