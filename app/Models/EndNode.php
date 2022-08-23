<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EndNode extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'dev_addr',
        'name',
        'description',
        'count_to_response',
        'nwk_s_key',
        'app_s_key',
        'dev_eui',
        'join_eui',
    ];

    public function lastActivity(): Attribute
    {
        return new Attribute(fn() => $this
                ->historicalData()
                ->latest()
                ->first());
    }
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function historicalData(): HasMany
    {
        return $this->hasMany(HistoricalData::class);
    }
}
