<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address'); // Untuk kolom IP Address
            $table->string('status');     // Untuk: Connected, Disconnected, Unstable
            $table->integer('latency');   // Untuk: 12 ms (simpan angkanya saja)
            $table->timestamps();         // Untuk: Last Check (updated_at)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
    public function index()
    {
        $stasiuns = Stasiun::orderBy('nama_stasiun', 'asc')->get();
        return view('stasiun', compact('stasiuns')); // Sesuai nama file stasiun.blade.php
    }
};