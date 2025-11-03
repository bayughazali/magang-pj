<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Kode Reset Password</h2>
    <p>Kode verifikasi Anda adalah:</p>
    <input type="hidden" name="email" value="{{ session('email') }}">
    <p>Kode berlaku selama 15 menit.</p>
</body>
</html>
