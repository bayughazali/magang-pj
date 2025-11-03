<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <div class="code">
    @isset($code)
        {{ $code }}
    @else
        <!-- kosong / pesan / gunakan session -->
        {{ session('code') ?? 'Kode belum tersedia' }}
    @endisset
    </div>
</body>
</html>
