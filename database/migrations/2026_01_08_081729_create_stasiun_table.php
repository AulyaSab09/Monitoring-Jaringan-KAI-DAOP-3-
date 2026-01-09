<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stasiun', function (Blueprint $table) {
            $table->id();
            $table->string('nama_stasiun'); // Nama lengkap stasiun
            $table->string('kode_stasiun'); // Singkatan/Kode stasiun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stasiun');
    }
};