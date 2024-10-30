<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaiKhoanController;

// Route::get('/taikhoan', [TaiKhoanController::class, 'getAllTaiKhoan']);
Route::post('/login', [TaiKhoanController::class, 'login']);
Route::post('/nguoidung', [NguoiDungController::class, 'getNguoiDung']);