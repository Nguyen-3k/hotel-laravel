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
                <div class="relative group pl-4 ml-2 flex items-center border-l border-emerald-600">
                    
                    <button class="flex items-center gap-2 focus:outline-none cursor-pointer">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=047857&color=fff&rounded=true&bold=true" 
                             alt="Avatar" 
                             class="w-9 h-9 rounded-full border-2 border-emerald-300 hover:border-yellow-400 transition shadow-sm object-cover">
                        
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
                                    
                                    <a href="#hoan-tien" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <span>💰</span> Hoàn tiền
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
            @endauth
            
        </nav>
    </div>
</header>