<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Activity Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    th { background-color: #007bff; color: white; }
    .header { text-align: center; margin-bottom: 20px; }
    .logo { width: 80px; margin-bottom: 10px; }
<<<<<<< HEAD
=======
    img.evidence { max-height: 60px; border-radius: 6px; }
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('images/pln-logo.png') }}" class="logo" alt="PLN Logo">
    <h2>Activity Report</h2>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
<<<<<<< HEAD
        <th>Nama</th>
        <th>Kegiatan</th>
        <th>Tanggal</th>
        <th>Lokasi</th>
        <th>Cluster</th>
=======
        <th>Sales</th>
        <th>Aktivitas</th>
        <th>Tanggal</th>
        <th>Lokasi</th>
        <th>Cluster</th>
        <th>Evidence</th>
        <th>Hasil / Kendala</th>
        <th>Status</th>
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
      </tr>
    </thead>
    <tbody>
      @foreach($activities as $i => $activity)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $activity->sales }}</td>
        <td>{{ $activity->aktivitas }}</td>
        <td>{{ \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}</td>
        <td>{{ $activity->lokasi }}</td>
        <td>{{ $activity->cluster }}</td>
<<<<<<< HEAD
=======
        <td>
          @if($activity->evidence)
            <img src="{{ public_path('storage/'.$activity->evidence) }}" class="evidence" alt="evidence">
          @else
            <span>-</span>
          @endif
        </td>
        <td>{{ $activity->hasil_kendala ?? '-' }}</td>
        <td>
          @if($activity->status == 'selesai')
            <span style="color:green; font-weight:bold;">Selesai</span>
          @else
            <span style="color:orange;">{{ ucfirst($activity->status) }}</span>
          @endif
        </td>
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
