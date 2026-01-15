<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Add parent_id for tree hierarchy (nullable for root devices)
            if (!Schema::hasColumn('monitors', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('location');
                $table->foreign('parent_id')->references('id')->on('monitors')->onDelete('cascade');
            }
            
            // Add kode_lokasi for location code
            if (!Schema::hasColumn('monitors', 'kode_lokasi')) {
                $table->string('kode_lokasi')->nullable()->after('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            if (Schema::hasColumn('monitors', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('monitors', 'kode_lokasi')) {
                $table->dropColumn('kode_lokasi');
            }
        });
    }
};

