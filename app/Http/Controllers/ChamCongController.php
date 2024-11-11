<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChamCong;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChamCongExport;

class ChamCongController extends Controller
{
    public function chamCong(Request $request)
    {
        $userId = $request->input('maND');
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');

        try {
            $chamCong = ChamCong::where('maND', $userId)
                                ->where('ngay', $currentDate)
                                ->first();

            if (!$chamCong) {
                $chamCong = ChamCong::create([
                    'maND' => $userId,
                    'ngay' => $currentDate,
                    'gioCheckin' => $currentTime,
                ]);

                return response()->json(['message' => 'Check-in thành công!', 'data' => $chamCong], 200);
            } elseif (!$chamCong->gioCheckout) {
                $chamCong->update(['gioCheckout' => $currentTime]);

                $gioCheckin = Carbon::parse($chamCong->gioCheckin);
                $gioCheckout = Carbon::parse($currentTime);

                // Tính tổng phút làm
                $tongPhutLam = $gioCheckout->diffInMinutes($gioCheckin);

                // Tính tổng giờ làm (chuyển sang dạng float)
                $tongGioLam = $tongPhutLam / 60;

                // Tính số công
                $cong = 0; // Mặc định là 0
                if ($tongGioLam >= 8) {
                    $cong = 1.0;
                } elseif ($tongGioLam >= 4) {
                    $cong = 0.5;
                }

                // Cập nhật tổng giờ làm và số công vào database
                $chamCong->update(['tongGioLam' => $tongGioLam, 'cong' => $cong]);

                return response()->json(['message' => 'Check-out thành công!', 'data' => $chamCong], 200);
            } else {
                return response()->json(['message' => 'Bạn đã check-in và check-out cho ngày hôm nay rồi.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi khi chấm công.', 'error' => $e->getMessage()], 500);
        }
    }

    public function thongKeCong(Request $request)
    {
        $userId = $request->input('maND');
        $currentMonth = Carbon::now()->format('Y-m');

        try {
            $chamCongRecords = ChamCong::where('maND', $userId)
                                        ->where('ngay', 'like', "$currentMonth%")
                                        ->get();

            $tongCong = 0.0;
            $ngayDiTre = [];
            $ngayVeSom = [];

            foreach ($chamCongRecords as $record) {
                if ($record->cong) {
                    $tongCong += (float) $record->cong; 
                }

                if ($record->gioCheckin && Carbon::parse($record->gioCheckin)->greaterThan('08:00:00')) {
                    $ngayDiTre[] = [
                        'ngay' => $record->ngay,
                        'gioCheckin' => $record->gioCheckin,
                    ];
                }

                if ($record->gioCheckout && Carbon::parse($record->gioCheckout)->lessThan('17:00:00')) {
                    $ngayVeSom[] = [
                        'ngay' => $record->ngay,
                        'gioCheckout' => $record->gioCheckout,
                    ];
                }
            }

            return response()->json([
                'tongCong' => $tongCong,
                'soNgayDiTre' => count($ngayDiTre),
                'chiTietDiTre' => $ngayDiTre,
                'soNgayVeSom' => count($ngayVeSom),
                'chiTietVeSom' => $ngayVeSom,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi khi lấy thông tin chấm công.', 'error' => $e->getMessage()], 500);
        }
    }

    public function chitietChamCong(Request $request)
    {
        $userId = $request->input('maND');
        $currentDate = Carbon::now()->format('Y-m-d');

        try {
            $chamCong = ChamCong::where('maND', $userId)
                                ->where('ngay', $currentDate)
                                ->first();

            if ($chamCong) {
                return response()->json(['message' => 'Lấy chi tiết chấm công thành công!', 'data' => $chamCong], 200);
            } else {
                return response()->json(['message' => 'Không tìm thấy bản ghi chấm công cho ngày hôm nay.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi khi lấy thông tin chấm công.', 'error' => $e->getMessage()], 500);
        }
    }

    public function baoCaoTheoThoiGian(Request $request)
    {
        $userId = $request->input('maND');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        try {
            $chamCongRecords = ChamCong::where('maND', $userId)
                                    ->whereBetween('ngay', [$startDate, $endDate])
                                    ->get();

            if ($chamCongRecords->isEmpty()) {
                return response()->json(['message' => 'Không tìm thấy bản ghi chấm công trong khoảng thời gian đã cho.'], 404);
            }

            $tongCong = 0.0;
            $ngayDiTre = [];
            $ngayVeSom = [];

            foreach ($chamCongRecords as $record) {
                if ($record->gioCheckin && Carbon::parse($record->gioCheckin)->greaterThan('08:00:00')) {
                    $ngayDiTre[] = [
                        'ngay' => $record->ngay,
                        'gioCheckin' => $record->gioCheckin,
                    ];
                }

                if ($record->gioCheckout && Carbon::parse($record->gioCheckout)->lessThan('17:00:00')) {
                    $ngayVeSom[] = [
                        'ngay' => $record->ngay,
                        'gioCheckout' => $record->gioCheckout,
                    ];
                }

                // Cập nhật tổng công
                $tongCong += (float) $record->cong; // Chuyển đổi sang float
            }

            return response()->json([
                'tongCong' => $tongCong,
                'soNgayDiTre' => count($ngayDiTre),
                'chiTietDiTre' => $ngayDiTre,
                'soNgayVeSom' => count($ngayVeSom),
                'chiTietVeSom' => $ngayVeSom,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi khi lấy báo cáo chấm công.', 'error' => $e->getMessage()], 500);
        }
    }
    public function getChamCongByPeriod(Request $request)
    {
        $validated = $request->validate([
            'maND' => 'required|integer',
            'period' => 'required|string', // 1 Tuần, 2 Tuần, 1 Tháng, Tùy Chọn
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
    
        $maND = $validated['maND'];
        $period = $validated['period'];
    
        $startDate = Carbon::today(); // Ngày hiện tại
        $endDate = $startDate; // Ngày kết thúc mặc định là ngày hôm nay
    
        // Xác định thời gian bắt đầu và kết thúc dựa trên period
        if ($period == "1 Tuần") {
            $startDate = $startDate->copy()->subDays(7); // Ngày bắt đầu là 7 ngày trước
        } elseif ($period == "2 Tuần") {
            $startDate = $startDate->copy()->subDays(14); // Ngày bắt đầu là 14 ngày trước
        } elseif ($period == "1 Tháng") {
            $startDate = $startDate->copy()->startOfMonth(); // Ngày bắt đầu là đầu tháng
        } elseif ($period == "Tùy Chọn") {
            if (!$request->start_date || !$request->end_date) {
                return response()->json(['error' => 'Start date and end date are required for custom period'], 400);
            }
            // Xử lý ngày bắt đầu và kết thúc cho tùy chọn
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
    
        // Lấy thông tin chấm công trong khoảng thời gian
        $attendance = ChamCong::where('maND', $maND)
            ->whereBetween(\DB::raw('DATE(ngay)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->get();
    
        // Xử lý thông tin chấm công và tính toán status cho từng ngày
        $attendanceData = $attendance->map(function ($entry) {
            $entry->status = 'Vắng mặt'; // Mặc định là vắng mặt
    
            if ($entry->gioCheckin && $entry->gioCheckout) {
                // Nếu có checkin và checkout, tính số công
                $entry->status = 'Có mặt';
            }
    
            if ($entry->gioCheckin && $entry->gioCheckin > '08:00:00') {
                $entry->status = 'Đi trễ';
            }
    
            if ($entry->gioCheckout && $entry->gioCheckout < '17:00:00') {
                $entry->status = 'Về sớm';
            }
    
            return $entry;
        });
    
        return response()->json($attendanceData);
    }
   

    public function export(Request $request)
    {
        // Kiểm tra tham số đầu vào
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $maND = $request->input('maND');
        $isSelectAll = $request->input('isSelectAll') === '1';
        $maCongTy = $request->input('maCongTy'); // Nhận thông tin mã công ty từ request
    
        // Kiểm tra nếu thiếu tham số quan trọng
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Thiếu thông tin ngày bắt đầu hoặc ngày kết thúc'], 400);
        }
    
        // Lấy dữ liệu từ bảng chấm công
        $query = ChamCong::whereBetween('ngay', [$startDate, $endDate]);
    
        // Nếu chọn nhân viên cụ thể
        if ($maND && !$isSelectAll) {
            $query->where('maND', $maND);
        }
    
        // Nếu chọn công ty cụ thể, lọc theo mã công ty
        if ($maCongTy) {
            $query->whereHas('nguoidung', function($query) use ($maCongTy) {
                $query->where('maCongTy', $maCongTy);
            });
        }
    
        // Lấy dữ liệu
        $data = $query->with('nguoidung')->get();
    
        // Kiểm tra nếu không có dữ liệu
        if ($data->isEmpty()) {
            return response()->json(['error' => 'Không có dữ liệu'], 404);
        }
    
        $date = \Carbon\Carbon::now()->format('Hisdmy'); 
    
        // Tạo tên file với ngày tháng năm
        $fileName = 'chamcong_report_' . $date . '.xlsx';
    
        // Tạo đối tượng xuất Excel
        $export = new ChamCongExport($data);
    
        // Lưu file vào đường dẫn tạm thời
        $pathToFile = storage_path('app/public/' . $fileName);
    
        // Xuất tệp Excel và lưu vào thư mục tạm
        Excel::store($export, 'public/' . $fileName);
    
        // Trả về tệp Excel để người dùng tải về
        return response()->download($pathToFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    


}
