<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Progress Siswa - Wali Kelas</title>
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
        <h2>LAPORAN PROGRESS SISWA - {{ $kelas->nama_kelas ?? 'Kelas' }}</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
        <p>Wali Kelas: {{ $guru->nama_guru ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Progress Perilaku</th>
                <th width="15%">Kedisiplinan</th>
                <th width="30%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exportData ?? [] as $index => $siswa)
            @php
                $totalPelanggaran = $siswa->pelanggaran->count();
                $kedisiplinan = $totalPelanggaran > 0 ? max(0, 100 - ($totalPelanggaran * 10)) : 100;
                $progress = $kedisiplinan >= 80 ? 80 : 60;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $siswa->nama_siswa }}</strong></td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ $progress >= 80 ? '#28a745' : ($progress >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $progress }}%
                    </span>
                </td>
                <td class="text-center">
                    <span style="background: {{ $kedisiplinan >= 80 ? '#28a745' : ($kedisiplinan >= 60 ? '#ffc107' : '#dc3545') }}; color: white; padding: 2px 8px; border-radius: 3px;">
                        {{ $kedisiplinan }}%
                    </span>
                </td>
                <td>{{ $totalPelanggaran > 0 ? 'Perlu pembinaan' : 'Dalam monitoring' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>📊 Ringkasan Progress Siswa</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ count($exportData ?? []) }} siswa</li>
            <li><strong>Siswa Bermasalah:</strong> {{ collect($exportData ?? [])->filter(function($s) { return $s->pelanggaran->count() > 0; })->count() }} siswa</li>
            <li><strong>Siswa Normal:</strong> {{ collect($exportData ?? [])->filter(function($s) { return $s->pelanggaran->count() == 0; })->count() }} siswa</li>
        </ul>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Wali Kelas {{ $kelas->nama_kelas }}</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $guru->nama_guru ?? 'Wali Kelas' }}</p>
        <p>NIP: {{ $guru->nip ?? '-' }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>