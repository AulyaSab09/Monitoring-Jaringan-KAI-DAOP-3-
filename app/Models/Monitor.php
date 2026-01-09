<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    // Pastikan parent_id masuk ke fillable agar bisa diisi
    protected $fillable = [
        'name', 
        'ip_address', 
        'type', 
        'location', 
        'status', 
        'latency', 
        'history', 
        'parent_id' // <--- PENTING DITAMBAHKAN
    ];

    // Casting history agar otomatis jadi Array/JSON saat diambil
    protected $casts = [
        'history' => 'array',
    ];

    /**
     * Relasi ke Anak-anaknya (Turunan)
     * Satu induk bisa punya banyak anak (hasMany)
     */
    public function children()
    {
        return $this->hasMany(Monitor::class, 'parent_id');
    }

    /**
     * Relasi ke Induknya (Bapak)
     * Satu anak dimiliki oleh satu induk (belongsTo)
     */
    public function parent()
    {
        return $this->belongsTo(Monitor::class, 'parent_id');
    }

    /**
     * Cek apakah ada anak yang DOWN (untuk indikator warning di parent)
     */
    public function hasDownChild(): bool
    {
        return $this->children()->where('status', 'Disconnected')->exists();
    }

    /**
     * Get child status summary (e.g., ['up' => 3, 'total' => 5])
     */
    public function getChildStatusSummary(): array
    {
        $total = $this->children->count();
        $up = $this->children->where('status', 'Connected')->count();
        return ['up' => $up, 'total' => $total];
    }
}