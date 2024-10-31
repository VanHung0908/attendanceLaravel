<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Thêm admin
        $adminId = DB::table('nguoidung')->insertGetId([
            'hoTen' => 'Phan Thị Bảo Trân',
            'diaChi' => 'Hồ Chí Minh',
            'ngaySinh' => '1990-01-01',
            'gioiTinh' => 'Nữ',
            'email' => 'BaoTran@gmail.com',
            'SDT' => '0123456789',
            'ngayBatDau' => '2024-10-10',
            'trangThaiKhuonMat' => 0, // Đặt thành 0
            'IMG' => null, // Giá trị IMG có thể NULL
            'maVaiTro' => 1, // Admin
            'maCongTy' => 1, // Công ty 1
        ]);

        DB::table('taikhoan')->insert([
            'tenDN' => '01001',
            'matKhau' => bcrypt('123'), // Mã hóa mật khẩu
            'maND' => $adminId
        ]);

        // Thêm quản lý
        $managerId = DB::table('nguoidung')->insertGetId([
            'hoTen' => 'Nguyễn Thanh Phú',
            'diaChi' => 'Hồ Chí Minh',
            'ngaySinh' => '2003-04-26',
            'gioiTinh' => 'Nam',
            'email' => 'thanhphu@gmail.com',
            'SDT' => '0987654321',
            'ngayBatDau' => '2024-10-10',
            'trangThaiKhuonMat' => 0, // Đặt thành 0
            'IMG' => null, // Giá trị IMG có thể NULL
            'maVaiTro' => 2, // Quản lý
            'maCongTy' => 1, // Công ty 1
        ]);

        DB::table('taikhoan')->insert([
            'tenDN' => '02002',
            'matKhau' => bcrypt('123'), // Mã hóa mật khẩu
            'maND' => $managerId
        ]);

        // Thêm nhân viên
        $employeeId = DB::table('nguoidung')->insertGetId([
            'hoTen' => 'Lâm Văn Hưng',
            'diaChi' => 'Hồ Chí Minh',
            'ngaySinh' => '2003-08-09',
            'gioiTinh' => 'Nam',
            'email' => 'vanhung@gmail.com',
            'SDT' => '0353627994',
            'ngayBatDau' => '2024-10-10',
            'trangThaiKhuonMat' => 0, // Đặt thành 0
            'IMG' => null, // Giá trị IMG có thể NULL
            'maVaiTro' => 3, // Nhân viên
            'maCongTy' => 1, // Công ty 1
        ]);

        DB::table('taikhoan')->insert([
            'tenDN' => '03003',
            'matKhau' => bcrypt('123'), // Mã hóa mật khẩu
            'maND' => $employeeId
        ]);
    }
}
