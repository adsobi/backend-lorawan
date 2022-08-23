<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gateway extends Model
{
    use HasFactory;
    protected $fillable = [
        'gateway_eui',
        'name',
        'description',
     ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->user_id = auth()->user()->id ;
        });
    }
     public function lastActivity(): Attribute
     {
         return new Attribute(fn() => $this
            ->historicalData()
            ->latest()
            ->first());
     }
     public function historicalData(): HasMany
     {
         return $this->hasMany(HistoricalData::class);
     }
}
