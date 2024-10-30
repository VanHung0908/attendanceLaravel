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
        Schema::create('chamcong', function (Blueprint $table) {
            $table->id('maChamCong');
            $table->time('gioCheckin');
            $table->time('gioCheckout')->nullable();
            $table->date('ngay');
            $table->integer('tongGioLam')->nullable();
            $table->foreignId('maND')->constrained('nguoidung');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chamcong');
    }
};