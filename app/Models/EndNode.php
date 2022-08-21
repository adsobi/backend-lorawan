<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EndNode extends Model
{
    protected $fillable = [
        'app_id',
        'dev_addr',
        'name',
        'nwk_s_key',
        'app_s_key',
        'dev_eui',
        'join_eui',
     ];
     public function app():BelongsTo
     {
        return $this->belongsTo(App::class);
     }
}
