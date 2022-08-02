<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Downlink extends Model
{
    use HasFactory;
    protected $fillable = [
        'gateway',
        'data',
        'freq',
        'modu',
        'datr',
        'codr',
        'tmst',
    ];
}
