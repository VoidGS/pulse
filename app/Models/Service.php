<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'team_id',
        'user_id'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
