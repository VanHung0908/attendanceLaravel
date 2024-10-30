<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;

class TaiKhoanController extends Controller
{
    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'tenDN' => 'required|string',
            'matKhau' => 'required|string',
        ]);

        // Tìm tài khoản theo tên đăng nhập
        $taiKhoan = TaiKhoan::where('tenDN', $request->tenDN)->first();

        // Kiểm tra xem tài khoản có tồn tại không
        if (!$taiKhoan || !Hash::check($request->matKhau, $taiKhoan->matKhau)) {
            return response()->json([
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ], 401);
        }

        // Trả về chỉ trường maND của tài khoản
        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'maND' => $taiKhoan->maND // Chỉ trả về maND
        ]);
    }

    public function getAllTaiKhoan()
    {
        // Lấy tất cả các tài khoản với trường 'tenDN'
        $dsTaiKhoan = TaiKhoan::select('tenDN')->get();

        // Trả về kết quả dưới dạng JSON
        return response()->json([
            'message' => 'Danh sách tên đăng nhập',
            'data' => $dsTaiKhoan
        ]);
    }
}
