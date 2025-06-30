<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementHistory extends Model
{
    protected $fillable = [
        'x', 'y', 'direction', 'commands', 'obstacles', 'result_status', 'result_x', 'result_y'
    ];

    protected $casts = [
        'obstacles' => 'array',
    ];
} 