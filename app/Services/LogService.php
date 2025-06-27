<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function log($tenHanhDong, $tenBangThucHien)
    {
        if (!Auth::check()) return;

        Log::create([
            'tenhanhdong' => $tenHanhDong,
            'tenbangthuchien' => $tenBangThucHien,
            'thoigianthuchien' => now(),
            'id_taikhoan' => Auth::id()
        ]);
    }
}
