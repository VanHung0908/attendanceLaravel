<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChamCong;
use Carbon\Carbon;

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
}
