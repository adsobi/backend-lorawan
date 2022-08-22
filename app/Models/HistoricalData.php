<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricalData extends Model
{
    use HasFactory;
    public const TYPES = ['Uplink', 'Downlink', 'JoinAccept', 'JoinRequest'];

    protected $fillable = [
        'end_node_id',
        'gateway_id',
        'data',
        'snr',
        'rssi',
        'type',
     ];

     public function endNode():BelongsTo
     {
        return $this->belongsTo(EndNode::class);
     }

     public function gateway():BelongsTo
     {
        return $this->belongsTo(Gateway::class);
     }
}
