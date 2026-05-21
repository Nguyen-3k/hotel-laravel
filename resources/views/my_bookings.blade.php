<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử giao dịch - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('header')

    <main class="container mx-auto py-10 px-4 flex-grow flex flex-col md:flex-row gap-8 max-w-6xl">
        
        <aside class="w-full md:w-1/4 bg-white rounded-2xl shadow-md p-6 h-fit border-t-4 border-emerald-600">
            <div class="flex flex-col items-center mb-6 border-b pb-6">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=047857&color=fff&size=100&rounded=true&bold=true" class="w-24 h-24 rounded-full border-4 border-emerald-100 mb-3 shadow-sm">
                <h3 class="text-lg font-black text-gray-900">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-500">Thành viên Thiên Ân</p>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="/profile" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-emerald-700 font-medium rounded-xl transition">
                    <span>👤</span> Hồ sơ của tôi
                </a>
                <a href="/my-bookings" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl transition">
                    <span>📜</span> Lịch sử giao dịch
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-emerald-700 font-medium rounded-xl transition opacity-50 cursor-not-allowed">
                    <span>💰</span> Hoàn tiền (Sắp ra mắt)
                </a>
            </nav>
        </aside>

        <div class="w-full md:w-3/4">
            <h2 class="text-2xl font-black text-gray-900 mb-6">Lịch Sử Giao Dịch Của Bạn</h2>

            <div class="space-y-6">
                @forelse($bookings as $booking)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:shadow-md transition">
                        
                        <div class="flex gap-4 items-start">
                            <div class="bg-emerald-100 text-emerald-800 w-16 h-16 rounded-xl flex items-center justify-center font-black text-xl shrink-0">
                                P.{{ $booking->room->room_number ?? 'X' }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg mb-1">Mã đơn: #{{ $booking->id }}</h4>
                                <p class="text-sm text-gray-500">Ngày đặt: {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm font-medium text-gray-700 mt-2">
                                    📅 Lưu trú: {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end w-full md:w-auto mt-4 md:mt-0 border-t md:border-t-0 pt-4 md:pt-0 border-gray-100">
                            <p class="text-xl font-black text-amber-600 mb-2">{{ number_format($booking->total_price) }}đ</p>
                            
                            @if($booking->status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-1.5 rounded-full text-xs font-bold border border-yellow-200">⏳ Đang chờ xác nhận</span>
                            @elseif($booking->status == 'confirmed')
                                <span class="bg-green-100 text-green-800 px-4 py-1.5 rounded-full text-xs font-bold border border-green-200">✅ Đã xác nhận / Đang ở</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-4 py-1.5 rounded-full text-xs font-bold border border-gray-200">❌ Đã hủy / Trả phòng</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa có giao dịch nào</h3>
                        <p class="text-gray-500 mb-6">Bạn chưa thực hiện bất kỳ đặt phòng nào tại Thiên Ân Hotel.</p>
                        <a href="/rooms" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-6 rounded-xl transition">
                            Xem phòng ngay
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

</body>
</html>