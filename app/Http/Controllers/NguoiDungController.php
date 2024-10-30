<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung; 

class NguoiDungController extends Controller
{
    public function getNguoiDung(Request $request)
    {
        // Kiểm tra phương thức HTTP
        if ($request->isMethod('post')) {
            // Kiểm tra xem MaND có trong request không
            if ($request->has('mand')) {
                $mand = $request->input('mand');

                // Tìm người dùng theo MaND
                $nguoiDung = NguoiDung::where('MaND', $mand)->first();

                if ($nguoiDung) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $nguoiDung
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy thông tin người dùng'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thiếu thông tin MaND'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Yêu cầu không hợp lệ, vui lòng sử dụng phương thức POST'
            ], 405);
        }
    }
}
