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
        'zone',
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

    /**
     * Relasi ke incidents history
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Get the latest incident.
     */
    public function latestIncident()
    {
        return $this->hasOne(Incident::class)->latestOfMany('down_at');
    }

    /**
     * Scope untuk device Center
     */
    public function scopeCenter($query)
    {
        return $query->where('zone', 'center');
    }

    /**
     * Scope untuk device Lintas Utara
     */
    public function scopeLintasUtara($query)
    {
        return $query->where('zone', 'lintas utara');
    }

    /**
     * Scope untuk device Lintas Selatan
     */
    public function scopeLintasSelatan($query)
    {
        return $query->where('zone', 'lintas selatan');
    }
}