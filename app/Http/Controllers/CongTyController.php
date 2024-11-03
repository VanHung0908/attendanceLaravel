<?php

namespace App\Http\Controllers;

use App\Models\CongTy;
use Illuminate\Http\Request;

class CongTyController extends Controller
{
    public function getCompany()
    {
        // Lấy danh sách công ty
        $congTy = CongTy::all();
        return response()->json($congTy);
    }

    public function storeCompany(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'tenCongTy' => 'required|string|max:255',
            'diaDiem' => 'required|string|max:255',
            'soDienThoai' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'nguoiDaiDien' => 'required|string|max:255',
            'linhVucKinhDoanh' => 'required|string|max:255',
            'trangThai' => 'required|string|max:255',
        ]);

        // Tạo công ty mới
        $congTy = CongTy::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $congTy,
        ]);
    }

    public function getCompanyById($maCongTy)
    {
        // Tìm công ty theo mã
        $company = CongTy::where('maCongTy', $maCongTy)->first();

        // Kiểm tra xem công ty có tồn tại không
        if (!$company) {
            return response()->json(['message' => 'Công ty không tồn tại'], 404);
        }

        // Trả về thông tin công ty
        return response()->json($company, 200);
    }

    public function update(Request $request, $maCongTy)
    {
        // Tìm công ty theo mã
        $company = CongTy::where('maCongTy', $maCongTy)->first();

        // Kiểm tra xem công ty có tồn tại không
        if (!$company) {
            return response()->json(['message' => 'Công ty không tồn tại'], 404);
        }

         // Xác thực dữ liệu đầu vào với các trường có thể được gửi
        $validatedData = $request->validate([
            'tenCongTy' => 'nullable|string|max:255',
            'diaChi' => 'nullable|string|max:255',
            'soDienThoai' => 'nullable|string|max:15',
        ]);

        // Cập nhật thông tin công ty
        $company->update(array_filter($validatedData));

        return response()->json([
            'message' => 'Cập nhật công ty thành công',
            'company' => $company
        ], 200);
        
        // Cập nhật thông tin công ty mà không cần xác thực
        // Lấy tất cả dữ liệu từ yêu cầu
        // $data = $request->all();

        // // Cập nhật thông tin công ty
        // $company->update($data);

        // return response()->json([
        //     'message' => 'Cập nhật công ty thành công',
        //     'company' => $company
        // ], 200);
    }

    public function deleteCompany($maCongTy)
    {
        try {
            // Tìm công ty theo mã
            $company = CongTy::where('maCongTy', $maCongTy)->first();
    
            // Kiểm tra nếu công ty tồn tại
            if (!$company) {
                return response()->json([
                    'message' => 'Công ty không tồn tại',
                ], 404);
            }
    
            // Xóa công ty
            $company->delete();
    
            // Trả về phản hồi thành công
            return response()->json([
                'message' => 'Công ty đã được xóa thành công',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra trong quá trình xóa công ty.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}

