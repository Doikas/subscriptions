<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class EmailLog extends Model
{
    protected $table = 'subscription_email_logs';
    use HasFactory, AsSource, Filterable;
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    protected $fillable = [
        'subscription_id',
        'subject',
        'content',
        'sent_successfully',
        'sent_at',
    ];
    protected $allowedFilters = [
        'id',
        'subscription_id',
        'customer.email',
        'customer.fullname',
        'subscription.domain',
        'service.name',
        'subject',
        'content',
        'sent_successfully',
        'sent_at',
    ];
    protected $allowedSorts = [
        'id',
        'subscription_id',
        'customer.email',
        'customer.fullname',
        'subscription.domain',
        'service.name',
        'subject',
        'content',
        'sent_successfully',
        'sent_at',
    ];
}
