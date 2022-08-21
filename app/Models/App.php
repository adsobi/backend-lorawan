<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    protected $fillable = [
       'key',
       'name',
    ];

    public function endNodes(): HasMany
    {
        return $this->hasMany(EndNode::class);
    }
}
