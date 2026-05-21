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

        <h2 class="text-3xl font-black text-gray-900 mb-8">Danh Sách Tất Cả Các Phòng</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($rooms as $room)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="w-full h-96 overflow-hidden">
                            <img src="{{ $room->image_url ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500' }}" alt="{{ $room->room_number }}" class="w-full h-full object-cover">
                        </div>
                        
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Phòng {{$room->room_number}} - Hạng {{$room->room_type}}</h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $room->description }}</p>
                        </div>
                    </div>
                    <div class="p-5 border-t border-gray-50 flex items-center justify-between bg-gray-50">
                        <span class="text-md font-bold text-amber-600">{{ number_format($room->price_per_night) }}đ/đêm</span>
                        <a href="/detail/{{ $room->id }}" class="bg-gray-800 text-white font-medium px-4 py-2 rounded-lg text-sm hover:bg-gray-900 transition">Chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>