<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User; // Đã thêm Model User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Đã thêm thư viện mã hóa mật khẩu
use Carbon\Carbon;

class HotelController extends Controller
{
    // 1. Logic trang chủ: Lấy ra 3 phòng ngẫu nhiên để hiển thị nổi bật
    public function index() {
        $rooms = Room::inRandomOrder()->take(3)->get();
        return view('index', compact('rooms'));
    }

    // 2. Logic trang danh sách phòng: Lấy toàn bộ phòng
    public function rooms() {
        $rooms = Room::all();
        return view('rooms', compact('rooms'));
    }

    // 3. Logic trang chi tiết phòng
    public function detail($id) {
        $room = Room::findOrFail($id);
        return view('detail', compact('room'));
    }

    // 4. Logic trang form đặt phòng
    public function bookingForm(Request $request) {
        $rooms = Room::all();
        $selectedRoomId = $request->query('room_id');

        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
                           ->where('check_out_date', '>=', now()->toDateString())
                           ->get(['room_id', 'check_in_date', 'check_out_date']);

        $bookedDates = $bookings->groupBy('room_id')->map(function ($roomBookings) {
            return $roomBookings->map(function ($booking) {
                return [
                    'from' => $booking->check_in_date,
                    'to' => $booking->check_out_date
                ];
            });
        });

        return view('booking', compact('rooms', 'selectedRoomId', 'bookedDates'));
    }

