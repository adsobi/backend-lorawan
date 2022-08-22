<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    use HasFactory;
    protected $fillable = [
       'key',
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
    public function endNodes(): HasMany
    {
        return $this->hasMany(EndNode::class);
    }
}
