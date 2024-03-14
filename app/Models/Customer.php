<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'gender',
        'birthdate',
    ];

    public function guardians(): HasMany {
        return $this->hasMany(CustomerGuardian::class);
    }

    public function discounts(): HasMany {
        return $this->hasMany(CustomerDiscount::class);
    }
}
