<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Phòng - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-gray-900 text-white py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="/admin" class="text-xl font-bold tracking-wide text-yellow-500">⚙️ HỆ THỐNG QUẢN TRỊ ADMIN</a>
            <nav class="space-x-4 text-sm">
                <a href="/admin/rooms" class="hover:text-yellow-400 font-bold text-yellow-400">Danh sách phòng</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-10 px-4 max-w-2xl">
        <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-blue-500">
            <h2 class="text-2xl font-black text-gray-800 mb-6 border-b pb-3">Sửa Thông Tin Phòng P.{{ $room->room_number }}</h2>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/admin/rooms/{{ $room->id }}/update" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Số phòng</label>
                        <input type="text" name="room_number" value="{{ $room->room_number }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Loại phòng</label>
                        <select name="room_type" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600">
                            <option value="Standard" {{ $room->room_type == 'Standard' ? 'selected' : '' }}>Standard (Tiêu chuẩn)</option>
                            <option value="Deluxe" {{ $room->room_type == 'Deluxe' ? 'selected' : '' }}>Deluxe (Cao cấp)</option>
                            <option value="VIP" {{ $room->room_type == 'VIP' ? 'selected' : '' }}>VIP (Thương gia)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Giá mỗi đêm (VNĐ)</label>
                    <input type="number" name="price_per_night" value="{{ $room->price_per_night }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Link Ảnh minh họa</label>
                    <input type="url" name="image_url" value="{{ $room->image_url }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Mô tả tiện ích</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600">{{ $room->description }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition shadow">CẬP NHẬT THÔNG TIN</button>
            </form>
        </div>
    </main>
</body>
</html>