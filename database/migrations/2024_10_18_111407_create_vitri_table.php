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
        Schema::create('vitri', function (Blueprint $table) {
            $table->id('maViTri');
            $table->string('kinhDo');
            $table->string('viDo');
            $table->string('trangThai');
            $table->foreignId('maCongTy')->constrained('congty')->onDelete('cascade'); 
            $table->foreignId('maND')->constrained('nguoidung')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitri');
    }
};
