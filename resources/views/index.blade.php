<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiên Ân Hotel - Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    @include('header')

    <section class="bg-emerald-900 text-white py-24 text-center px-4 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1200')] bg-cover bg-center bg-blend-overlay">
        <h1 class="text-4xl md:text-5xl font-black mb-4">Chào mừng đến với Thiên Ân Hotel</h1>
        <p class="text-lg text-emerald-100 max-w-xl mx-auto">Trải nghiệm dịch vụ nghỉ dưỡng cao cấp, không gian tinh tế và sang trọng bậc nhất.</p>
    </section>
    
    <div class="container mx-auto px-4 mt-14 mb-1">
        <form action="/search" method="GET" class="bg-white rounded-2xl shadow-xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-4 max-w-4xl mx-auto border-t-4 border-emerald-600">
            <div class="w-full md:w-2/5">
                <label class="block text-sm font-bold text-gray-700 mb-1">Ngày nhận phòng</label>
                <input type="date" name="check_in" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
            </div>
            <div class="w-full md:w-2/5">
                <label class="block text-sm font-bold text-gray-700 mb-1">Ngày trả phòng</label>
                <input type="date" name="check_out" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
            </div>
            <div class="w-full md:w-1/5 mt-4 md:mt-5">
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition h-12 flex items-center justify-center">
                    🔍 TÌM PHÒNG
                </button>
            </div>
        </form>
    </div>

    <main class="container mx-auto py-16 px-4">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-8 shadow-sm font-medium text-center max-w-3xl mx-auto">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">🏆 Phòng Nổi Bật</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($rooms as $room)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 flex flex-col justify-between">
                    <div class="w-full h-96 overflow-hidden">
                        <img src="{{ $room->image_url ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500' }}" alt="{{ $room->room_number }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">Loại: {{ $room->room_type }}</span>
                            <span class="text-sm font-semibold text-gray-500">Phòng: {{ $room->room_number }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Phòng Nghỉ Dưỡng Hạng {{ $room->room_type }}</h3>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ $room->description ?? 'Không gian rộng rãi thoáng mát đầy đủ tiện nghi, wifi căng đét.' }}</p>
                    </div>
                    <div class="px-6 pb-6 pt-2 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-lg font-black text-amber-600">{{ number_format($room->price_per_night) }}đ<small class="text-gray-500 font-normal">/đêm</small></span>
                        <a href="/detail/{{ $room->id }}" class="bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-emerald-800 transition">Xem chi tiết</a>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 col-span-3 py-8">Hiện tại hệ thống chưa cập nhật phòng mẫu nào.</p>
            @endforelse
        </div>
    </main>
</body>
</html>