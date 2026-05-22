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
            <div class="flex flex-col items-center mb-6 border-b pb-6 relative group">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" class="w-24 h-24 rounded-full border-4 border-emerald-100 mb-3 shadow-sm object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=047857&color=fff&size=100&rounded=true&bold=true" class="w-24 h-24 rounded-full border-4 border-emerald-100 mb-3 shadow-sm">
                @endif
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
            </nav>
        </aside>

        <div class="w-full md:w-3/4 bg-white rounded-2xl shadow-md p-8">
            <h2 class="text-2xl font-black text-gray-900 mb-6 pb-4 border-b">Lịch Sử Giao Dịch Của Bạn</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-bold">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-bold">⚠️ {{ session('error') }}</div>
            @endif

            @forelse($bookings as $booking)
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mb-4 hover:shadow-md transition duration-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-4 mb-4">
                        <div>
                            <span class="text-xs text-gray-400 font-mono uppercase tracking-wider">Mã đơn hàng</span>
                            <h4 class="text-sm font-black text-gray-700">#{{ $booking->id }}</h4>
                        </div>
                        
                        <div>
                            @if($booking->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1.5 rounded-full border border-yellow-200 flex items-center gap-1">
                                    ⏳ Chờ quản trị viên duyệt
                                </span>
                            @elseif($booking->status === 'confirmed')
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200 flex items-center gap-1">
                                    ✅ Đặt phòng thành công
                                </span>
                            @elseif($booking->status === 'refund_pending')
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-200 flex items-center gap-1">
                                    🔄 Đang chờ hoàn tiền
                                </span>
                            @elseif($booking->status === 'cancelled')
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1.5 rounded-full border border-red-200 flex items-center gap-1">
                                    ❌ Đơn hàng đã hủy
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-2">
                        <div>
                            <p class="text-gray-400 text-xs font-medium">Phòng lưu trú</p>
                            <p class="font-bold text-emerald-800 mt-0.5">P.{{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'Tiêu chuẩn' }})</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium">Thời gian nghỉ</p>
                            <p class="font-bold text-gray-700 mt-0.5">
                                {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} 
                                <span class="text-gray-400 font-normal">đến</span> 
                                {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-gray-400 text-xs font-medium">Tổng chi phí</p>
                            <p class="font-black text-lg text-red-600 mt-0.5">{{ number_format($booking->total_price) }}đ</p>
                        </div>
                    </div>

                    @if($booking->status === 'confirmed')
                        <div class="text-right mt-4 border-t pt-4">
                            <button onclick="toggleRefundForm({{ $booking->id }})" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 font-bold py-2 px-4 rounded-lg transition cursor-pointer">
                                💸 Yêu cầu hoàn tiền
                            </button>
                        </div>
                        
                        <form id="refundForm-{{ $booking->id }}" action="/my-bookings/{{ $booking->id }}/refund" method="POST" enctype="multipart/form-data" class="hidden mt-4 bg-white p-5 rounded-xl border-2 border-red-100 shadow-inner text-left">
                            @csrf
                            <h5 class="text-red-700 font-bold text-sm mb-3">Vui lòng cung cấp thông tin nhận tiền:</h5>
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Số TK - Tên chủ thẻ - Tên ngân hàng <span class="text-red-500">*</span></label>
                                <textarea name="bank_info" rows="2" placeholder="Ví dụ: 97042292... - NGUYEN VAN A - Vietcombank" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-red-500" required></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Tải ảnh mã QR nhận tiền (Nếu có):</label>
                                <input type="file" name="refund_qr" accept="image/*" class="w-full border border-gray-300 rounded-lg p-1.5 text-xs bg-gray-50">
                            </div>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg text-xs transition shadow-md w-full md:w-auto">
                                Xác nhận gửi yêu cầu
                            </button>
                        </form>
                    @endif

                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 text-gray-400 flex items-center justify-center rounded-full mx-auto mb-4 text-3xl">📭</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Chưa có giao dịch nào</h3>
                    <p class="text-gray-500 text-sm mb-6">Bạn chưa thực hiện bất kỳ đặt phòng nào bằng <span class="font-bold text-emerald-700">tài khoản này.</span></p>
                    <a href="/rooms" class="inline-block bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-6 rounded-xl transition shadow-sm text-sm">Xem phòng ngay</a>
                </div>
            @endforelse

        </div>
    </main>

    @include('footer')

    <script>
        function toggleRefundForm(id) {
            const form = document.getElementById('refundForm-' + id);
            if(form) {
                form.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>