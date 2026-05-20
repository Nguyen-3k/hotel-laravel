<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Phòng - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-emerald-800 text-white py-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="/" class="text-xl font-bold tracking-wide">✨ THIÊN ÂN HOTEL</a>
            <nav class="space-x-6 font-medium">
                <a href="/" class="hover:text-yellow-400">Trang chủ</a>
                <a href="/rooms" class="hover:text-yellow-400 border-b-2 border-yellow-400 pb-1">Danh sách phòng</a>
                <a href="/booking" class="hover:text-yellow-400">Đặt phòng</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-3xl font-black text-gray-900 mb-8">Danh Sách Tất Cả Các Phòng</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($rooms as $room)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="relative">
                            <img src="{{ $room->image_url ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500' }}" alt="{{ $room->room_number }}" class="w-100 h-48 object-cover">
                            @if($room->status == 'available')
                                <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">🟢 Còn trống</span>
                            @else
                                <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">🔴 Đã được đặt</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Phòng {{$room->room_number}} - Hạng {{$room->room_type}}</h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $room->description }}</p>
                        </div>
                    </div>
                    <div class="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50">
                        <span class="text-md font-bold text-amber-600">{{ number_format($room->price_per_night) }}đ/đêm</span>
                        <a href="/detail/{{ $room->id }}" class="bg-gray-800 text-white font-medium px-4 py-2 rounded-lg text-xs hover:bg-gray-900 transition">Chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>