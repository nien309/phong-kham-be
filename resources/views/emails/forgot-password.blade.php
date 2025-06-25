<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
</head>
<body>
    <p>Xin chào {{ $user->hoten ?? 'người dùng' }},</p>

    <p>Bạn nhận được email này vì đã yêu cầu đặt lại mật khẩu.</p>

    <p>
        Nhấn vào nút bên dưới để đặt lại mật khẩu:
    </p>

    <p>
        <a href="{{ $resetUrl }}" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none;">Đặt lại mật khẩu</a>
    </p>

    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này.</p>

    <p>Trân trọng,<br>{{ config('app.name') }}</p>
</body>
</html>
