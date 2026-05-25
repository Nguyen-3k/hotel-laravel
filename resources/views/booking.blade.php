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
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6 md:p-8 relative">
            
            <div>
                <h2 class="text-2xl font-black text-gray-900 mb-6 border-b pb-3 text-center">Form Đăng Ký Đặt Phòng</h2>
                
                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Lỗi đặt phòng!</p>
                    <p>{{ session('error') }}</p>
                </div>
                @endif
                
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
                        <p class="font-bold">⚠️ Vui lòng kiểm tra lại thông tin:</p>
                        <ul class="list-disc pl-5 text-sm mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form id="checkoutForm" action="/checkout" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" id="hidden_surcharge" name="surcharge" value="0">
                    <input type="hidden" id="hidden_total" name="calculated_total" value="0">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Chọn phòng bạn muốn đặt:</label>
                        <select name="room_id" id="roomSelect" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required onchange="updateRoomConfig()">
                            <option value="">-- Click chọn số phòng --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}" data-type="{{ mb_strtolower($r->room_type) }}" data-price="{{ $r->price_per_night }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>
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

                    <div class="grid grid-cols-2 gap-4 bg-emerald-50 p-4 rounded-xl border border-emerald-100 mt-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Số lượng phòng:</label>
                            <input type="number" id="room_count" name="room_count" min="1" value="1" class="w-full border border-emerald-300 rounded-lg p-2 text-sm focus:outline-emerald-700" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Số lượng người ở:</label>
                            <input type="number" id="guest_count" name="guest_count" min="1" value="1" class="w-full border border-emerald-300 rounded-lg p-2 text-sm focus:outline-emerald-700" required>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-bold text-emerald-800" id="rule_text">💡 Vui lòng chọn phòng để xem quy định số người.</p>
                        </div>
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

                    <button type="button" onclick="openConfirmModal()" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-xl transition mt-6 shadow-md shadow-emerald-700/20 text-lg">
                        🚀 ĐẾN BƯỚC THANH TOÁN
                    </button>
                </form>
            </div>
            
        </div>
    </main>

    <div id="confirmModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 transform transition-all scale-95 opacity-0" id="modalContent">
            
            <div class="text-center mb-4 border-b pb-4">
                <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl">📝</div>
                <h3 class="text-xl font-black text-gray-900">Xác nhận đơn đặt phòng</h3>
                <p class="text-sm text-gray-500">Vui lòng kiểm tra kỹ chi phí trước khi thanh toán</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm border border-gray-200">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 font-medium">Khách hàng:</span>
                    <span class="font-bold text-gray-800" id="modal_name"></span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 font-medium">Số lượng:</span>
                    <span class="font-bold text-emerald-700"><span id="modal_rooms"></span> phòng / <span id="modal_guests"></span> người</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 font-medium">Thời gian lưu trú:</span>
                    <span class="font-bold text-gray-800"><span id="modal_days"></span> đêm</span>
                </div>
                <div class="flex justify-between mb-3 text-red-600 font-bold hidden bg-red-50 p-2 rounded border border-red-100" id="surcharge_box">
                    <span>Phụ thu vượt người:</span>
                    <span id="modal_surcharge">0đ</span>
                </div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-200 border-dashed mt-2 items-center">
                    <span class="font-bold text-gray-800 uppercase tracking-wider">Tổng cộng:</span>
                    <span class="font-black text-2xl text-red-600" id="modal_total"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 rounded-xl transition">
                    Quay lại sửa
                </button>
                <button type="button" onclick="submitFinalForm()" class="w-1/2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition shadow-md">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>

    <script>
        // ==========================================
        // CẤU HÌNH QUY ĐỊNH
        // ==========================================
        let currentPrice = 0;
        let isSingle = true;
        const surchargeRate = 150000; // Phụ thu 150k

        function updateRoomConfig() {
            const select = document.getElementById('roomSelect');
            if(!select.value) return;
            
            const option = select.options[select.selectedIndex];
            currentPrice = parseInt(option.getAttribute('data-price'));
            const type = option.getAttribute('data-type');
            
            isSingle = type.includes('đơn');
            
            const base = isSingle ? 1 : 2;
            const max = isSingle ? 2 : 3;
            
            document.getElementById('rule_text').innerHTML = `💡 Quy định: Phòng này tiêu chuẩn <b>${base} người</b>, tối đa <b>${max} người</b>. Phụ thu <b>${surchargeRate.toLocaleString()}đ</b> cho người vượt quá tiêu chuẩn.`;

            // Gọi hàm tính toán lại giới hạn người khi đổi loại phòng
            checkGuestLimit();
        }

        // HÀM MỚI: KIỂM TRA VÀ CHẶN SỐ LƯỢNG NGƯỜI REAL-TIME
        function checkGuestLimit() {
            const roomInput = document.getElementById('room_count');
            const guestInput = document.getElementById('guest_count');

            let rooms = parseInt(roomInput.value) || 1;
            let guests = parseInt(guestInput.value) || 1;

            const maxPax = isSingle ? 2 : 3;
            const maxAllowed = rooms * maxPax;

            // Set cứng thuộc tính max cho ô input để giới hạn mũi tên click
            guestInput.max = maxAllowed;

            // Nếu khách cố tình gõ phím số lớn hơn giới hạn
            if (guests > maxAllowed) {
                alert(`⚠️ VƯỢT QUÁ GIỚI HẠN!\n\nVới ${rooms} phòng bạn chọn, hệ thống chỉ cho phép lưu trú tối đa ${maxAllowed} người (Quy định: ${maxPax} người/phòng).\n\nVui lòng TĂNG SỐ LƯỢNG PHÒNG lên nếu bạn đi đông người hơn!`);
                guestInput.value = maxAllowed; // Tự động ép số về mức tối đa
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.getElementById('roomSelect');
            const checkinInput = document.getElementById('checkin');
            const checkoutInput = document.getElementById('checkout');

            // --- Lắng nghe sự kiện gõ phím/click ở ô Số lượng ---
            document.getElementById('room_count').addEventListener('input', checkGuestLimit);
            document.getElementById('guest_count').addEventListener('input', checkGuestLimit);

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

            roomSelect.addEventListener('change', function() {
                if(this.value) {
                    initDatePickers(this.value);
                }
                checkinInput.value = '';
                checkoutInput.value = '';
            });

            initDatePickers(roomSelect.value);
            updateRoomConfig(); // Cập nhật text quy định nếu đã chọn phòng sẵn
        });

        // ==========================================
        // LOGIC MODAL VÀ TÍNH TIỀN
        // ==========================================
        function openConfirmModal() {
            const form = document.getElementById('checkoutForm');
            
            // Ép form tự kiểm tra lỗi (VD: SĐT chưa đủ 10 số, thiếu tên).
            // Nếu có lỗi, trình duyệt sẽ báo đỏ và return luôn, KHÔNG bật Modal.
            if (!form.reportValidity()) {
                return;
            }

            const select = document.getElementById('roomSelect');
            if(!select.value) { alert('Vui lòng chọn phòng!'); return; }

            const name = document.getElementById('fullname').value;
            const phone = document.querySelector('input[name="customer_phone"]').value;
            const checkIn = document.getElementById('checkin').value;
            const checkOut = document.getElementById('checkout').value;
            const rooms = parseInt(document.getElementById('room_count').value);
            const guests = parseInt(document.getElementById('guest_count').value);

            if(!name || !phone || !checkIn || !checkOut || !rooms || !guests) {
                alert('Vui lòng điền đầy đủ các thông tin bắt buộc (*)!');
                return;
            }

            // Tính số ngày ở
            const inDate = new Date(checkIn);
            const outDate = new Date(checkOut);
            let days = Math.round((outDate - inDate) / (1000 * 60 * 60 * 24));
            if(days <= 0 || isNaN(days)) days = 1;

            // Tính quy định số người
            const basePax = isSingle ? 1 : 2;
            const maxPax = isSingle ? 2 : 3;
            const baseAllowed = rooms * basePax;
            const maxAllowed = rooms * maxPax;

            // Lớp bảo vệ thứ 2 trước khi mở Modal
            if (guests > maxAllowed) {
                alert(`LỖI: ${rooms} phòng này chỉ cho phép tối đa ${maxAllowed} người lưu trú.\nVui lòng tăng số lượng phòng hoặc giảm số lượng người.`);
                return;
            }

            // Tính phụ thu
            let surcharge = 0;
            if (guests > baseAllowed) {
                let extraPeople = guests - baseAllowed;
                surcharge = extraPeople * surchargeRate;
            }

            // Tính tổng tiền = (Giá phòng * số phòng * số ngày) + Phụ thu
            let total = (currentPrice * rooms * days) + surcharge;

            // Đổ dữ liệu ra Modal
            document.getElementById('modal_name').innerText = name;
            document.getElementById('modal_rooms').innerText = rooms;
            document.getElementById('modal_guests').innerText = guests;
            document.getElementById('modal_days').innerText = days;
            
            if(surcharge > 0) {
                document.getElementById('surcharge_box').classList.remove('hidden');
                document.getElementById('modal_surcharge').innerText = surcharge.toLocaleString() + 'đ';
            } else {
                document.getElementById('surcharge_box').classList.add('hidden');
            }
            
            document.getElementById('modal_total').innerText = total.toLocaleString() + 'đ';

            // Lưu tiền vào form ẩn để gửi lên Controller
            document.getElementById('hidden_surcharge').value = surcharge;
            document.getElementById('hidden_total').value = total;

            // Hiển thị Modal
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        function submitFinalForm() {
            document.getElementById('checkoutForm').submit();
        }
    </script>
</body>
</html>