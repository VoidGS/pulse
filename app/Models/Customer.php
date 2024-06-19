<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model {
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'cpf',
        'birthdate',
        'phone',
        'email',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    public function guardians(): BelongsToMany {
        return $this->belongsToMany(Guardian::class)->withTimestamps();
    }

    public function discounts(): HasMany {
        return $this->hasMany(CustomerDiscount::class);
    }
}
