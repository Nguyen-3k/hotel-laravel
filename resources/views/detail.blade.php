<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Phòng - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    
    @include('header')

    <main class="container mx-auto py-12 px-4 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
            <div>
                <img src="{{ $room->image_url ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500' }}" alt="{{ $room->room_number }}" class="w-full rounded-xl object-cover h-80 shadow-md">
            </div>
            <div class="flex flex-col justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Phòng Số: {{ $room->room_number }}</h1>
                    <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full mb-6">Hạng phòng: {{ $room->room_type }}</span>
                    
                    <h3 class="font-bold text-gray-700 mb-2">📄 Mô tả phòng:</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $room->description ?? 'Phòng đầy đủ tiện ích cơ bản, có điều hòa nhiệt độ, nóng lạnh, giường đệm cao cấp êm ái giúp bạn có giấc ngủ sâu.' }}</p>
                    
                    <div class="text-2xl font-black text-amber-600 mb-4">
                        {{ number_format($room->price_per_night) }} VNĐ <span class="text-sm font-normal text-gray-400">/ một đêm</span>
                    </div>
                </div>

                <a href="/booking?room_id={{ $room->id }}" class="block text-center bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 shadow-md hover:shadow-lg transition">📅 ĐẶT PHÒNG NGAY</a>
            </div>
        </div>
    </main>
</body>
</html>