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

    <main class="container mx-auto py-12 px-4 max-w-5xl">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-4 mb-8 text-white flex flex-col md:flex-row justify-between items-center transform hover:scale-[1.01] transition duration-300">
            <div class="flex items-center gap-4">
                <span class="text-4xl">🎁</span>
                <div>
                    <h3 class="font-black text-xl">Ưu đãi khách hàng mới!</h3>
                    <p class="text-sm text-emerald-100">Giảm ngay 10% tổng hóa đơn đặt phòng cho lần đầu tiên.</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-2 bg-white/20 px-4 py-2 rounded-lg border border-white/30 backdrop-blur-sm">
                <span class="text-sm">Mã code:</span>
                <span class="font-mono font-black text-yellow-300 text-xl tracking-wider select-all">TAHOTELBANMOI</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
            
            <div class="flex flex-col gap-3">
                <div class="h-64 md:h-80 w-full">
                    @if(Str::startsWith($room->image_url, 'http'))
                        <img src="{{ $room->image_url }}" alt="Phòng {{ $room->room_number }}" class="w-full h-full object-cover rounded-xl shadow">
                    @else
                        <img src="{{ asset($room->image_url) }}" alt="Phòng {{ $room->room_number }}" class="w-full h-full object-cover rounded-xl shadow">
                    @endif            
                </div>
                
                @if(isset($room->images) && $room->images->count() > 0)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($room->images as $img)
                        <img src="{{ asset($img->image_url) }}" class="w-full h-20 object-cover rounded-lg shadow-sm hover:opacity-80 transition cursor-pointer border">
                    @endforeach
                </div>
                @endif
            </div>

            <div class="flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start">
                        <h1 class="text-3xl font-black text-gray-900 mb-2">Phòng Số: {{ $room->room_number }}</h1>
                        <div class="flex flex-col items-end">
                            <div class="flex text-yellow-400 text-lg">
                                @php $avg = round($room->averageRating()); @endphp
                                @for($i=1; $i<=5; $i++)
                                    <span>{{ $i <= $avg ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500">({{ $room->reviews->count() ?? 0 }} đánh giá)</span>
                        </div>
                    </div>

                    <div class="flex gap-2 mb-6 mt-2">
                        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-200">
                            Hạng: {{ $room->room_type }}
                        </span>
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1.5 rounded-full border border-blue-200">
                            👤 Tối đa: {{ str_contains(mb_strtolower($room->room_type), 'đơn') ? '2' : '3' }} người
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-gray-700 mb-2">📄 Mô tả phòng:</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $room->description ?? 'Phòng đầy đủ tiện ích cơ bản, có điều hòa nhiệt độ, nóng lạnh, giường đệm cao cấp êm ái giúp bạn có giấc ngủ sâu.' }}</p>
                    
                    <div class="text-2xl font-black text-amber-600 mb-4">
                        {{ number_format($room->price_per_night) }} VNĐ <span class="text-sm font-normal text-gray-400">/ một đêm</span>
                    </div>
                </div>

                <a href="/booking?room_id={{ $room->id }}" class="block text-center bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl hover:bg-emerald-800 shadow-md hover:shadow-lg transition">📅 ĐẶT PHÒNG NGAY</a>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-2xl shadow-md p-6 md:p-8">
            <h3 class="text-2xl font-black text-gray-900 mb-6 border-b pb-3">⭐ Đánh Giá Từ Khách Hàng</h3>
            
            @if(isset($room->reviews) && $room->reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($room->reviews as $review)
                    <div class="border-b border-gray-100 pb-4 last:border-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold">
                                {{ substr($review->user->name ?? 'K', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">{{ $review->user->name ?? 'Khách ẩn danh' }}</h4>
                                <div class="flex text-yellow-400 text-xs">
                                    @for($i=1; $i<=5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 ml-auto">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 text-sm pl-13">{{ $review->comment ?? 'Khách hàng không để lại bình luận.' }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <span class="text-3xl mb-2 block">💬</span>
                    <p>Chưa có đánh giá nào cho phòng này. Hãy là người đầu tiên trải nghiệm nhé!</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>