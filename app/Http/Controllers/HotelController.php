<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HotelController extends Controller
{
    // 1. Logic trang chủ: Lấy ra 3 phòng ngẫu nhiên để hiển thị nổi bật
    public function index() {
        $rooms = Room::where('status', 'available')->take(3)->get();
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

    // 4. Logic trang form đặt phòng (Tự động bắt ID phòng nếu khách bấm từ nút Xem chi tiết qua)
    public function bookingForm(Request $request) {
        $rooms = Room::where('status', 'available')->get();
        $selectedRoomId = $request->query('room_id');
        return view('booking', compact('rooms', 'selectedRoomId'));
    }

    // 5. Logic xử lý khi khách ấn nút GỬI FORM ĐẶT PHÒNG
    public function checkout(Request $request) {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Tính toán số đêm và tổng tiền bằng thư viện Carbon có sẵn của Laravel
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $nights * $room->price_per_night;

        // Lưu vào Database
        Booking::create([
            'room_id' => $request->room_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_price' => $totalPrice,
        ]);

        // Đổi trạng thái phòng thành đã được đặt để người khác không đặt trùng
        $room->update(['status' => 'booked']);

        return redirect('/rooms')->with('success', 'Đặt phòng thành công! Tổng tiền của bạn là: ' . number_format($totalPrice) . ' VNĐ.');
    }
// 6. Logic trang Quản trị (Admin) + Thống kê
    public function admin() {
        // Lấy danh sách đơn đặt phòng
        $bookings = Booking::with('room')->orderBy('created_at', 'desc')->get();

        // THỐNG KÊ NHANH:
        // 1. Tính tổng doanh thu (chỉ cộng tiền các đơn đã duyệt)
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
        
        // 2. Đếm tổng số phòng đang có trong hệ thống
        $totalRooms = Room::count();
        
        // 3. Đếm số lượng đơn hàng đang chờ duyệt
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Gửi toàn bộ dữ liệu này ra ngoài giao diện
        return view('admin', compact('bookings', 'totalRevenue', 'totalRooms', 'pendingBookings'));
    }
    // 8. Logic Admin: Duyệt/Hủy đơn đặt phòng và Nhả phòng trống
// Logic Admin: Duyệt đơn đặt phòng
    public function confirmBooking($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']); // Đổi trạng thái thành đã duyệt
        
        return redirect('/admin')->with('success', 'Đã duyệt thành công đơn đặt phòng #' . $id);
    }

    // Logic Admin: Hủy đơn đặt phòng và Nhả phòng trống
    public function cancelBooking($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']); // Đổi trạng thái thành đã hủy
        
        // Nhả phòng về trạng thái available
        $room = Room::find($booking->room_id);
        if ($room) {
            $room->update(['status' => 'available']);
        }
        
        return redirect('/admin')->with('error', 'Đã hủy đơn #' . $id . ' và tự động nhả phòng trống cho khách khác.');
    }
// 9. Hiển thị form Đăng nhập
    public function loginForm() {
        return view('login');
    }

    // 10. Xử lý khi bấm nút Đăng nhập
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Nếu email và pass đúng -> Cấp quyền vào /admin
            return redirect('/admin');
        }

        // Nếu sai -> Đuổi về trang login kèm thông báo lỗi
        return redirect('/login')->with('error', 'Email hoặc mật khẩu không chính xác!');
    }

    // 11. Xử lý Đăng xuất
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
    // 12. Hiển thị form Thêm phòng mới
    public function createRoom() {
        return view('create_room');
    }

    // 13. Xử lý lưu phòng mới vào Database
    public function storeRoom(Request $request) {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number', // Không được trùng số phòng cũ
            'room_type' => 'required',
            'price_per_night' => 'required|numeric',
            'image_url' => 'nullable|url',
            'description' => 'nullable'
        ]);

        // Lưu vào CSDL
        Room::create([
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'price_per_night' => $request->price_per_night,
            'image_url' => $request->image_url,
            'description' => $request->description,
            'status' => 'available' // Mặc định phòng mới tạo là còn trống
        ]);

        // Thêm xong thì quay về trang chủ hoặc danh sách phòng để xem kết quả
        return redirect('/rooms')->with('success', 'Đã thêm phòng ' . $request->room_number . ' thành công!');
    }
    // 14. Hiển thị danh sách phòng (Giao diện Admin)
    public function manageRooms() {
        $rooms = Room::orderBy('created_at', 'desc')->get();
        return view('manage_rooms', compact('rooms'));
    }

    // 15. Xóa phòng
    public function deleteRoom($id) {
        $room = Room::findOrFail($id);
        
        // Kiểm tra xem phòng có đang có người đặt không, nếu có thì không cho xóa
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
        
        // Kiểm tra dữ liệu. Lưu ý: Cho phép giữ nguyên số phòng hiện tại (bỏ qua check unique của chính nó)
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id,
            'room_type' => 'required',
            'price_per_night' => 'required|numeric',
            'image_url' => 'nullable|url',
            'description' => 'nullable'
        ]);

        // Cập nhật vào Database
        $room->update([
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'price_per_night' => $request->price_per_night,
            'image_url' => $request->image_url,
            'description' => $request->description
        ]);

        return redirect('/admin/rooms')->with('success', 'Đã cập nhật thông tin phòng P.' . $room->room_number . ' thành công!');
    }
}