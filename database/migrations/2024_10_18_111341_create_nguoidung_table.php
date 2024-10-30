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
        Schema::create('nguoidung', function (Blueprint $table) {
            $table->id('maND');
            $table->string('hoTen');
            $table->string('diaChi');
            $table->date('ngaySinh');
            $table->string('gioiTinh');
            $table->string('email', 191)->unique();
            $table->string('SDT');
            $table->date('ngayBatDau');
            $table->date('ngayKetThuc')->nullable();
            $table->string('trangThaiKhuonMat')->nullable();
            $table->foreignId('maVaiTro')->constrained('vaitro');
            $table->foreignId('maCongTy')->constrained('congty');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoidung');
    }
};
