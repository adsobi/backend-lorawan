<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Downlink extends Model
{
    protected $fillable = [
        'gateway',
        'end_node_id',
        'data',
        'freq',
        'modu',
        'datr',
        'codr',
        'tmst',
    ];
}
