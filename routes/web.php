<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

// ==============================================================
// 1. CÁC TRANG CÔNG KHAI (KHÁCH VÃNG LAI)
// ==============================================================
Route::get('/', [HotelController::class, 'index']);
Route::get('/rooms', [HotelController::class, 'rooms']);
Route::get('/detail/{id}', [HotelController::class, 'detail']);
Route::get('/search', [HotelController::class, 'searchRooms']);

// Form đặt phòng cho khách chưa đăng nhập (hoặc đã đăng nhập)
Route::get('/booking', [HotelController::class, 'bookingForm']);
Route::post('/checkout', [HotelController::class, 'checkout']);

// Thanh toán & Webhook (Không cần đăng nhập)
Route::get('/payment/{id}', [HotelController::class, 'payment']);
Route::post('/payment/{id}/confirm', [HotelController::class, 'confirmPayment']);
Route::post('/sepay/webhook', [HotelController::class, 'sepayWebhook']);

// ==============================================================
// 2. TÀI KHOẢN (ĐĂNG NHẬP / ĐĂNG KÝ)
// ==============================================================
Route::get('/login', [HotelController::class, 'loginForm'])->name('login');
Route::post('/login', [HotelController::class, 'login']);
Route::post('/logout', [HotelController::class, 'logout']);

// Thêm route Đăng ký cho Khách hàng
Route::get('/register', [HotelController::class, 'registerForm'])->name('register');
Route::post('/register', [HotelController::class, 'register']);

// ==============================================================
// 3. KHU VỰC KHÁCH HÀNG ĐÃ ĐĂNG NHẬP (Chỉ cần 'auth')
// ==============================================================
Route::middleware('auth')->group(function () {
    // Thông tin cá nhân
    Route::get('/profile', [HotelController::class, 'profile']);
    Route::post('/profile/update', [HotelController::class, 'updateProfile']);
    
    // Khách hàng vào xem lịch sử các đơn họ đã đặt
    Route::get('/my-bookings', [HotelController::class, 'myBookings']); 
});

// ==============================================================
// 4. VÙNG BẢO MẬT ADMIN (Bắt buộc 'auth' VÀ 'admin')
// ==============================================================
Route::middleware(['auth', 'admin'])->group(function () {
    
    // Bảng điều khiển Admin
    Route::get('/admin', [HotelController::class, 'admin']);
    
    // Duyệt / Hủy đơn đặt phòng
    Route::post('/admin/booking/{id}/confirm', [HotelController::class, 'confirmBooking']);
    Route::post('/admin/booking/{id}/cancel', [HotelController::class, 'cancelBooking']);
    
    // Quản lý danh sách phòng (Thêm, Sửa, Xóa)
    Route::get('/admin/rooms', [HotelController::class, 'manageRooms']);
    Route::get('/admin/rooms/create', [HotelController::class, 'createRoom']);
    Route::post('/admin/rooms/store', [HotelController::class, 'storeRoom']);
    Route::get('/admin/rooms/{id}/edit', [HotelController::class, 'editRoom']);
    Route::post('/admin/rooms/{id}/update', [HotelController::class, 'updateRoom']);
    Route::post('/admin/rooms/{id}/delete', [HotelController::class, 'deleteRoom']);
    
});