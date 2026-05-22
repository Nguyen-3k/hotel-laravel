<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    
    <header class="bg-gray-900 text-white py-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="/admin" class="text-xl font-black tracking-wide text-yellow-500 hover:text-yellow-400 transition">
                ⚙️ HỆ THỐNG QUẢN TRỊ
            </a>
            
            <nav class="flex items-center space-x-6 text-sm font-medium">
                <a href="/" class="hover:text-yellow-400 bg-gray-800 px-4 py-2 rounded-lg transition" target="_blank">
                    🌍 Xem trang khách
                </a>
                
                <div class="flex items-center space-x-4">
                    <a href="/admin/rooms" class="hover:text-yellow-400 transition">🏨 Quản lý phòng</a>
                    <a href="/admin/rooms/create" class="hover:text-yellow-400 transition">➕ Thêm phòng</a>
                </div>

                <form action="/logout" method="POST" class="border-l border-gray-700 pl-6 ml-2 m-0 flex items-center">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 font-bold flex items-center gap-2 transition cursor-pointer">
                        <span>🚪</span> Đăng xuất
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-10 px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Tổng Doanh Thu</h3>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($totalRevenue) }}<span class="text-lg text-gray-500 ml-1">đ</span></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full text-2xl">💰</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-emerald-500 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Tổng Số Phòng</h3>
                    <p class="text-3xl font-black text-gray-800">{{ $totalRooms }}<span class="text-lg text-gray-500 ml-1">phòng</span></p>
                </div>
                <div class="bg-emerald-100 p-3 rounded-full text-2xl">🏨</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-yellow-500 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Đơn Chờ Duyệt</h3>
                    <p class="text-3xl font-black text-gray-800">{{ $pendingBookings }}<span class="text-lg text-gray-500 ml-1">đơn</span></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full text-2xl">⏳</div>
            </div>
        </div>
        
        <h2 class="text-2xl font-black text-gray-800 mb-6">Danh sách Đơn Đặt Phòng Gần Đây</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-sm uppercase">
                        <th class="p-4 border-b">ID</th>
                        <th class="p-4 border-b">Khách Hàng</th>
                        <th class="p-4 border-b">Phòng Đặt</th>
                        <th class="p-4 border-b">Ngày Đặt</th>
                        <th class="p-4 border-b text-center">Trạng Thái</th>
                        <th class="p-4 border-b text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-4 font-bold text-gray-600">#{{ $booking->id }}</td>
                            <td class="p-4 font-bold text-emerald-700">
                                {{ $booking->customer_name }} <br>
                                <span class="text-xs text-gray-500 font-normal">📞 {{ $booking->customer_phone }}</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-amber-100 text-amber-800 font-bold px-2 py-1 rounded">
                                    P.{{ $booking->room->room_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4">
                                {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                            </td>
                            
                            <td class="p-4 text-center">
                                @if($booking->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">Chờ xác nhận</span>
                                @elseif($booking->status == 'confirmed')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Đã duyệt</span>
                                @elseif($booking->status == 'refund_pending')
                                    <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-bold border border-amber-200">Chờ hoàn tiền</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Đã hủy</span>
                                @endif
                            </td>

                            <td class="p-4 text-center flex flex-col items-center justify-center gap-2">
                                @if($booking->status == 'pending')
                                    <div class="flex gap-2">
                                        <form action="/admin/booking/{{ $booking->id }}/confirm" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Duyệt</button>
                                        </form>
                                        <form action="/admin/booking/{{ $booking->id }}/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này không?');">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Hủy đơn</button>
                                        </form>
                                    </div>

                                @elseif($booking->status == 'refund_pending')
                                    <div class="text-left bg-amber-50 p-3 rounded-lg border border-amber-200 w-full max-w-xs mb-1">
                                        <p class="text-xs font-bold text-amber-800">🏦 Thông tin hoàn trả:</p>
                                        <p class="text-xs text-gray-700 font-mono mt-0.5">{{ $booking->bank_info }}</p>
                                        @if($booking->refund_qr)
                                            <a href="{{ asset($booking->refund_qr) }}" target="_blank" class="text-[11px] text-blue-600 font-bold hover:underline block mt-1">🖼️ Xem ảnh QR nhận tiền</a>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-1 w-full items-center">
                                        <button onclick="showRefundActions({{ $booking->id }})" id="processBtn-{{ $booking->id }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm w-full max-w-[120px]">
                                            ⚙️ Xử lý hoàn trả
                                        </button>
                                        
                                        <div id="refundActions-{{ $booking->id }}" class="hidden flex gap-1.5 mt-1">
                                            <form action="/admin/booking/{{ $booking->id }}/refund-confirm" method="POST" onsubmit="return confirm('Xác nhận bạn ĐÃ chuyển khoản hoàn tiền cho khách?');">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-[11px] font-bold transition shadow-sm">
                                                    Đã hoàn tiền
                                                </button>
                                            </form>
                                            <form action="/admin/booking/{{ $booking->id }}/refund-deny" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn TỪ CHỐI hoàn tiền?');">
                                                @csrf
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-[11px] font-bold transition shadow-sm">
                                                    Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Đã xử lý xong</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Chưa có đơn đặt phòng nào trong hệ thống.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <h2 class="text-2xl font-black text-gray-800 mb-6 mt-12">✉️ Yêu Cầu Đổi Email</h2>
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-12">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-800 text-white text-sm uppercase">
                    <tr>
                        <th class="p-4 border-b">Khách Hàng</th>
                        <th class="p-4 border-b">Email Hiện Tại</th>
                        <th class="p-4 border-b text-center">Trạng Thái</th>
                        <th class="p-4 border-b text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pendingEmails as $user)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-4 font-bold text-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-500">{{ $user->email }}</td>
                            <td class="p-4 text-center">
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">Đang chờ duyệt</span>
                            </td>
                            <td class="p-4 text-center flex justify-center gap-2">
                                <form action="/admin/approve-email/{{ $user->id }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                        ✅ Cấp quyền
                                    </button>
                                </form>
                                
                                <form action="/admin/reject-email/{{ $user->id }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu này không?');">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                        ❌ Từ chối
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Chưa có yêu cầu đổi email nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentEmailsCount = {{ count($pendingEmails) }};
            let currentBookingsCount = {{ $pendingBookings }};

            setInterval(function() {
                fetch('/admin/check-new-data')
                    .then(response => response.json())
                    .then(data => {
                        let hasNew = false;
                        let msg = "";

                        if(data.emails > currentEmailsCount) {
                            hasNew = true;
                            msg += "Có yêu cầu đổi Email mới! \n";
                            currentEmailsCount = data.emails;
                        }

                        if(data.bookings > currentBookingsCount) {
                            hasNew = true;
                            msg += "Có đơn đặt phòng mới! \n";
                            currentBookingsCount = data.bookings;
                        }

                        if(hasNew) {
                            let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                            audio.play().catch(e => console.log("Trình duyệt chặn autoplay âm thanh"));

                            alert("🔔 THÔNG BÁO HỆ THỐNG:\n" + msg + "Vui lòng F5 lại trang để xử lý!");
                        }
                    })
                    .catch(e => console.error("Lỗi cập nhật real-time:", e));
            }, 5000); 
        });

        // Hàm JavaScript hỗ trợ hiển thị 2 nút hoàn tiền
        function showRefundActions(id) {
            const actionDiv = document.getElementById('refundActions-' + id);
            const processBtn = document.getElementById('processBtn-' + id);
            if(actionDiv) {
                actionDiv.classList.toggle('hidden');
                
                if(!actionDiv.classList.contains('hidden')) {
                    processBtn.innerText = '❌ Đóng lựa chọn';
                    processBtn.classList.replace('bg-amber-500', 'bg-gray-500');
                    processBtn.classList.replace('hover:bg-amber-600', 'hover:bg-gray-600');
                } else {
                    processBtn.innerText = '⚙️ Xử lý hoàn trả';
                    processBtn.classList.replace('bg-gray-500', 'bg-amber-500');
                    processBtn.classList.replace('hover:bg-gray-600', 'hover:bg-amber-600');
                }
            }
        }
    </script>
</body>
</html>