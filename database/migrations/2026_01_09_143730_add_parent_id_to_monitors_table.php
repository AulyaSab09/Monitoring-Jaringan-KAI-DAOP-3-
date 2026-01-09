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
            // Kolom parent_id untuk menyimpan ID induk
            // nullable() artinya boleh kosong (kalau dia Induk)
            // after('id') agar posisinya rapi di sebelah id
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');

            // Membuat relasi (Foreign Key)
            // Jika Induk dihapus, semua Anak-nya ikut terhapus (cascade)
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('monitors')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Hapus foreign key dulu baru kolomnya agar tidak error
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};