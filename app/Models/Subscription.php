<?php

namespace App\Models;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use App\Models\Service;
use App\Models\Customer;

class Subscription extends Model
{
    use HasFactory, AsSource, Filterable;
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }
    protected $fillable = [
        'customer_id',
        'service_id',
        'domain',
        'price',
        'paid_status',
        'start_date',
        'expired_date',
        'notes',
    ];
    protected $allowedFilters = [
        'id',
        'customer_id',
        'service_id',
        'customer.email',
        'customer.lastname',
        'domain',
        'price',
        'paid_status',
        'start_date',
        'expired_date',
        'notes',
    ];
    protected $allowedSorts = [
        'id',
        'service.name',
        'service_id',
        'customer_id',
        'customer.fullname',
        'customer.email',
        'domain',
        'price',
        'paid_status',
        'start_date',
        'expired_date',
        'notes',
    ];
}
