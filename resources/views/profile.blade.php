<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Thiên Ân Hotel</title>
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
                <a href="/profile" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl transition">
                    <span>👤</span> Hồ sơ của tôi
                </a>
                <a href="/my-bookings" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-emerald-700 font-medium rounded-xl transition">
                    <span>📜</span> Lịch sử giao dịch
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-emerald-700 font-medium rounded-xl transition opacity-50 cursor-not-allowed" title="Tính năng đang bảo trì">
                    <span>💰</span> Hoàn tiền (Sắp ra mắt)
                </a>
            </nav>
        </aside>

        <div class="w-full md:w-3/4 bg-white rounded-2xl shadow-md p-8">
            <h2 class="text-2xl font-black text-gray-900 mb-6 pb-4 border-b">Hồ Sơ Cá Nhân</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form action="/profile/update" method="POST" class="max-w-2xl">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email (Không thể thay đổi)</label>
                        <input type="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-200 text-gray-500 cursor-not-allowed" disabled>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Đổi mật khẩu (Bỏ trống nếu không đổi)</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mật khẩu mới</label>
                            <input type="password" name="password" placeholder="Nhập mật khẩu mới..." class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600">
                        </div>
                    </div>

                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-8 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                        💾 LƯU THAY ĐỔI
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>