    // 5. Logic xử lý khi khách ấn nút GỬI FORM ĐẶT PHÒNG
    public function checkout(Request $request) {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'customer_email' => ['nullable', 'email', 'ends_with:@gmail.com'],
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ], [
            'customer_phone.regex' => 'Số điện thoại không hợp lệ! Vui lòng nhập đúng 10 số và bắt đầu bằng số 0.',
            'customer_email.email' => 'Địa chỉ email không đúng định dạng.',
            'customer_email.ends_with' => 'Hệ thống hiện tại chỉ hỗ trợ nhận email có đuôi @gmail.com.',
        ]);

        $room = Room::findOrFail($request->room_id);

        $isConflict = Booking::where('room_id', $request->room_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<', $request->check_out_date)
            ->where('check_out_date', '>', $request->check_in_date)
            ->exists();

        if ($isConflict) {
            return redirect()->back()->with('error', 'Rất tiếc! Phòng này đã có khách giữ chỗ trong khoảng thời gian bạn chọn. Vui lòng chọn ngày khác!');
        }

        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        if ($nights == 0) {
            $nights = 1;
        }
        $totalPrice = $nights * $room->price_per_night;

        $booking = Booking::create([
            'room_id' => $request->room_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_price' => $totalPrice,
        ]);

        $room->update(['status' => 'booked']);

        return redirect('/payment/' . $booking->id);
    }

    // 6. Logic trang Quản trị (Admin) + Thống kê
    public function admin() {
        $bookings = Booking::with('room')->orderBy('created_at', 'desc')->get();

        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
        $totalRooms = Room::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        return view('admin', compact('bookings', 'totalRevenue', 'totalRooms', 'pendingBookings'));
    }

    // 7. Logic Admin: Duyệt đơn đặt phòng
    public function confirmBooking($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']); 
        
        return redirect('/admin')->with('success', 'Đã duyệt thành công đơn đặt phòng #' . $id);
    }

    // 8. Logic Admin: Hủy đơn đặt phòng và Nhả phòng trống
    public function cancelBooking($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']); 
        
        $room = Room::find($booking->room_id);
        if ($room) {
            $room->update(['status' => 'available']);
        }
        
        return redirect('/admin')->with('error', 'Đã hủy đơn #' . $id . ' và tự động nhả phòng trống cho khách khác.');
    }

    // =========================================================
    // QUẢN LÝ TÀI KHOẢN (LOGIN / REGISTER / LOGOUT)
    // =========================================================

    // 9. Hiển thị form Đăng nhập
    public function loginForm() {
        return view('login');
    }

    // 10. Xử lý Đăng nhập & Phân luồng
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Nếu là Admin -> Vào trang quản trị
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }
            // Nếu là Khách -> Về trang chủ
            return redirect('/');
        }

        return redirect('/login')->with('error', 'Email hoặc mật khẩu không chính xác!');
    }

    // 11. Xử lý Đăng xuất
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    // Hiển thị form Đăng ký
    public function registerForm() {
        return view('register');
    }

    // Xử lý Đăng ký
    public function register(Request $request) {
        // Kiểm tra dữ liệu hợp lệ
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email này đã được sử dụng!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        // Tạo tài khoản mới (Mặc định role là customer do migration đã setup)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Đăng nhập luôn cho khách sau khi đăng ký thành công
        Auth::login($user);

        return redirect('/')->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với Thiên Ân Hotel.');
    }

    // =========================================================
    // QUẢN LÝ THÔNG TIN CÁ NHÂN (PROFILE & GIAO DỊCH)
    // =========================================================

    // Hiển thị giao diện Thông tin cá nhân
    public function profile() {
        return view('profile');
    }

    // Xử lý cập nhật thông tin cá nhân
    public function updateProfile(Request $request) {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6', 
        ]);

        $user->name = $request->name;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // Xem lịch sử đặt phòng của khách hàng
    public function myBookings() {
        // Lấy danh sách các đơn trùng với email của tài khoản đang đăng nhập
        $bookings = Booking::with('room')
            ->where('customer_email', Auth::user()->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my_bookings', compact('bookings'));
    }

    // =========================================================
    // QUẢN LÝ PHÒNG DÀNH CHO ADMIN
    // =========================================================

    // 12. Hiển thị form Thêm phòng mới
    public function createRoom() {
        return view('create_room');
    }

    // 13. Xử lý lưu phòng mới vào Database
    public function storeRoom(Request $request) {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number',
            'room_type' => 'required',
            'price_per_night' => 'required|numeric',
            'image_url' => 'nullable|url',
            'description' => 'nullable'
        ]);

        Room::create([
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'price_per_night' => $request->price_per_night,
            'image_url' => $request->image_url,
            'description' => $request->description,
            'status' => 'available' 
        ]);

        return redirect('/admin/rooms')->with('success', 'Đã thêm phòng ' . $request->room_number . ' thành công!');
    }

    // 14. Hiển thị danh sách phòng (Giao diện Admin)
    public function manageRooms() {
        $rooms = Room::orderBy('created_at', 'desc')->get();
        return view('manage_rooms', compact('rooms'));
    }

    // 15. Xóa phòng
    public function deleteRoom($id) {
        $room = Room::findOrFail($id);
        
        $hasBookings = Booking::where('room_id', $id)->whereIn('status', ['pending', 'confirmed'])->exists();
        if ($hasBookings) {
            return redirect('/admin/rooms')->with('error', 'Không thể xóa! Phòng này đang có khách đặt hoặc đang ở.');
        }

        $room->delete();
        return redirect('/admin/rooms')->with('success', 'Đã xóa phòng thành công!');
    }

    // 16. Hiển thị form Sửa phòng
    public function editRoom($id) {
        $room = Room::findOrFail($id);
        return view('edit_room', compact('room'));
    }

    // 17. Xử lý lưu thông tin phòng đã sửa vào Database
    public function updateRoom(Request $request, $id) {
        $room = Room::findOrFail($id);
        
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id,
            'room_type' => 'required',
            'price_per_night' => 'required|numeric',
            'image_url' => 'nullable|url',
            'description' => 'nullable'
        ]);

        $room->update([
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'price_per_night' => $request->price_per_night,
            'image_url' => $request->image_url,
            'description' => $request->description
        ]);

        return redirect('/admin/rooms')->with('success', 'Đã cập nhật thông tin phòng P.' . $room->room_number . ' thành công!');
    }

    // =========================================================
    // THANH TOÁN VÀ TÌM KIẾM
    // =========================================================

    // 18. Trang Thanh Toán Trung Gian (Mô phỏng VNPay)
    public function payment($id) {
        $booking = Booking::findOrFail($id);
        $deposit = $booking->total_price * 0.3;

        $bankId = 'VCB'; 
        $accountNo = '1234567890'; 
        $accountName = 'NGUYEN VAN A'; 

        $description = 'Thanh toan coc don ' . $booking->id;
        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$deposit}&addInfo=" . urlencode($description) . "&accountName=" . urlencode($accountName);

        return view('payment', compact('booking', 'deposit', 'qrUrl'));
    }

    // 19. Xử lý khi khách bấm "Tôi đã chuyển khoản" (Giả lập Webhook)
    public function confirmPayment($id) {
        return redirect('/rooms')->with('success', 'Đã ghi nhận yêu cầu đặt phòng! Chúng tôi sẽ kiểm tra thanh toán và liên hệ với bạn trong giây lát.');
    }

    // 20. API Nhận biến động số dư từ SePay Webhook
    public function sepayWebhook(Request $request) {
        $content = strtoupper($request->input('content')); 
        $amount = (int) $request->input('transferAmount');

        if (preg_match('/DON\s+(\d+)/i', $content, $matches)) {
            $bookingId = $matches[1];
            $booking = Booking::find($bookingId);

            if ($booking && $booking->status == 'pending') {
                $booking->update(['status' => 'confirmed']);
                return response()->json(['success' => true, 'message' => 'Đã duyệt đơn hàng tự động!']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng hoặc đã được duyệt.']);
    }

    // 21. Thuật toán tìm kiếm phòng trống theo khoảng thời gian
    public function searchRooms(Request $request) {
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');

        if (!$checkIn || !$checkOut) {
            return redirect('/rooms');
        }

        $bookedRoomIds = Booking::whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->pluck('room_id') 
            ->toArray();

        $rooms = Room::whereNotIn('id', $bookedRoomIds)->get();

        $message = "Hiển thị các phòng trống từ " . \Carbon\Carbon::parse($checkIn)->format('d/m/Y') . " đến " . \Carbon\Carbon::parse($checkOut)->format('d/m/Y');
        
        return view('rooms', compact('rooms'))->with('searchMessage', $message);
    }
}