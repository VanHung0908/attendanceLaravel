<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung; 
use Illuminate\Support\Facades\Hash;

class NguoiDungController extends Controller
{
    public function getNguoiDung(Request $request)
    {
        // Kiểm tra phương thức HTTP
        if ($request->isMethod('post')) {
            // Kiểm tra xem maND có trong request không
            if ($request->has('maND')) {
                $maND = $request->input('maND');
    
                // Tìm người dùng theo maND
                try {
                    $nguoiDung = NguoiDung::where('maND', $maND)->first();
    
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
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thiếu thông tin maND'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Yêu cầu không hợp lệ, vui lòng sử dụng phương thức POST'
            ], 405);
        }
    }

    public function updateFaceStatus(Request $request)
    {
        $request->validate([
            'maND' => 'required|integer', // Chỉnh sửa tên tham số để phù hợp với bảng
            'trangThaiKhuonMat' => 'required|boolean',
        ]);

        // Tìm người dùng theo maND
        $nguoiDung = NguoiDung::where('maND', $request->maND)->first(); // Sử dụng maND

        if ($nguoiDung) {
            $nguoiDung->trangThaiKhuonMat = $request->trangThaiKhuonMat; // Cập nhật trạng thái
            $nguoiDung->save();

            return response()->json(['message' => 'Cập nhật trạng thái thành công'], 200);
        }

        return response()->json(['message' => 'Người dùng không tìm thấy'], 404);
    }
    public function getNguoiDungWithCompany(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('maND')) {
                $maND = $request->input('maND');

                try {
                    $nguoiDung = NguoiDung::with('congTy')->where('maND', $maND)->first();

                    if ($nguoiDung) {
                        return response()->json([
                            'status' => 'success',
                            'data' => [
                                'nguoiDung' => $nguoiDung,
                                'congTy' => $nguoiDung->congTy, // Lấy thông tin công ty liên quan
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Không tìm thấy thông tin người dùng'
                        ], 404);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thiếu thông tin maND'
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
