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

    <main class="container mx-auto py-10 px-4 max-w-3xl">
        <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-blue-500">
            <h2 class="text-2xl font-black text-gray-800 mb-6 border-b pb-3">Sửa Thông Tin Phòng P.{{ $room->room_number }}</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-6 font-bold">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 border-b pb-6">
                <h3 class="font-bold text-gray-700 mb-3">📸 Các ảnh phụ của phòng hiện tại</h3>
                @if(isset($room) && $room->images && $room->images->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($room->images as $img)
                            <div class="relative group border rounded shadow-sm">
                                <img src="{{ asset($img->image_url) }}" class="w-full h-24 object-cover rounded">
                                <form action="/admin/room-images/{{ $img->id }}" method="POST" class="absolute top-1 right-1 hidden group-hover:block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')" class="bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-800 shadow cursor-pointer">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic bg-gray-50 p-3 rounded">Chưa có ảnh phụ nào.</p>
                @endif
            </div>

            <form action="/admin/rooms/{{ $room->id }}/update" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Số phòng</label>
                        <input type="text" name="room_number" value="{{ $room->room_number ?? old('room_number') }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Hạng phòng</label>
                        <select name="room_type" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                            <option value="Standard" {{ isset($room) && $room->room_type == 'Standard' ? 'selected' : '' }}>Standard</option>
                            <option value="Deluxe" {{ isset($room) && $room->room_type == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                            <option value="VIP" {{ isset($room) && $room->room_type == 'VIP' ? 'selected' : '' }}>VIP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Cấu hình giường</label>
                        <select name="bed_type" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                            <option value="1 giường đơn" {{ isset($room) && $room->bed_type == '1 giường đơn' ? 'selected' : '' }}>1 Giường Đơn</option>
                            <option value="1 giường đôi" {{ isset($room) && $room->bed_type == '1 giường đôi' ? 'selected' : '' }}>1 Giường Đôi</option>
                            <option value="2 giường đơn" {{ isset($room) && $room->bed_type == '2 giường đơn' ? 'selected' : '' }}>2 Giường Đơn</option>
                            <option value="2 giường đôi" {{ isset($room) && $room->bed_type == '2 giường đôi' ? 'selected' : '' }}>2 Giường Đôi</option>
                            <option value="1 giường đôi 1 giường đơn" {{ isset($room) && $room->bed_type == '1 giường đôi 1 giường đơn' ? 'selected' : '' }}>1 Giường Đôi, 1 Giường Đơn</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Giá mỗi đêm (VNĐ)</label>
                    <input type="number" name="price_per_night" value="{{ $room->price_per_night }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" required>
                </div>

                @if($room->image_url)
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ảnh chính hiện tại</label>
                    <img src="{{ Str::startsWith($room->image_url, ['http://', 'https://']) ? $room->image_url : asset($room->image_url) }}" alt="Ảnh phòng" class="w-32 h-32 object-cover rounded shadow-md border">
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Đổi Ảnh Chính (Tùy chọn)</label>
                        <input type="file" name="image_upload" accept="image/*" class="w-full border border-gray-300 bg-white rounded p-1.5 focus:outline-blue-600">
                        <p class="text-xs text-gray-500 mt-1">Bỏ trống nếu không đổi ảnh chính.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-blue-700 mb-1">Thêm ảnh phụ (Nhiều ảnh)</label>
                        <input type="file" name="gallery_images[]" multiple accept="image/*" class="w-full border border-blue-300 bg-white rounded p-1.5 focus:outline-blue-600">
                        <p class="text-xs text-gray-500 mt-1">Giữ phím Ctrl (hoặc Shift) để chọn nhiều ảnh.</p>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Hoặc nhập Link Ảnh (URL)</label>
                    <input type="url" name="image_url" value="{{ $room->image_url }}" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600" placeholder="https://example.com/image.jpg">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Mô tả tiện ích</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded p-2 focus:outline-blue-600">{{ $room->description }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition shadow cursor-pointer">CẬP NHẬT THÔNG TIN</button>
            </form>
        </div>
    </main>
</body>
</html>