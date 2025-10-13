<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4e73df;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4e73df;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        
        .info-section {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 20px 5px 0;
            width: 150px;
            color: #5a5c69;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #333;
        }
        
        .filters-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
        }
        
        .filters-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        
        .data-table th {
            background-color: #4e73df;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #3b5998;
        }
        
        .data-table td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            text-align: left;
            vertical-align: top;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        
        .data-table tbody tr:hover {
            background-color: #eaecf4;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-primary {
            background-color: #4e73df;
            color: white;
        }
        
        .badge-success {
            background-color: #1cc88a;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">Laporan Data Users</div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tanggal Export:</div>
                <div class="info-value">{{ $date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Data:</div>
                <div class="info-value">{{ number_format($total) }} records</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ $total > 0 ? 'Data Tersedia' : 'Tidak Ada Data' }}</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    @if($filters['date_from'] || $filters['date_to'] || $filters['role'])
    <div class="filters-section">
        <div class="filters-title">Filter Yang Diterapkan:</div>
        <div class="info-grid">
            @if($filters['date_from'])
            <div class="info-row">
                <div class="info-label">Tanggal Dari:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($filters['date_to'])
            <div class="info-row">
                <div class="info-label">Tanggal Sampai:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($filters['role'])
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">{{ strtoupper($filters['role']) }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Data Table -->
    @if($total > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 25%;">Nama</th>
                <th style="width: 30%;">Email</th>
                <th style="width: 15%;">Role</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 10%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $user->role ?? 'USER' }}</span>
                </td>
                <td class="text-center">
                    @if($user->email_verified_at)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                </td>
                <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
            </tr>
            @if(($index + 1) % 25 == 0 && $index + 1 < $total)
            </tbody>
        </table>
        <div class="page-break"></div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 10%;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
            @endif
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada data yang sesuai dengan filter yang diterapkan.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem PLN AKPOL pada {{ $date }}</p>
        <p>© {{ date('Y') }} PLN AKPOL. Semua hak dilindungi.</p>
    </div>
</body>
</html>