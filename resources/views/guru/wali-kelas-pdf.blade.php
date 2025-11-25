<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Wali Kelas - SIPELA</title>
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
        .print-btn { margin: 20px 0; text-align: center; }
        .print-btn button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN WALI KELAS</h2>
        <p>Generated: {{ date('d F Y, H:i:s') }} WIB | Kelas: {{ $kelas->nama_kelas ?? 'N/A' }}</p>
        <p>Wali Kelas: {{ auth()->user()->name ?? 'N/A' }}</p>
    </div>

    @if(isset($siswaKelas) && count($siswaKelas) > 0)
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="15%">NISN</th>
                <th width="15%">Pelanggaran</th>
                <th width="15%">Prestasi</th>
                <th width="25%">Status Kedisiplinan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswaKelas as $index => $siswa)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                <td>{{ $siswa->nisn ?? '-' }}</td>
                <td class="text-center">{{ $siswa->total_pelanggaran ?? 0 }}</td>
                <td class="text-center">{{ $siswa->total_prestasi ?? 0 }}</td>
                <td class="text-center">
                    @php
                        $kedisiplinan = $siswa->tingkat_kedisiplinan ?? 100;
                    @endphp
                    <span style="background: {{ $kedisiplinan >= 80 ? '#28a745' : ($kedisiplinan >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $kedisiplinan }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="summary">
        <h3>📊 Ringkasan Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ count($siswaKelas ?? []) }} siswa</li>
            <li><strong>Total Pelanggaran:</strong> {{ $totalPelanggaran ?? 0 }} kasus</li>
            <li><strong>Total Prestasi:</strong> {{ $totalPrestasi ?? 0 }} prestasi</li>
            <li><strong>Total Sanksi:</strong> {{ $totalSanksi ?? 0 }} sanksi</li>
            <li><strong>Tingkat Kedisiplinan Kelas:</strong> {{ $tingkatKedisiplinan ?? 0 }}%</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ date('d F Y') }}</p>
        <p>Wali Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ auth()->user()->name ?? 'Wali Kelas' }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>