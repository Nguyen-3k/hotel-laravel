<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra 2 điều kiện: Đã đăng nhập chưa? VÀ Cột role có phải là 'admin' không?
        if (auth()->check() && auth()->user()->role === 'admin') {
            // Nếu đúng, cho phép đi tiếp vào trang Admin
            return $next($request);
        }

        // Nếu là khách hoặc chưa đăng nhập, đá văng ra trang chủ kèm câu chửi
        return redirect('/')->with('error', 'Cảnh báo: Bạn không có quyền truy cập khu vực Quản trị!');
    }
    }
