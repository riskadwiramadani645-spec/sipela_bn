<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ringkasan Input Saya - Guru</title>
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
        .section { margin: 20px 0; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>RINGKASAN INPUT DATA SAYA</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ $periode ?? 'Semua Data' }}</p>
    </div>

    <div class="section">
        <h3>📊 STATISTIK UMUM</h3>
        <table style="width: 60%;">
            <tr>
                <td><strong>Total Pelanggaran Input</strong></td>
                <td>{{ is_array($data) ? $data['pelanggaran'] : count($data) }}</td>
            </tr>
            <tr>
                <td><strong>Total Prestasi Input</strong></td>
                <td>{{ is_array($data) ? $data['prestasi'] : 0 }}</td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>{{ is_array($data) ? $data['periode'] : 'Semua Data' }}</td>
            </tr>
        </table>
    </div>

    @if(!is_array($data))
    <div class="section">
        <h3>⚠️ PELANGGARAN TERBANYAK YANG SAYA INPUT</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Jumlah Kasus</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $topPelanggaran = collect($data)
                        ->groupBy('jenis_pelanggaran_id')
                        ->map(function($items) {
                            $first = $items->first();
                            return (object) [
                                'nama_pelanggaran' => $first->jenisPelanggaran->nama_pelanggaran ?? 'N/A',
                                'total_kasus' => $items->count(),
                                'poin' => (int)($first->jenisPelanggaran->poin ?? 0)
                            ];
                        })
                        ->sortByDesc('total_kasus')
                        ->take(5);
                @endphp
                @forelse($topPelanggaran as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_pelanggaran }}</td>
                    <td>{{ $item->total_kasus }}</td>
                    <td>{{ $item->poin }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Guru Pencatat</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->guru->nama_guru ?? 'Guru' }}</p>
        <p>Guru</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>