<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Cọc - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    @include('header')

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col md:flex-row">
            
            <div class="bg-emerald-800 text-white p-8 md:w-1/2 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-black mb-2">✨ THIÊN ÂN HOTEL</h2>
                    <p class="text-emerald-200 mb-8">Cổng thanh toán tự động</p>
                    
                    <div class="space-y-4">
                        <div class="border-b border-emerald-600 pb-4">
                            <p class="text-sm text-emerald-300">Mã đơn hàng</p>
                            <p class="text-xl font-bold">#{{ $booking->id }}</p>
                        </div>
                        <div class="border-b border-emerald-600 pb-4">
                            <p class="text-sm text-emerald-300">Khách hàng</p>
                            <p class="text-lg font-bold">{{ $booking->customer_name }}</p>
                            <p class="text-sm">{{ $booking->customer_phone }}</p>
                        </div>
                        <div class="border-b border-emerald-600 pb-4">
                            <p class="text-sm text-emerald-300">Lưu trú</p>
                            <p class="font-bold">Nhận: {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</p>
                            <p class="font-bold">Trả: {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 bg-emerald-900 rounded-xl p-4 border border-emerald-700">
                    <p class="text-sm text-emerald-300">Tổng tiền phòng</p>
                    <p class="text-xl line-through text-gray-400">{{ number_format($booking->total_price) }} đ</p>
                    <p class="text-sm text-emerald-300 mt-2">Cần thanh toán (Cọc 30%)</p>
                    <p class="text-3xl font-black text-yellow-400">{{ number_format($deposit) }} VNĐ</p>
                </div>
            </div>

            <div class="p-8 md:w-1/2 flex flex-col items-center justify-center text-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Quét mã để thanh toán</h3>
                <p class="text-sm text-gray-500 mb-6">Sử dụng App ngân hàng hoặc Momo/ZaloPay để quét mã QR dưới đây.</p>
                
                <div class="bg-white p-4 rounded-2xl shadow-lg border-2 border-emerald-500 mb-6 relative">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-emerald-400/20 to-transparent h-12 animate-[scan_2s_ease-in-out_infinite]"></div>
                    <img src="{{ $qrUrl }}" alt="Mã QR Thanh Toán" class="w-64 h-64 object-contain relative z-10">
                </div>

                <p class="text-xs text-gray-400 mb-6">Nội dung CK: <strong class="text-gray-800">Thanh toan coc don {{ $booking->id }}</strong></p>

                <form action="/payment/{{ $booking->id }}/confirm" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition transform hover:scale-105">
                        ✅ TÔI ĐÃ CHUYỂN KHOẢN THÀNH CÔNG
                    </button>
                </form>
                <a href="/" class="text-xs text-gray-400 mt-4 underline cursor-pointer hover:text-gray-600">Hủy giao dịch & Quay lại trang chủ</a>
            </div>

        </div>
    </main>

    <style>
        @keyframes scan {
            0% { top: 0; }
            50% { top: 80%; }
            100% { top: 0; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingId = {{ $booking->id }};
            let checkInterval;

            function checkStatus() {
                fetch(`/payment/${bookingId}/status`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_paid) {
                            clearInterval(checkInterval);
                            
                            // Tạo hiệu ứng giật nảy màn hình khi nhận tiền thành công
                            document.body.innerHTML += `
                                <div class="fixed inset-0 bg-black/80 z-50 flex flex-col items-center justify-center text-white">
                                    <div class="text-6xl mb-4 animate-bounce">🎉</div>
                                    <h2 class="text-3xl font-black text-emerald-400 mb-2">THANH TOÁN THÀNH CÔNG!</h2>
                                    <p>Hệ thống đã nhận được tiền cọc. Đang tự động chuyển hướng...</p>
                                </div>
                            `;
                            
                            // Chuyển hướng về trang lịch sử đặt phòng sau 2.5s
                            setTimeout(() => {
                                window.location.href = '/my-bookings';
                            }, 2500);
                        }
                    })
                    .catch(error => console.error('Lỗi khi kiểm tra thanh toán:', error));
            }

            // Liên tục gọi hỏi backend 3 giây 1 lần
            checkInterval = setInterval(checkStatus, 3000);
        });
    </script>
</body>
</html>