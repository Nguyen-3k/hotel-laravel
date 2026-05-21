<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('header')

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-md w-full p-8 border-t-4 border-emerald-600">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-gray-900">Mừng bạn trở lại!</h2>
                <p class="text-gray-500 mt-2 text-sm">Đăng nhập để quản lý đơn đặt phòng của bạn</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ email" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-gray-700">Mật khẩu</label>
                        <a href="#" class="text-xs text-emerald-600 hover:underline">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500">
                    <label for="remember" class="ml-2 text-sm font-medium text-gray-600">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition transform hover:-translate-y-0.5 mt-2">
                    🔓 ĐĂNG NHẬP
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-6 pt-6 border-t border-gray-100">
                Chưa có tài khoản? <a href="/register" class="text-emerald-600 font-bold hover:underline">Đăng ký ngay</a>
            </p>
        </div>
    </main>

</body>
</html>