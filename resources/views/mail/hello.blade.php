<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">

    <div style="max-width: 600px; margin: auto; background-color: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333;">Hi {{ $user->hoten ?? 'User' }},</h2>

        <p>You are receiving this email because we received a password reset request for your account.</p>

        <p style="text-align: center;">
            <a href="#" style="
                background-color: #007BFF;
                color: white;
                padding: 12px 24px;
                border-radius: 5px;
                text-decoration: none;
                display: inline-block;
            ">Reset Password</a>
        </p>

        <p>If you did not request a password reset, no further action is required.</p>

        <p>Regards,<br>{{ config('app.name') }}</p>
    </div>

</body>
</html>
