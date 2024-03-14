<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerGuardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'gender',
        'customer_id',
        'birthdate',
    ];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }
}
