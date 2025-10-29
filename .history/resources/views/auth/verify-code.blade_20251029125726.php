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
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .header p {
            color: #666;
            font-size: 0.9rem;
        }
        .email-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 1rem;
            color: #667eea;
            font-weight: 500;
        }
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 5px;
            font-family: monospace;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-control.error {
            border-color: #dc3545;
        }
        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
            margin-bottom: 10px;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
        }
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #856404;
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verifikasi Kode</h2>
            <p>Masukkan 6 digit kode yang dikirim ke email Anda</p>
        </div>

        @if(session('email'))
            <div class="email-display">
                {{ session('email') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="info-box">
            ⏰ Kode berlaku selama 15 menit
        </div>

        <form action="{{ route('verify.code') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">

            <div class="form-group">
                <label for="code">Kode Verifikasi</label>
                <input type="text"
                       id="code"
                       name="code"
                       class="form-control @error('code') error @enderror"
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       value="{{ old('code') }}"
                       required
                       autofocus>
                @error('code')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">
                Verifikasi Kode
            </button>
        </form>

        <form action="{{ route('resend.code') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">
            <button type="submit" class="btn btn-secondary">
                Kirim Ulang Kode
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('forgot.password') }}">← Gunakan email lain</a>
        </div>
    </div>

    <script>
        // Auto format input ke 6 digit
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });
    </script>
