<header class="bg-emerald-800 text-white py-4 shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-4">
        
        <a href="/" class="text-xl font-bold tracking-wide">✨ THIÊN ÂN HOTEL</a>
        
        <nav class="flex items-center space-x-2 md:space-x-4 font-medium">
            <a href="/" class="hover:text-yellow-400 px-2 py-1 transition">Trang chủ</a>
            <a href="/rooms" class="hover:text-yellow-400 px-2 py-1 transition">Danh sách phòng</a>
            <a href="/booking" class="hover:text-yellow-400 px-2 py-1 transition">Đặt phòng</a>

            @guest
                <div class="border-l border-emerald-600 pl-4 ml-2 flex space-x-2">
                    <a href="/login" class="text-sm bg-white text-emerald-800 px-4 py-2 rounded-lg hover:bg-gray-100 font-bold shadow-sm transition">Đăng nhập</a>
                    <a href="/register" class="text-sm border border-emerald-400 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 font-bold transition">Đăng ký</a>
                </div>
            @endguest

            @auth
                <div class="flex items-center border-l border-emerald-600 pl-4 ml-2">
                    
                    <div class="relative mr-4">
                        @php
                            // Logic truy vấn thông báo: Admin lấy của admin (user_id = null), User lấy của user
                            $query = Auth::user()->role === 'admin' 
                                     ? App\Models\Notification::whereNull('user_id') 
                                     : App\Models\Notification::where('user_id', Auth::id());
                            
                            $unreadCount = (clone $query)->where('is_read', false)->count();
                            $notifications = $query->orderBy('created_at', 'desc')->take(5)->get();
                        @endphp

                        <button id="notiBtn" class="relative p-2 text-emerald-100 hover:text-white transition focus:outline-none cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if($unreadCount > 0)
                                <span id="unreadBadge" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-emerald-800">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div id="notiDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 hidden z-50">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="font-bold text-gray-800 text-sm">Thông báo mới</h4>
                                @if($unreadCount > 0)
                                    <span id="markAllReadBtn" class="text-xs text-emerald-600 cursor-pointer hover:underline font-bold">Đánh dấu đã đọc</span>
                                @else
                                    <span class="text-xs text-gray-400">Không có thông báo mới</span>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse($notifications as $noti)
                                    <div class="block px-4 py-3 border-b border-gray-50 transition {{ $noti->is_read ? 'bg-white opacity-70' : 'bg-emerald-50 unread-item' }}">
                                        <p class="text-sm text-gray-800"><span class="font-bold text-emerald-700">{{ $noti->title }}:</span> {{ $noti->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $noti->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">Chưa có thông báo nào.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>    

                    <div class="relative group flex items-center">
                        <button class="flex items-center gap-2 focus:outline-none cursor-pointer">
                            
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" 
                                     alt="Avatar" 
                                     class="w-9 h-9 rounded-full border-2 border-emerald-300 hover:border-yellow-400 transition shadow-sm object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=047857&color=fff&rounded=true&bold=true" 
                                     alt="Avatar" 
                                     class="w-9 h-9 rounded-full border-2 border-emerald-300 hover:border-yellow-400 transition shadow-sm object-cover">
                            @endif
                            
                            <span class="text-sm font-bold text-white hidden md:block">
                                {{ explode(' ', Auth::user()->name)[count(explode(' ', Auth::user()->name)) - 1] }}
                            </span>
                            
                            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div class="absolute right-0 top-full pt-3 w-56 hidden group-hover:block z-50">
                            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 transform origin-top-right transition-all duration-300">
                                
                                <div class="px-4 py-3 bg-emerald-50 border-b border-emerald-100">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                                        {{ Auth::user()->role === 'admin' ? 'Quản trị viên' : 'Khách hàng' }}
                                    </p>
                                    <p class="text-sm font-bold text-emerald-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-2">
                                    @if(Auth::user()->role === 'admin')
                                        <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-emerald-700 hover:bg-emerald-50 transition">
                                            <span>⚙️</span> Quản trị hệ thống
                                        </a>
                                    @else
                                        <a href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            <span>👤</span> Thông tin người dùng
                                        </a>
                                        
                                        <a href="/my-bookings" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            <span>📜</span> Danh sách giao dịch
                                        </a>
                                        
                                    @endif
                                </div>
                                
                                <div class="border-t border-gray-100 py-1 bg-gray-50">
                                    <form action="/logout" method="POST" class="block w-full">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-100 transition">
                                            <span>🚪</span> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
            
        </nav>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notiBtn = document.getElementById('notiBtn');
        const notiDropdown = document.getElementById('notiDropdown');
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        const unreadBadge = document.getElementById('unreadBadge');

        // Logic ẩn/hiện bảng thông báo
        if(notiBtn && notiDropdown) {
            notiBtn.addEventListener('click', function(e) {
                e.stopPropagation(); 
                notiDropdown.classList.toggle('hidden'); 
            });

            document.addEventListener('click', function(e) {
                if (!notiBtn.contains(e.target) && !notiDropdown.contains(e.target)) {
                    notiDropdown.classList.add('hidden');
                }
            });
        }

        // Logic đánh dấu đã đọc bằng AJAX
        if(markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Giữ bảng thông báo mở
                
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Tắt huy hiệu chấm đỏ
                        if(unreadBadge) unreadBadge.style.display = 'none';
                        
                        // Xóa nền xanh của các thông báo chưa đọc
                        document.querySelectorAll('.unread-item').forEach(item => {
                            item.classList.remove('bg-emerald-50', 'unread-item');
                            item.classList.add('bg-white', 'opacity-70');
                        });

                        // Cập nhật giao diện nút
                        markAllReadBtn.innerText = 'Đã đọc tất cả';
                        markAllReadBtn.classList.add('text-gray-400', 'cursor-not-allowed', 'no-underline');
                        markAllReadBtn.classList.remove('text-emerald-600', 'hover:underline', 'cursor-pointer');
                    }
                })
                .catch(error => console.error('Lỗi:', error));
            });
        }
    });
