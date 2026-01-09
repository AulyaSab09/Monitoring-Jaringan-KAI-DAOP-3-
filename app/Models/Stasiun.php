<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stasiun extends Model
{
    // Nama tabel di database
    protected $table = 'stasiun';

    // Kolom yang boleh diisi
    protected $fillable = ['nama_stasiun', 'kode_stasiun'];
}