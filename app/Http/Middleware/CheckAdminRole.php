<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
{
    $user = auth()->user();

    if (!$user || !in_array($user->phan_quyen, ['admin_hethong', 'admin_nhansu', 'nhanvien'])) {
        return response()->json(['message' => 'Bạn không có quyền truy cập trang quản trị'], 403);
    }

    return $next($request);
}

}