</script>

@auth
    @if(Auth::user()->role !== 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentUnread = {{ $unreadCount ?? 0 }};
                
                setInterval(function() {
                    fetch('/notifications/check-customer')
                        .then(response => response.json())
                        .then(data => {
                            // Nếu có thông báo mới (Số trên database lớn hơn số đang hiện)
                            if(data.count > currentUnread) {
                                
                                // 1. Phát âm thanh Ting Ting
                                let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                                audio.play().catch(e => console.log("Trình duyệt chặn autoplay"));
                                
                                // KHÔNG DÙNG ALERT NỮA, BẮT ĐẦU TỰ ĐỘNG CẬP NHẬT GIAO DIỆN
                                
                                // 2. Cập nhật con số ở chấm đỏ
                                let badge = document.getElementById('unreadBadge');
                                let notiBtn = document.getElementById('notiBtn');
                                
                                if (!badge && data.count > 0) {
                                    // Nếu chưa có chấm đỏ thì tạo mới
                                    notiBtn.insertAdjacentHTML('beforeend', `<span id="unreadBadge" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-emerald-800">${data.count}</span>`);
                                } else if (badge) {
                                    // Nếu có rồi thì đổi số
                                    badge.innerText = data.count;
                                    badge.style.display = 'block';
                                }

                                // 3. Tự động vẽ lại danh sách thông báo thả xuống
                                let listContainer = document.querySelector('#notiDropdown .max-h-80');
                                if (listContainer && data.notifications) {
                                    listContainer.innerHTML = ''; // Xóa sạch thông báo cũ
                                    
                                    data.notifications.forEach(noti => {
                                        let bgClass = noti.is_read ? 'bg-white opacity-70' : 'bg-emerald-50 unread-item';
                                        // Bơm mã HTML mới vào
                                        listContainer.innerHTML += `
                                            <div class="block px-4 py-3 border-b border-gray-50 transition ${bgClass}">
                                                <p class="text-sm text-gray-800"><span class="font-bold text-emerald-700">${noti.title}:</span> ${noti.message}</p>
                                                <p class="text-xs text-gray-400 mt-1">${noti.time}</p>
                                            </div>
                                        `;
                                    });
                                }

                                // 4. Khôi phục nút "Đánh dấu đã đọc"
                                let markReadBtn = document.getElementById('markAllReadBtn');
                                if (markReadBtn && data.count > 0) {
                                    markReadBtn.innerText = 'Đánh dấu đã đọc';
                                    markReadBtn.className = 'text-xs text-emerald-600 cursor-pointer hover:underline font-bold';
                                }
                                
                                // Cập nhật lại mốc để không báo trùng
                                currentUnread = data.count;
                            }
                        })
                        .catch(e => console.error("Lỗi đồng bộ Real-time:", e));
                }, 4000); // Quét 4 giây 1 lần cho lẹ
            });
        </script>
    @endif
@endauth