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
            background-color: #f4f4f4;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .code-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 25px 0;
        }
        .code {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .code-label {
            margin: 15px 0 0 0;
            color: #ffffff;
            font-size: 14px;
            opacity: 0.9;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 15px 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .warning strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .warning ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
        .warning li {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>🔐 Reset Password</h1>
            <p>{{ config('app.name', 'Laravel') }}</p>
        </div>

        <div class="content">
            <p>Halo,</p>

            <p>Anda telah meminta untuk mereset password akun Anda. Gunakan kode verifikasi berikut untuk melanjutkan proses reset password:</p>

            <div class="code-box">
                <div class="code">{{ $Code }}</div>
                <p class="code-label">Masukkan kode ini di halaman verifikasi</p>
            </div>

            <div class="warning">
                <strong>⚠️ Peringatan Keamanan:</strong>
                <ul>
                    <li>Kode ini berlaku selama <strong>15 menit</strong> sejak email dikirim</li>
                    <li><strong>Jangan bagikan</strong> kode ini kepada siapa pun, termasuk staff kami</li>
                    <li>Jika Anda <strong>tidak meminta</strong> reset password, segera abaikan email ini dan hubungi support</li>
                    <li>Untuk keamanan akun Anda, pastikan menggunakan password yang kuat</li>
                </ul>
            </div>

            <p>Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi tim support kami.</p>

            <p style="margin-top: 30px;">
                Terima kasih,<br>
                <strong>Tim {{ config('app.name', 'Laravel') }}</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>Email otomatis - Mohon tidak membalas email ini</strong></p>
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
