<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS cho hóa đơn giống máy in KiotViet */
        .receipt-print {
            font-family: 'Courier New', Courier, monospace;
            background: #fff;
            color: #000;
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .receipt-print .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>
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
        
        <h2 class="text-2xl font-black text-gray-800 mb-6">Danh sách Đơn Đặt Phòng</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-bold">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-bold">⚠️ {{ session('error') }}</div>
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
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Đã cọc (Chờ đến)</span>
                                @elseif($booking->status == 'checked_in')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Đang lưu trú</span>
                                @elseif($booking->status == 'completed')
                                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-xs font-bold border border-gray-300">Đã trả phòng</span>
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
                                        <form action="/admin/booking/{{ $booking->id }}/cancel" method="POST" onsubmit="return confirm('Hủy đơn này?');">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Hủy</button>
                                        </form>
                                    </div>

                                @elseif($booking->status == 'confirmed')
                                    <button onclick="openCheckinModal({{ $booking->id }}, '{{ $booking->customer_name }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-xs font-bold transition shadow-sm w-full max-w-[120px]">
                                        🛎️ Check-in
                                    </button>

                                @elseif($booking->status == 'checked_in')
                                    <button onclick="openCheckoutModal({{ $booking->id }}, '{{ $booking->customer_name }}', '{{ $booking->room->room_number ?? '' }}', '{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}', '{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}', {{ $booking->total_price }})" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1.5 rounded text-xs font-bold transition shadow-sm w-full max-w-[120px]">
                                        💳 Check-out
                                    </button>
                                    @if($booking->id_card_image)
                                        <a href="{{ asset($booking->id_card_image) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline">Xem giấy tờ</a>
                                    @endif

                                @elseif($booking->status == 'refund_pending')
                                    <div class="text-left bg-amber-50 p-2 rounded border border-amber-200 w-full max-w-[150px]">
                                        <button onclick="showRefundActions({{ $booking->id }})" id="processBtn-{{ $booking->id }}" class="bg-amber-500 hover:bg-amber-600 text-white px-2 py-1 rounded text-xs font-bold w-full">Xử lý hoàn trả</button>
                                        <div id="refundActions-{{ $booking->id }}" class="hidden flex flex-col gap-1 mt-1">
                                            <form action="/admin/booking/{{ $booking->id }}/refund-confirm" method="POST" onsubmit="return confirm('Xác nhận đã chuyển khoản?');">
                                                @csrf <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-[11px] w-full">Đã hoàn tiền</button>
                                            </form>
                                            <form action="/admin/booking/{{ $booking->id }}/refund-deny" method="POST" onsubmit="return confirm('Từ chối hoàn tiền?');">
                                                @csrf <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-[11px] w-full">Từ chối</button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Đã kết thúc</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Chưa có đơn đặt phòng nào.</td>
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
                            <td class="p-4 font-bold text-gray-700">{{ $user->name }}</td>
                            <td class="p-4 text-gray-500">{{ $user->email }}</td>
                            <td class="p-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">Đang chờ duyệt</span></td>
                            <td class="p-4 text-center flex justify-center gap-2">
                                <form action="/admin/approve-email/{{ $user->id }}" method="POST">
                                    @csrf <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-bold">Cấp quyền</button>
                                </form>
                                <form action="/admin/reject-email/{{ $user->id }}" method="POST">
                                    @csrf <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-gray-500">Chưa có yêu cầu đổi email nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <div id="checkinModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center">
        <div class="bg-white w-full max-w-sm rounded-xl shadow-2xl p-6">
            <h3 class="text-lg font-black text-gray-900 mb-2">Thủ tục Nhận Phòng (Check-in)</h3>
            <p class="text-sm text-gray-600 mb-4">Khách hàng: <span id="checkin_customer_name" class="font-bold text-blue-700"></span></p>
            
            <form id="checkinForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tải lên CCCD / Passport <span class="text-red-500">*</span></label>
                    <input type="file" name="id_card_image" accept="image/*" class="w-full border border-gray-300 rounded p-2 text-sm bg-gray-50" required>
                    <p class="text-xs text-gray-500 mt-1 italic">Vui lòng chụp rõ nét giấy tờ tùy thân của khách.</p>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeCheckinModal()" class="w-1/2 bg-gray-200 text-gray-800 font-bold py-2 rounded transition">Hủy</button>
                    <button type="submit" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow-md">Hoàn tất Check-in</button>
                </div>
            </form>
        </div>
    </div>

    <div id="checkoutModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center">
        <div class="bg-gray-200 w-full max-w-md rounded-xl shadow-2xl p-6">
            
            <div class="receipt-print" id="printArea">
                <div class="text-center">
                    <h2 class="font-black text-xl mb-1">THIÊN ÂN HOTEL</h2>
                    <p class="text-xs">Đ/c: Ea Kao, Buôn Ma Thuột, Đắk Lắk</p>
                    <p class="text-xs">SĐT: 0987.654.321</p>
                </div>
                
                <div class="dashed-line"></div>
                <h3 class="text-center font-bold text-lg my-2">HÓA ĐƠN THANH TOÁN</h3>
                <div class="dashed-line"></div>

                <div class="text-sm space-y-1 mb-4">
                    <p><strong>Mã ĐĐP:</strong> #<span id="bill_id"></span></p>
                    <p><strong>Khách hàng:</strong> <span id="bill_name"></span></p>
                    <p><strong>Phòng:</strong> <span id="bill_room"></span></p>
                    <p><strong>Ngày đến:</strong> <span id="bill_in"></span></p>
                    <p><strong>Ngày đi:</strong> <span id="bill_out"></span></p>
                </div>

                <div class="dashed-line"></div>
                
                <div class="flex justify-between font-bold text-sm">
                    <span>Tổng tiền (Đã gồm phụ thu):</span>
                    <span id="bill_total"></span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span>Đã cọc trực tuyến (30%):</span>
                    <span id="bill_deposit"></span>
                </div>

                <div class="dashed-line"></div>

                <div class="flex justify-between font-black text-lg mt-2">
                    <span>CẦN THANH TOÁN:</span>
                    <span id="bill_remaining"></span>
                </div>
                
                <div class="text-center mt-6 text-xs italic">
                    <p>Cảm ơn quý khách và hẹn gặp lại!</p>
                    <p>Powered by Thien An System</p>
                </div>
            </div>

            <div class="flex gap-2 pt-6">
                <button type="button" onclick="closeCheckoutModal()" class="w-1/3 bg-gray-400 text-white font-bold py-2 rounded transition">Quay lại</button>
                
                <form id="checkoutForm" method="POST" class="w-2/3 m-0">
                    @csrf
                    <button type="submit" onclick="return confirm('Xác nhận đã thu đủ tiền và trả phòng?')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded transition shadow-md">
                        💸 Đã Thu Tiền & Check-out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Các hàm phụ trợ Modal
        function showRefundActions(id) {
            const actionDiv = document.getElementById('refundActions-' + id);
            actionDiv.classList.toggle('hidden');
        }

        // Logic Modal Check-in
        function openCheckinModal(id, name) {
            document.getElementById('checkin_customer_name').innerText = name;
            document.getElementById('checkinForm').action = '/admin/booking/' + id + '/check-in';
            document.getElementById('checkinModal').classList.remove('hidden');
        }
        function closeCheckinModal() {
            document.getElementById('checkinModal').classList.add('hidden');
        }

        // Logic Modal Check-out (Tính toán tiền 30/70)
        function openCheckoutModal(id, name, room, checkIn, checkOut, totalAmount) {
            document.getElementById('bill_id').innerText = id;
            document.getElementById('bill_name').innerText = name;
            document.getElementById('bill_room').innerText = room;
            document.getElementById('bill_in').innerText = checkIn;
            document.getElementById('bill_out').innerText = checkOut;

            // Tính tiền Cọc 30% và Còn lại 70%
            let deposit = Math.round(totalAmount * 0.3);
            let remaining = totalAmount - deposit;

            document.getElementById('bill_total').innerText = totalAmount.toLocaleString() + 'đ';
            document.getElementById('bill_deposit').innerText = '-' + deposit.toLocaleString() + 'đ';
            document.getElementById('bill_remaining').innerText = remaining.toLocaleString() + 'đ';

            document.getElementById('checkoutForm').action = '/admin/booking/' + id + '/check-out';
            document.getElementById('checkoutModal').classList.remove('hidden');
        }
        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>