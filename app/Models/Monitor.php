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
        'kode_lokasi',
        'ip_address',
        'status',
        'latency',
        'parent_id',
        'history',
    ];

    protected $casts = [
        'history' => 'array',
    ];

    /**
     * Relasi ke parent device
     */
    public function parent()
    {
        return $this->belongsTo(Monitor::class, 'parent_id');
    }

    /**
     * Relasi ke children devices
     */
    public function children()
    {
        return $this->hasMany(Monitor::class, 'parent_id');
    }
}