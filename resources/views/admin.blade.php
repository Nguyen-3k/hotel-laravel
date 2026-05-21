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
                                @else
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Đã hủy</span>
                                @endif
                            </td>

                            <td class="p-4 text-center flex justify-center gap-2">
                                @if($booking->status == 'pending')
                                    <form action="/admin/booking/{{ $booking->id }}/confirm" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Duyệt</button>
                                    </form>
                                    
                                    <form action="/admin/booking/{{ $booking->id }}/cancel" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này và nhả phòng không?');">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">Hủy đơn</button>
                                    </form>
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
    </main>
</body>
</html>