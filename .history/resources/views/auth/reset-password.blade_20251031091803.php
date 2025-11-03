<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            padding: 40px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .icon-wrapper svg {
            width: 40px;
            height: 40px;
            stroke: white;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #718096;
            text-align: center;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .code-display {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            text-align: center;
        }

        .code-label {
            color: #4a5568;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .code-value {
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            text-shadow: 2px 2px 4px rgba(102, 126, 234, 0.1);
        }

        .info-box {
            background: #fff5f5;
            border-left: 4px solid #fc8181;
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .info-box-warning {
            background: #fffaf0;
            border-left-color: #f6ad55;
        }

        .info-box p {
            color: #742a2a;
            font-size: 14px;
            line-height: 1.6;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box-warning p {
            color: #7c2d12;
        }

        .info-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .timer {
            background: #edf2f7;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .timer svg {
            width: 20px;
            height: 20px;
            stroke: #667eea;
        }

        .timer span {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
        }

        .timer .time {
            color: #667eea;
            font-weight: 700;
        }

        .actions {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            transform: translateY(-2px);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .code-value {
                font-size: 36px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <svg viewBox="0 0 24 24">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
        </div>

        <h1>Kode Reset Password</h1>
        <p class="subtitle">Kode verifikasi Anda adalah:</p>

        <div class="code-display">
            <div class="code-label">Kode Verifikasi</div>
            <div class="code-value">123456</div>
        </div>

        <div class="timer">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Kode berlaku selama <span class="time">15 menit</span></span>
        </div>

        <div class="info-box info-box-warning">
            <p>
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Gunakan kode ini untuk memverifikasi identitas Anda dan melanjutkan proses reset password.
            </p>
        </div>

        <div class="info-box">
            <p>
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Jangan bagikan kode ini kepada siapa pun untuk keamanan akun Anda.
            </p>
        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="copyCode()">
                📋 Salin Kode
            </button>
            <a href="#" class="btn btn-secondary">
                Tidak menerima kode? Kirim Ulang
            </a>
        </div>

        <div class="back-link">
            <a href="#">← Kembali ke Login</a>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.querySelector('.code-value').textContent;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.querySelector('.btn-primary');
                const originalText = btn.textContent;
                btn.textContent = '✓ Kode Tersalin!';
                btn.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';

                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                }, 2000);
            });
        }
    </script>
</body>
</html>
