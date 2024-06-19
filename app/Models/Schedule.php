<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Schedule extends Model {
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'service_id',
        'start_date',
        'end_date',
        'recurrence_id',
        'event_id',
        'status',
        'active',
    ];

    protected $casts = [
        'status' => ScheduleStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }
}
