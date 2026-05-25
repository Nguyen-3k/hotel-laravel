<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Phòng - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    
    @include('header')

    <main class="container mx-auto py-12 px-4 flex-grow">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(isset($searchMessage))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-medium text-center">
                🔍 {{ $searchMessage }}
            </div>
        @endif

        <h2 class="text-3xl font-black text-gray-900 mb-6 text-center md:text-left">Danh Sách Tất Cả Các Phòng</h2>
        
        <div class="flex flex-wrap justify-center md:justify-start mb-8 gap-3">
            <button onclick="filterRooms('all')" id="btn-all" class="px-6 py-2.5 rounded-full font-bold text-sm bg-emerald-800 text-white shadow-md transition transform hover:-translate-y-0.5">
                Tất cả phòng
            </button>
            <button onclick="filterRooms('single')" id="btn-single" class="px-6 py-2.5 rounded-full font-bold text-sm bg-gray-200 text-gray-700 hover:bg-emerald-600 hover:text-white shadow-sm transition transform hover:-translate-y-0.5">
                🛏️ Giường Đơn
            </button>
            <button onclick="filterRooms('double')" id="btn-double" class="px-6 py-2.5 rounded-full font-bold text-sm bg-gray-200 text-gray-700 hover:bg-emerald-600 hover:text-white shadow-sm transition transform hover:-translate-y-0.5">
                🛌 Giường Đôi
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" id="roomContainer">
            @foreach($rooms as $room)
                @php
                    // Kiểm tra xem chữ "đơn" có nằm trong loại phòng không (dùng mb_strtolower để chống lỗi font tiếng Việt)
                    $isSingle = str_contains(mb_strtolower($room->room_type), 'đơn');
                    $roomClass = $isSingle ? 'type-single' : 'type-double';
                    $maxPax = $isSingle ? 2 : 3;
                @endphp

                <div class="room-card {{ $roomClass }} bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col justify-between group">
                    <div>
                        <div class="w-full h-72 overflow-hidden relative">
                            @if($room->image_url)
                                @if(Str::startsWith($room->image_url, 'http'))
                                    <img src="{{ $room->image_url }}" alt="{{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                @else
                                    <img src="{{ asset($room->image_url) }}" alt="{{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                @endif
                            @else
                                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500" alt="{{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @endif
                            
                            <div class="absolute top-3 right-3 bg-black/75 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full border border-gray-600 shadow-lg flex items-center gap-1.5">
                                <span>👤</span> Tối đa: {{ $maxPax }} người
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Phòng {{$room->room_number}} - Hạng {{$room->room_type}}</h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $room->description }}</p>
                        </div>
                    </div>
                    <div class="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50">
                        <span class="text-md font-black text-red-600">{{ number_format($room->price_per_night) }}đ <span class="text-xs text-gray-500 font-normal">/ đêm</span></span>
                        <a href="/detail/{{ $room->id }}" class="bg-emerald-700 text-white font-bold px-5 py-2 rounded-lg text-sm hover:bg-emerald-800 transition shadow-md">Chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
    
    @include('footer')

    <script>
        function filterRooms(type) {
            let cards = document.querySelectorAll('.room-card');
            
            // Xử lý đổi màu Nút bấm cho đẹp
            const buttons = ['all', 'single', 'double'];
            buttons.forEach(t => {
                let btn = document.getElementById('btn-' + t);
                if (t === type) {
                    btn.classList.remove('bg-gray-200', 'text-gray-700');
                    btn.classList.add('bg-emerald-800', 'text-white', 'shadow-md');
                } else {
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                    btn.classList.remove('bg-emerald-800', 'text-white', 'shadow-md');
                }
            });

            // Xử lý Ẩn/Hiện Card phòng (Lưu ý: Card đang dùng flex nên phải trả về 'flex' chứ không phải 'block')
            cards.forEach(card => {
                if (type === 'all') {
                    card.style.display = 'flex';
                } else if (type === 'single') {
                    card.style.display = card.classList.contains('type-single') ? 'flex' : 'none';
                } else if (type === 'double') {
                    card.style.display = card.classList.contains('type-double') ? 'flex' : 'none';
                }
            });
        }
    </script>
</body>
</html>