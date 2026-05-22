<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hiệu ứng trượt mượt mà cho đổi mật khẩu */
        .slide-down {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out, opacity 0.3s ease;
            opacity: 0;
        }
        .slide-down.active {
            max-height: 500px;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('header')

    <main class="container mx-auto py-10 px-4 flex-grow flex flex-col md:flex-row gap-8 max-w-6xl">
        
        <aside class="w-full md:w-1/4 bg-white rounded-2xl shadow-md p-6 h-fit border-t-4 border-emerald-600">
            <form action="/profile/avatar" method="POST" enctype="multipart/form-data" class="flex flex-col items-center mb-6 border-b pb-6 relative group">
                @csrf
                <label for="avatar_upload" class="cursor-pointer relative">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" class="w-24 h-24 rounded-full border-4 border-emerald-100 mb-3 shadow-sm object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=047857&color=fff&size=100&rounded=true&bold=true" class="w-24 h-24 rounded-full border-4 border-emerald-100 mb-3 shadow-sm">
                    @endif
                    
                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 mb-3">
                        <span class="text-white text-sm font-bold">📷 Đổi ảnh</span>
                    </div>
                </label>
                <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                
                <h3 class="text-lg font-black text-gray-900">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-500">Thành viên Thiên Ân</p>
            </form>

            <nav class="flex flex-col space-y-2">
                <a href="/profile" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl transition">
                    <span>👤</span> Hồ sơ của tôi
                </a>
                <a href="/my-bookings" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-emerald-700 font-medium rounded-xl transition">
                    <span>📜</span> Lịch sử giao dịch
                </a>
            </nav>
        </aside>

        <div class="w-full md:w-3/4 bg-white rounded-2xl shadow-md p-8">
            <h2 class="text-2xl font-black text-gray-900 mb-6 pb-4 border-b">Hồ Sơ Cá Nhân</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">⚠️ {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                    <ul class="list-disc pl-5 font-medium">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8 max-w-2xl">
                
                <form action="/profile/email" method="POST" class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                    @csrf
                    <label class="block text-sm font-black text-gray-800 mb-4">Quản lý Email tài khoản</label>

                    @if(Auth::user()->email_change_status === 'none')
                        <div class="mb-3">
                            <input type="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-200 text-gray-500" disabled>
                        </div>
                        <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                            <input type="password" name="password" placeholder="Nhập mật khẩu hiện tại để xác thực..." class="w-full md:w-2/3 border border-gray-300 rounded-xl p-3 bg-white focus:outline-blue-600" required>
                            <button type="submit" name="action" value="request" class="w-full md:w-1/3 shrink-0 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition text-sm">Xin đổi Email</button>
                        </div>

                    @elseif(Auth::user()->email_change_status === 'pending')
                        <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                            <input type="email" value="{{ Auth::user()->email }}" class="w-full border border-yellow-400 rounded-xl p-3 bg-yellow-50 text-gray-500" disabled>
                            <span class="shrink-0 bg-yellow-100 text-yellow-800 font-bold py-3 px-4 rounded-xl text-sm border border-yellow-300 w-full text-center md:w-auto">⏳ Đang chờ Admin duyệt</span>
                        </div>

                    @elseif(Auth::user()->email_change_status === 'approved')
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-bold text-emerald-700 mb-1 block">Nhập Email mới:</label>
                                <input type="email" name="new_email" placeholder="Ví dụ: emailmoi@gmail.com" class="w-full border-2 border-emerald-500 rounded-xl p-3 bg-white focus:outline-emerald-600" required>
                            </div>
                            <div class="flex flex-col md:flex-row gap-3 items-start md:items-center pt-2">
                                <input type="password" name="password" placeholder="Nhập mật khẩu để xác nhận đổi..." class="w-full md:w-2/3 border border-emerald-300 rounded-xl p-3 bg-white focus:outline-emerald-600" required>
                                <button type="submit" name="action" value="update" class="w-full md:w-1/3 shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition text-sm">Xác nhận đổi</button>
                            </div>
                        </div>
                        
                    @elseif(Auth::user()->email_change_status === 'changed')
                        <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                            <input type="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-200 text-gray-500" disabled>
                            <span class="shrink-0 bg-gray-200 text-gray-600 font-bold py-3 px-4 rounded-xl text-sm border border-gray-300 w-full text-center md:w-auto">🔒 Đã sử dụng quyền đổi Email</span>
                        </div>
                    @endif

                    <p class="text-xs text-red-500 mt-4 font-medium">* Hệ thống yêu cầu nhập mật khẩu bảo mật trước khi thực hiện thao tác này.</p>
                </form>

                <form action="/profile/update" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                    </div>

                    <button type="button" id="togglePasswordBtn" class="text-emerald-700 font-bold flex items-center gap-2 hover:underline mb-4">
                        <span>🔑</span> Thay đổi mật khẩu
                    </button>

                    <div id="passwordForm" class="slide-down bg-emerald-50 p-5 rounded-xl border border-emerald-100 mb-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu hiện tại</label>
                                <input type="password" name="old_password" placeholder="Nhập mật khẩu cũ..." class="w-full border border-gray-300 rounded-xl p-3 bg-white focus:outline-emerald-600">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu mới</label>
                                    <input type="password" name="password" placeholder="Tối thiểu 6 ký tự" class="w-full border border-gray-300 rounded-xl p-3 bg-white focus:outline-emerald-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" placeholder="Xác nhận lại mật khẩu" class="w-full border border-gray-300 rounded-xl p-3 bg-white focus:outline-emerald-600">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full md:w-auto bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-8 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                        💾 LƯU THAY ĐỔI THÔNG TIN
                    </button>
                </form>

            </div>
        </div>
    </main>

    <script>
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const form = document.getElementById('passwordForm');
            form.classList.toggle('active');
        });
    </script>
</body>
</html>