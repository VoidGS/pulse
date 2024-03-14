<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
    ];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }
}
