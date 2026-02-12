<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Tambahkan kolom sort_order setelah parent_id
            $table->integer('sort_order')->default(1)->after('parent_id');
        });
    }
};
