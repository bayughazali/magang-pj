<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Competitor</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #ddd; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Report Competitor</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Cluster</th>
                <th>Nama Competitor</th>
                <th>Paket</th>
                <th>Kecepatan</th>
                <th>Kuota</th>
                <th>Harga</th>
                <th>Fitur Tambahan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competitors as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->cluster }}</td>
                <td>{{ $row->competitor_name }}</td>
                <td>{{ $row->paket }}</td>
                <td>{{ $row->kecepatan }}</td>
                <td>{{ $row->kuota }}</td>
                <td>Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
                <td>{{ $row->fitur_tambahan }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
