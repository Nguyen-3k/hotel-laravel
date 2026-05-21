<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Thiên Ân Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('header')

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-md w-full p-8 border-t-4 border-emerald-600">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-gray-900">Tạo tài khoản mới</h2>
                <p class="text-gray-500 mt-2 text-sm">Tham gia cùng Thiên Ân Hotel để nhận nhiều ưu đãi</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                    <ul class="list-disc pl-5 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên của bạn" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ email" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu</label>
                    <input type="password" name="password" placeholder="Tạo mật khẩu (ít nhất 6 ký tự)" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-50 focus:outline-emerald-600" required minlength="6">
                </div>

                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition transform hover:-translate-y-0.5 mt-2">
                    ĐĂNG KÝ TÀI KHOẢN
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-6 pt-6 border-t border-gray-100">
                Đã có tài khoản? <a href="/login" class="text-emerald-600 font-bold hover:underline">Đăng nhập ngay</a>
            </p>
        </div>
    </main>

</body>
</html>