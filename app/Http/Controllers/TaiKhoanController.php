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
            'maND' => $taiKhoan->maND 
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
    public function changePassword(Request $request)
{
    // Xác thực dữ liệu đầu vào
    $request->validate([
        'maND' => 'required|integer',
        'matKhauCu' => 'required|string',
        'matKhauMoi' => 'required|string|min:6|confirmed', // thêm 'confirmed' để xác nhận mật khẩu mới
    ]);

    // Tìm tài khoản theo mã người dùng
    $taiKhoan = TaiKhoan::find($request->maND);

    // Kiểm tra xem tài khoản có tồn tại không và mật khẩu cũ có đúng không
    if (!$taiKhoan || !Hash::check($request->matKhauCu, $taiKhoan->matKhau)) {
        return response()->json([
            'message' => 'Mật khẩu cũ không đúng hoặc tài khoản không tồn tại.'
        ], 401);
    }

    // Cập nhật mật khẩu mới
    $taiKhoan->matKhau = Hash::make($request->matKhauMoi);
    $taiKhoan->save();

    return response()->json([
        'message' => 'Đổi mật khẩu thành công.'
    ]);
}

}
