<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <h2>Reset Password</h2>
        <p>Halo,</p>
        <p>Gunakan kode berikut untuk reset password:</p>

        <div style="background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
            <h1 style="font-size: 36px; letter-spacing: 8px; margin: 0;">{{ $code }}</h1>
        </div>

        <p><strong>Peringatan:</strong></p>
        <ul>
            <li>Kode berlaku 15 menit</li>
            <li>Jangan bagikan kode ini</li>
        </ul>

        <p>Terima kasih,<br>Tim {{ config('app.name', 'Laravel') }}</p>
    </div>
</body>
</html>
