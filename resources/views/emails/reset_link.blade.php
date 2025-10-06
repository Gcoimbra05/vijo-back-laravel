<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset - Vijo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #333; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px; }
        .btn { display: inline-block; background: #4F46E5; color: #fff; padding: 12px 24px; border-radius: 4px; text-decoration: none; font-weight: bold; margin-top: 24px; }
        .footer { margin-top: 32px; font-size: 13px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset</h2>
        <p>Hello {{ $user->first_name }},</p>
        <p>We received a request to reset your password on Vijo.</p>
        <p>To create a new password, click the button below:</p>
        <a href="{{ $redirect_link }}" class="btn">Reset Password</a>
        <p style="margin-top:24px;">If you did not request this change, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} Vijo. All rights reserved.
        </div>
    </div>
</body>
</html>
