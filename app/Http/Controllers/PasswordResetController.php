<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:taikhoan,email',
        ]);

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Tùy chỉnh gửi email (tạm in ra token thay vì gửi mail)
        return response()->json([
            'message' => 'Token gửi thành công',
            'token' => $token
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:taikhoan,email',
            'token' => 'required|string',
            'matkhau' => 'required|string|min:6|confirmed',
        ]);

        $reset = DB::table('password_resets')->where([
            ['email', '=', $request->email],
            ['token', '=', $request->token],
        ])->first();

        if (!$reset) {
            return response()->json(['message' => 'Token không hợp lệ'], 400);
        }

        $user = TaiKhoan::where('email', $request->email)->first();
        $user->matkhau = bcrypt($request->matkhau);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mật khẩu đã được đặt lại thành công']);
    }
}
