<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-emerald-800 mb-2">THIÊN ÂN HOTEL</h1>
            <p class="text-gray-500">Đăng nhập hệ thống quản trị</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <form action="/login" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Email quản trị</label>
                <input type="email" name="email" value="admin@gmail.com" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu..." class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-700" required>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl transition shadow-md">ĐĂNG NHẬP</button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-400">
            <a href="/" class="hover:text-emerald-700">← Quay lại Trang chủ</a>
        </div>
    </div>
</body>
</html>