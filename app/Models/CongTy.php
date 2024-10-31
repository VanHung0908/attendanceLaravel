<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongTy extends Model
{
    use HasFactory;

    protected $table = 'congty'; // Tên bảng

    protected $primaryKey = 'maCongTy'; // Khóa chính của bảng

    protected $fillable = [
        'tenCongTy',
        'diaDiem',
        'soDienThoai',
        'email',
        'nguoiDaiDien',
        'linhVucKinhDoanh',
        'trangThai',
    ]; // Các thuộc tính có thể gán giá trị hàng loạt

    // Phương thức thiết lập quan hệ với model NguoiDung
    public function nguoiDungs()
    {
        return $this->hasMany(NguoiDung::class, 'maCongTy', 'maCongTy');
    }
}
