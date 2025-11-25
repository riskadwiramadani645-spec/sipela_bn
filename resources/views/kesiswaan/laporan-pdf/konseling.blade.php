<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Konseling - Kesiswaan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #666; }
        .header p { margin: 10px 0; color: #888; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .summary { margin-top: 30px; padding: 20px; background-color: #e9ecef; border-radius: 5px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN HASIL KONSELING</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Total: {{ count($data) }} sesi konseling</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Tanggal</th>
                <th width="25%">Jenis Konseling</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $konseling)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $konseling->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td>{{ $konseling->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td class="text-center">{{ $konseling->tanggal_konseling ? date('d/m/Y', strtotime($konseling->tanggal_konseling)) : '-' }}</td>
                <td>{{ $konseling->jenis_layanan ?? 'Individu' }} - {{ $konseling->topik ?? 'Konseling Umum' }}</td>
                <td class="text-center">
                    <span style="background: {{ $konseling->status == 'selesai' ? '#28a745' : ($konseling->status == 'diproses' ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ ucfirst($konseling->status ?? 'terdaftar') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Hasil Konseling</h3>
        <ul>
            <li><strong>Total Sesi Konseling:</strong> {{ count($data) }} sesi</li>
            <li><strong>Konseling Selesai:</strong> {{ collect($data)->where('status', 'selesai')->count() }} sesi</li>
            <li><strong>Konseling Dalam Proses:</strong> {{ collect($data)->where('status', 'proses')->count() }} sesi</li>
            <li><strong>Tindak Lanjut:</strong> Monitoring berkala dan evaluasi perkembangan siswa</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Bagian Kesiswaan</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Koordinator Kesiswaan</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>