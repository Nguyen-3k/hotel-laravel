<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Phòng Khách Sạn - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-emerald-800 text-white py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="/" class="text-xl font-bold tracking-wide">✨ THIÊN ÂN HOTEL</a>
            <nav class="space-x-6 font-medium">
                <a href="/" class="hover:text-yellow-400">Trang chủ</a>
                <a href="/rooms" class="hover:text-yellow-400">Danh sách phòng</a>
                <a href="/booking" class="hover:text-yellow-400 border-b-2 border-yellow-400 pb-1">Đặt phòng</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-4 max-w-5xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-5 gap-8 p-6 md:p-8">
            
            <div class="md:col-span-3">
                <h2 class="text-2xl font-black text-gray-900 mb-6 border-b pb-3">Form Đăng Ký Đặt Phòng</h2>
                @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Lỗi đặt phòng!</p>
                <p>{{ session('error') }}</p>
            </div>
                @endif
                <form action="/checkout" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Chọn phòng còn trống:</label>
                        <select name="room_id" id="roomSelect" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required>
                            <option value="">-- Click chọn số phòng --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}" data-price="{{ $r->price_per_night }}" data-name="P.{{ $r->room_number }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>
                                    Phòng {{ $r->room_number }} - Hạng {{ $r->room_type }} ({{ number_format($r->price_per_night) }}đ/đêm)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Họ và tên người đặt:</label>
                            <input type="text" name="customer_name" id="fullname" placeholder="Nhập tên tiếng Việt" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Số điện thoại liên hệ:</label>
                            <input type="tel" name="customer_phone" placeholder="Nhập SĐT để gọi xác nhận" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ngày nhận phòng (Check-in):</label>
                            <input type="date" name="check_in_date" id="checkin" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ngày trả phòng (Check-out):</label>
                            <input type="date" name="check_out_date" id="checkout" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-xl transition mt-6 shadow-md shadow-emerald-700/20">🚀 XÁC NHẬN ĐẶT PHÒNG HỆ THỐNG</button>
                </form>
            </div>

            <div class="md:col-span-2 bg-gray-50 rounded-xl p-5 border border-gray-100 flex flex-col items-center justify-center text-center">
                <h3 class="font-bold text-gray-800 mb-4">Thông tin chuyển khoản cọc (30%)</h3>
                
                <div class="mb-4">
                    <span class="text-sm text-gray-500">Số tiền cọc tạm tính:</span>
                    <div id="depositAmount" class="text-2xl font-black text-emerald-700 mt-1">0 VNĐ</div>
                </div>

                <div class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center shadow-inner overflow-hidden mb-4">
                    <img id="qrCodeImg" src="" alt="Mã QR VietQR" class="w-full h-full object-contain hidden">
                    <span id="qrPlaceholder" class="text-xs text-gray-400 p-4">Hãy chọn phòng và ngày đặt để hiển thị mã quét QR thanh toán tự động.</span>
                </div>

                <p class="text-xs text-gray-400 italic">Nội dung CK: <span id="transferContent" class="font-bold text-gray-700 not-italic">(Trống)</span></p>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.getElementById('roomSelect');
            const fullnameInput = document.getElementById('fullname');
            const checkinInput = document.getElementById('checkin');
            const checkoutInput = document.getElementById('checkout');
            const depositAmountEl = document.getElementById('depositAmount');
            const transferContentEl = document.getElementById('transferContent');
            const qrCodeImgEl = document.getElementById('qrCodeImg');
            const qrPlaceholder = document.getElementById('qrPlaceholder');

            function updateQR() {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value || !checkinInput.value || !checkoutInput.value) {
                    depositAmountEl.innerText = '0 VNĐ';
                    transferContentEl.innerText = '(Vui lòng điền đủ thông tin)';
                    qrCodeImgEl.classList.add('hidden');
                    qrPlaceholder.classList.remove('hidden');
                    return;
                }

                const price = parseInt(selectedOption.getAttribute('data-price')) || 0;
                const roomName = selectedOption.getAttribute('data-name') || 'Phong';
                
                // Tính toán số ngày trực tiếp bằng JS
                const d1 = new Date(checkinInput.value);
                const d2 = new Date(checkoutInput.value);
                const timeDiff = d2.getTime() - d1.getTime();
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24)) || 1;

                if(nights <= 0) {
                    depositAmountEl.innerText = 'Ngày không hợp lệ';
                    return;
                }

                const deposit = (price * nights) * 0.3;
                depositAmountEl.innerText = deposit.toLocaleString('vi-VN') + ' VNĐ';

                const userName = fullnameInput.value.trim() !== '' ? fullnameInput.value.trim() : 'Khach';
                const rawDescription = `Coc ${roomName} khach ${userName}`;
                transferContentEl.innerText = rawDescription;

                // Tạo link VietQR động kết nối ngân hàng quân đội MB của bạn
                qrCodeImgEl.src = `https://img.vietqr.io/image/MB-970422920653491427-compact.png?amount=${deposit}&addInfo=${encodeURIComponent(rawDescription)}&accountName=VI%20CONG%20NGUYEN`;
                qrCodeImgEl.classList.remove('hidden');
                qrPlaceholder.classList.add('hidden');
            }

            roomSelect.addEventListener('change', updateQR);
            fullnameInput.addEventListener('input', updateQR);
            checkinInput.addEventListener('change', updateQR);
            checkoutInput.addEventListener('change', updateQR);

            if(roomSelect.value) updateQR(); // Cập nhật luôn nếu có phòng được chọn trước
        });
    </script>
</body>
</html>