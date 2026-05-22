<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Carbon\Carbon;
use App\Models\Notification; // Đã tích hợp Model Notification

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

        // BẮT SỰ KIỆN: Bắn thông báo cho ADMIN khi có đơn mới
        Notification::create([
            'user_id' => null, // Gửi cho hệ thống (Admin)
            'title' => '🛎️ Đơn đặt phòng mới',
            'message' => 'Khách hàng ' . $request->customer_name . ' vừa đặt phòng ' . $room->room_number . ' (Mã đơn: #' . $booking->id . ').'
        ]);

        return redirect('/payment/' . $booking->id);
    }

    // 6. Logic trang Quản trị (Admin) + Thống kê
    public function admin() {
        $bookings = Booking::with('room')->orderBy('created_at', 'desc')->get();
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
        $totalRooms = Room::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Lấy danh sách khách hàng đang xin đổi Email
        $pendingEmails = User::where('email_change_status', 'pending')->get();

        return view('admin', compact('bookings', 'totalRevenue', 'totalRooms', 'pendingBookings', 'pendingEmails'));
    }

    // Logic Admin: Cấp quyền đổi Email cho khách
// Logic Admin: Cấp quyền đổi Email cho khách
    public function approveEmailChange($id) {
        $user = User::findOrFail($id);
        if ($user->email_change_status === 'pending') {
            $user->email_change_status = 'approved';
            $user->save();

            Notification::create([
                'user_id' => $user->id,
                'title' => '✅ Yêu cầu được duyệt',
                'message' => 'Quản trị viên đã cấp quyền. Bạn có thể vào phần Thông tin cá nhân để đổi Email mới ngay bây giờ.'
            ]);

            return back()->with('success', 'Đã cấp quyền đổi Email cho khách hàng: ' . $user->name);
        }
        return back();
    }

    // Logic Admin: Từ chối quyền đổi Email của khách
    public function rejectEmailChange($id) {
        $user = User::findOrFail($id);
        if ($user->email_change_status === 'pending') {
            // Trả trạng thái về none để họ có thể xin lại nếu muốn
            $user->email_change_status = 'none'; 
            $user->save();

            // BẮT SỰ KIỆN: Báo cho khách hàng biết Admin đã từ chối
            Notification::create([
                'user_id' => $user->id,
                'title' => '❌ Yêu cầu bị từ chối',
                'message' => 'Quản trị viên đã từ chối yêu cầu đổi Email của bạn. Nếu có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ.'
            ]);

            // Dùng 'error' session để hiển thị thông báo màu đỏ bên trang Admin
            return back()->with('error', 'Đã từ chối yêu cầu đổi Email của khách hàng: ' . $user->name);
        }
        return back();
    }
    
    // 7. Logic Admin: Duyệt đơn đặt phòng
    public function confirmBooking($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']); 
        
        // BẮT SỰ KIỆN: Tìm user dựa trên email đặt phòng để báo cho họ
        $user = User::where('email', $booking->customer_email)->first();
        if ($user) {
            $room = Room::find($booking->room_id);
            Notification::create([
                'user_id' => $user->id,
                'title' => '✅ Đơn đặt phòng đã duyệt',
                'message' => 'Đơn đặt phòng #' . $booking->id . ' (Phòng ' . ($room->room_number ?? '') . ') của bạn đã được xác nhận thành công!'
            ]);
        }

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
        
        // BẮT SỰ KIỆN: Báo cho khách hàng biết đơn đã bị hủy
        $user = User::where('email', $booking->customer_email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => '❌ Đơn đặt phòng bị hủy',
                'message' => 'Rất tiếc, đơn đặt phòng #' . $booking->id . ' của bạn đã bị hủy bởi Quản trị viên.'
            ]);
        }

        return redirect('/admin')->with('error', 'Đã hủy đơn #' . $id . ' và tự động nhả phòng trống cho khách khác.');
    }

    // =========================================================
    // QUẢN LÝ TÀI KHOẢN (LOGIN / REGISTER / LOGOUT)
    // =========================================================

    public function loginForm() {
        return view('login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }
            return redirect('/');
        }
        return redirect('/login')->with('error', 'Email hoặc mật khẩu không chính xác!');
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    public function registerForm() {
        return view('register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email này đã được sử dụng!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với Thiên Ân Hotel.');
    }

    // =========================================================
    // QUẢN LÝ THÔNG TIN CÁ NHÂN (PROFILE & GIAO DỊCH)
    // =========================================================

    public function profile() {
        return view('profile');
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'old_password' => 'nullable|required_with:password', 
            'password' => 'nullable|string|min:6|confirmed', 
        ], [
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp!',
            'old_password.required_with' => 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu mới.'
        ]);

        $user->name = $request->name;
        
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Mật khẩu hiện tại không đúng!');
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updateAvatar(Request $request) {
        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048']);
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename); 
            
            $user->avatar = $filename;
            $user->save();
        }
        return back()->with('success', 'Đã thay đổi ảnh đại diện!');
    }

