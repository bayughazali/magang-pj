<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .code-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #007bff;
            font-family: monospace;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reset Password</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="content">
        <p>Halo,</p>
        
        <p>Anda telah meminta untuk mereset password akun Anda. Gunakan kode verifikasi berikut untuk melanjutkan:</p>

        <div class="code-box">
           <div class="code">{{ $debugCode ?? 'Kode akan dikirim via email' }}</div>
            <p style="margin: 10px 0 0 0; color: #6c757d;">Masukkan kode ini di halaman reset password</p>
        </div>

        <div class="warning">
            <strong>Peringatan:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Kode ini berlaku selama <strong>15 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapa pun</li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
            </ul>
        </div>

        <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami.</p>
        
        <p>Terima kasih,<br>
        Tim {{ config('app.name') }}</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis, mohon jangan membalas email ini.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>