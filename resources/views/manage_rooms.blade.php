<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Phòng - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-gray-900 text-white py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="/admin" class="text-xl font-bold tracking-wide text-yellow-500">⚙️ HỆ THỐNG QUẢN TRỊ ADMIN</a>
            <nav class="space-x-4 text-sm">
                <a href="/admin" class="hover:text-yellow-400">Đơn hàng</a>
                <a href="/admin/rooms" class="text-yellow-400 font-bold">Danh sách phòng</a>
                <a href="/" class="hover:text-yellow-400 bg-gray-800 px-4 py-2 rounded-lg">Xem web khách</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-10 px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-black text-gray-800">Danh Sách Phòng Khách Sạn</h2>
            <a href="/admin/rooms/create" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow">
                + Thêm Phòng Mới
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 font-bold">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 font-bold">⚠️ {{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-sm uppercase">
                        <th class="p-4 border-b">Số Phòng</th>
                        <th class="p-4 border-b">Hạng Phòng</th>
                        <th class="p-4 border-b">Giá Đêm</th>
                        <th class="p-4 border-b text-center">Trạng Thái</th>
                        <th class="p-4 border-b text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-4 font-bold text-gray-800 text-lg">P.{{ $room->room_number }}</td>
                            <td class="p-4 font-semibold text-emerald-700">{{ $room->room_type }}</td>
                            <td class="p-4 font-bold text-red-600">{{ number_format($room->price_per_night) }}đ</td>
                            <td class="p-4 text-center">
                                @if($room->status == 'available')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Còn trống</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Đang sử dụng</span>
                                @endif
                            </td>
                            <td class="p-4 text-center flex justify-center gap-2">
    <a href="/admin/rooms/{{ $room->id }}/edit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold transition">Sửa</a>
    
    <form action="/admin/rooms/{{ $room->id }}/delete" method="POST" ...>
    </form>
</td>
                            <td class="p-4 text-center">
                                <form action="/admin/rooms/{{ $room->id }}/delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng này khỏi hệ thống?');">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold transition">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>