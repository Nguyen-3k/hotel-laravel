<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Phòng Khách Sạn - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('header')

    <main class="container mx-auto py-12 px-4 max-w-3xl flex-grow">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6 md:p-8">
            
            <div>
                <h2 class="text-2xl font-black text-gray-900 mb-6 border-b pb-3 text-center">Form Đăng Ký Đặt Phòng</h2>
                
                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Lỗi đặt phòng!</p>
                    <p>{{ session('error') }}</p>
                </div>
                @endif
                
                <form action="/checkout" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Chọn phòng bạn muốn đặt:</label>
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
                            <label class="block text-sm font-bold text-gray-700 mb-1">Họ và tên người đặt: <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="fullname" placeholder="Nhập tên người đặt" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Số điện thoại liên hệ: <span class="text-red-500">*</span></label>
                            <input type="tel" name="customer_phone" placeholder="Nhập số điện thoại" pattern="^0[0-9]{9}$" title="Vui lòng nhập đúng 10 số, bắt đầu bằng số 0" maxlength="10" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Địa chỉ Gmail (Không bắt buộc):</label>
                        <input type="email" name="customer_email" placeholder="Nhập gmail" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700">
                        <p class="text-xs text-gray-400 mt-1 italic">* Dùng để nhận hóa đơn điện tử (Chỉ chấp nhận đuôi @gmail.com)</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ngày nhận phòng (Check-in):</label>
                            <div class="relative">
                                <input type="text" name="check_in_date" id="checkin" placeholder="Chọn ngày đến" class="w-full border border-gray-300 rounded-xl p-3 pr-10 bg-gray-50 cursor-pointer" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-xl">📅</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ngày trả phòng (Check-out):</label>
                            <div class="relative">
                                <input type="text" name="check_out_date" id="checkout" placeholder="Chọn ngày đi" class="w-full border border-gray-300 rounded-xl p-3 pr-10 bg-gray-50 cursor-pointer" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-xl">📅</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-xl transition mt-6 shadow-md shadow-emerald-700/20">
                        🚀 TIẾP TỤC ĐẾN BƯỚC THANH TOÁN
                    </button>
                </form>
            </div>
            
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.getElementById('roomSelect');
            const checkinInput = document.getElementById('checkin');
            const checkoutInput = document.getElementById('checkout');

            // --- CHỈ GIỮ LẠI LOGIC LỊCH FLATPICKR ---
            const bookedDatesData = @json($bookedDates ?? []);
            let checkInPicker, checkOutPicker;

            function initDatePickers(roomId) {
                const disableDates = roomId ? (bookedDatesData[roomId] || []) : [];
                
                const config = {
                    locale: "vn",
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    disable: disableDates
                };

                if(checkInPicker) checkInPicker.destroy();
                if(checkOutPicker) checkOutPicker.destroy();

                checkInPicker = flatpickr(checkinInput, {
                    ...config,
                    onChange: function(selectedDates, dateStr, instance) {
                        checkOutPicker.set('minDate', dateStr);
                    }
                });

                checkOutPicker = flatpickr(checkoutInput, config);
            }

            // Xử lý khi chọn phòng khác
            roomSelect.addEventListener('change', function() {
                if(this.value) {
                    initDatePickers(this.value);
                }
                checkinInput.value = '';
                checkoutInput.value = '';
            });

            // Luôn khởi tạo lịch ngay khi trang load
            initDatePickers(roomSelect.value);
            
        });
    </script>
</body>
</html>