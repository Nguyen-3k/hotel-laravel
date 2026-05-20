<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

// Gọi hàm xử lý từ HotelController
Route::get('/', [HotelController::class, 'index']);
Route::get('/rooms', [HotelController::class, 'rooms']);
Route::get('/detail/{id}', [HotelController::class, 'detail']);
Route::get('/booking', [HotelController::class, 'bookingForm']);

// Route POST đón nhận dữ liệu gửi lên từ form Đăng ký đặt phòng
Route::post('/checkout', [HotelController::class, 'checkout']);

// Tuyến đường Đăng nhập, Đăng xuất
Route::get('/login', [HotelController::class, 'loginForm'])->name('login');
Route::post('/login', [HotelController::class, 'login']);
Route::post('/logout', [HotelController::class, 'logout']);

// VÙNG BẢO MẬT: Bắt buộc phải đăng nhập (auth) mới được vào
Route::middleware('auth')->group(function () {
    Route::get('/admin', [HotelController::class, 'admin']);
    Route::post('/admin/booking/{id}/confirm', [HotelController::class, 'confirmBooking']);
    Route::post('/admin/booking/{id}/cancel', [HotelController::class, 'cancelBooking']);
    
    // 2 dòng mới thêm để xử lý Thêm Phòng
    Route::get('/admin/rooms/create', [HotelController::class, 'createRoom']);
    Route::post('/admin/rooms/store', [HotelController::class, 'storeRoom']);
    // Quản lý danh sách phòng & Xóa
    Route::get('/admin/rooms', [HotelController::class, 'manageRooms']);
    Route::post('/admin/rooms/{id}/delete', [HotelController::class, 'deleteRoom']);
    // Sửa thông tin phòng
    Route::get('/admin/rooms/{id}/edit', [HotelController::class, 'editRoom']);
    Route::post('/admin/rooms/{id}/update', [HotelController::class, 'updateRoom']);
});