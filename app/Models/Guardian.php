<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Guardian extends Model {
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'cpf',
        'phone',
        'email',
        'birthdate',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    public function customer(): BelongsToMany {
        return $this->belongsToMany(Customer::class)->withTimestamps();
    }
}
