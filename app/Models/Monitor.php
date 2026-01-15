<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'location',
        'ip_address',
        'status',
        'latency',
    ];
    protected $guarded = [];
    protected $casts = [
        'history' => 'array',
    ];
}