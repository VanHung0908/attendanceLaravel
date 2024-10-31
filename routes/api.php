<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\NguoiDungController;

// Route::get('/taikhoan', [TaiKhoanController::class, 'getAllTaiKhoan']);
Route::post('/login', [TaiKhoanController::class, 'login']);
Route::post('/nguoidung', [NguoiDungController::class, 'getNguoiDung']);
Route::post('/updateFaceStatus', [NguoiDungController::class, 'updateFaceStatus']);
Route::post('/getNguoiDungWithCompany', [NguoiDungController::class, 'getNguoiDungWithCompany']);
Route::post('/doimatkhau', [TaiKhoanController::class, 'changePassword']);
