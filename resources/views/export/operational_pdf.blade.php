<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Operational</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 0; }
        small { text-align: center; display: block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Laporan Operational Data Pelanggan</h2>
    <small>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</small>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pelanggan</th>
                <th>Nama</th>
                <th>Bandwidth</th>
                <th>Telepon</th>
                <th>Provinsi</th>
                <th>Kabupaten</th>
                <th>Alamat</th>
                <th>Cluster</th>
                <th>Kode FAT</th>
                <th>Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->id_pelanggan }}</td>
                    <td>{{ $p->nama_pelanggan }}</td>
                    <td>{{ $p->bandwidth }}</td>
                    <td>{{ $p->nomor_telepon }}</td>
                    <td>{{ $p->provinsi }}</td>
                    <td>{{ $p->kabupaten }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>{{ $p->cluster }}</td>
                    <td>{{ $p->kode_fat }}</td>
                    <td>{{ $p->latitude }}, {{ $p->longitude }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
