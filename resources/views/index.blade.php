<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiên Ân Hotel - Trang Chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">

    @include('header')

<section class="bg-gray-900/50 text-white h-[50vh] min-h-[300px] flex items-center justify-center text-center px-4 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1200')] bg-cover bg-center bg-blend-overlay">
        
        <div class="text-center z-10 relative px-4">
            
            <h1 class="text-5xl md:text-5xl font-bold text-white mb-6 font-['Playfair_Display'] drop-shadow-[0_5px_5px_rgba(0,0,0,0.8)] tracking-wide italic">
                Thiên Ân Hotel
            </h1>
        
            <p class="text-lg md:text-1xl text-emerald-50 font-medium drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] max-w-2xl mx-auto mb-8">
                Trải nghiệm không gian nghỉ dưỡng đẳng cấp và ấm cúng giữa lòng đại ngàn.
            </p>
        
            <a href="/rooms" class="inline-block bg-green-500 hover:bg-yellow-400 text-gray-900 font-black py-4 px-8 rounded-xl transition shadow-lg transform hover:-translate-y-1">
                ĐẶT PHÒNG NGAY
            </a>
            
        </div>
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
            <div class="flex-grow">
        <label class="block text-xs font-bold text-gray-700 mb-1">Cấu hình giường</label>
        <div class="relative">
            <select name="bed_type" class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-emerald-600 appearance-none cursor-pointer">
                <option value="">Tất cả cấu hình</option>
                <option value="1 giường đơn">1 Giường Đơn (Tối đa 2 người)</option>
                <option value="1 giường đôi">1 Giường Đôi (Tối đa 3 người)</option>
                <option value="2 giường đơn">2 Giường Đơn (Tối đa 4 người)</option>
                <option value="2 giường đôi">2 Giường Đôi (Tối đa 6 người)</option>
                <option value="1 giường đôi 1 giường đơn">1 Đôi + 1 Đơn (Tối đa 5 người)</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <span class="text-sm">🛏️</span>
            </div>
        </div>
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
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition duration-300 flex flex-col justify-between group">
                    
                    <div class="p-4 pb-0">
                        <div class="w-full h-56 rounded-xl overflow-hidden relative shadow-sm">
                            @if($room->image_url)
                                <img src="{{ Str::startsWith($room->image_url, 'http') ? $room->image_url : asset($room->image_url) }}" alt="{{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            @else
                                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500" alt="{{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            @endif
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-center mb-3">
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">Loại: {{ $room->room_type }}</span>
                            <span class="text-sm font-semibold text-gray-500">Phòng: {{ $room->room_number }}</span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Phòng Nghỉ Dưỡng Hạng {{ $room->room_type }}</h3>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4 flex-grow">{{ $room->description ?? 'Không gian rộng rãi thoáng mát đầy đủ tiện nghi, wifi căng đét.' }}</p>
                    </div>
                    
                    <div class="px-6 pb-6 pt-0 border-t-0 flex items-center justify-between mt-auto">
                        <span class="text-lg font-black text-amber-600">{{ number_format($room->price_per_night) }}đ<small class="text-gray-500 font-normal">/đêm</small></span>
                        <a href="/detail/{{ $room->id }}" class="bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm hover:bg-emerald-800 shadow-md transition transform hover:-translate-y-0.5">Xem chi tiết</a>
                    </div>
                    
                </div>
            @empty
                <p class="text-center text-gray-500 col-span-3 py-8">Hiện tại hệ thống chưa cập nhật phòng mẫu nào.</p>
            @endforelse
        </div>
        </main>
    @include('footer')
</body>
</html>