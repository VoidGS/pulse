<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model {
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'team_id',
        'user_id',
        'active'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
