<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Menambahkan index agar query pencarian IP dan status secepat kilat
            $table->index('ip_address');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['status']);
        });
    }
};