// 3. Xử lý Yêu cầu / Xác nhận đổi Email (ĐÃ NÂNG CẤP BẢO MẬT)
    public function requestEmailChange(Request $request) {
        $user = Auth::user();

        // BẮT BUỘC BƯỚC NÀY: Xác thực mật khẩu trước khi làm bất cứ hành động nào
        $request->validate([
            'password' => 'required|string'
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu để xác thực quyền sở hữu.'
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Mật khẩu xác thực không chính xác! Yêu cầu bị từ chối.');
        }

        // 1. Khách bấm yêu cầu đổi Email
        if ($request->action == 'request') {
            $user->email_change_status = 'pending';
            $user->save();

            // Bắn thông báo cho Admin
            Notification::create([
                'user_id' => null, 
                'title' => '✉️ Yêu cầu đổi Email',
                'message' => 'Khách hàng ' . $user->name . ' vừa gửi yêu cầu xin đổi địa chỉ Email.'
            ]);

            return back()->with('success', 'Đã gửi yêu cầu đổi Email đến Quản trị viên. Vui lòng chờ duyệt!');
        }

        // 2. Khách xác nhận đổi Email mới
        if ($request->action == 'update') {
            $request->validate(['new_email' => 'required|email|unique:users,email'], [
                'new_email.unique' => 'Email này đã tồn tại trong hệ thống, vui lòng chọn email khác.'
            ]);
            
            $oldEmail = $user->email;
            $user->email = $request->new_email;
            $user->email_change_status = 'changed'; // 'changed' để khóa vĩnh viễn quyền đổi
            $user->save();
            
            // Ghi nhận lịch sử cho Khách hàng
            Notification::create([
                'user_id' => $user->id,
                'title' => '📝 Đổi Email thành công',
                'message' => 'Bạn đã xác thực mật khẩu và đổi Email thành công từ ' . $oldEmail . ' thành ' . $request->new_email
            ]);
            
            return back()->with('success', 'Đổi Email thành công! Tài khoản của bạn đã được cập nhật.');
        }

        return back();
    }
        public function myBookings() {
        $bookings = Booking::with('room')
            ->where('customer_email', Auth::user()->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my_bookings', compact('bookings'));
    }

    // =========================================================
    // QUẢN LÝ PHÒNG DÀNH CHO ADMIN
    // =========================================================

    public function createRoom() {
        return view('create_room');
    }

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

    public function manageRooms() {
        $rooms = Room::orderBy('created_at', 'desc')->get();
        return view('manage_rooms', compact('rooms'));
    }

    public function deleteRoom($id) {
        $room = Room::findOrFail($id);
        
        $hasBookings = Booking::where('room_id', $id)->whereIn('status', ['pending', 'confirmed'])->exists();
        if ($hasBookings) {
            return redirect('/admin/rooms')->with('error', 'Không thể xóa! Phòng này đang có khách đặt hoặc đang ở.');
        }

        $room->delete();
        return redirect('/admin/rooms')->with('success', 'Đã xóa phòng thành công!');
    }

    public function editRoom($id) {
        $room = Room::findOrFail($id);
        return view('edit_room', compact('room'));
    }

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

    public function payment($id) {
        $booking = Booking::findOrFail($id);
        $deposit = $booking->total_price * 0.3;

        $bankId = 'VCB'; 
        $accountNo = '9704229206534913427'; 
        $accountName = 'VI CONG NGUYEN'; 

        $description = 'Thanh toan coc don ' . $booking->id;
        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$deposit}&addInfo=" . urlencode($description) . "&accountName=" . urlencode($accountName);

        return view('payment', compact('booking', 'deposit', 'qrUrl'));
    }

    public function confirmPayment($id) {
        return redirect('/rooms')->with('success', 'Đã ghi nhận yêu cầu đặt phòng! Chúng tôi sẽ kiểm tra thanh toán và liên hệ với bạn trong giây lát.');
    }

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