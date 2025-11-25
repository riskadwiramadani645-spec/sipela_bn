<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary - Kesiswaan</title>
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
        .chart-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>EXECUTIVE SUMMARY KESISWAAN</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Eksekutif</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ $totalSiswa ?? 0 }} siswa</li>
            <li><strong>Total Pelanggaran:</strong> {{ $totalPelanggaran ?? 0 }} kasus</li>
            <li><strong>Total Prestasi:</strong> {{ $totalPrestasi ?? 0 }} prestasi</li>
            <li><strong>Siswa Bermasalah:</strong> {{ $siswaBermasalah ?? 0 }} siswa</li>
            <li><strong>Tingkat Kedisiplinan:</strong> {{ $tingkatKedisiplinan ?? 0 }}%</li>
        </ul>
    </div>

    <div class="chart-section">
        <h3>📈 Tren Pelanggaran per Bulan</h3>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Pelanggaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trenBulanan ?? [] as $bulan => $jumlah)
                <tr>
                    <td>{{ $bulan }}</td>
                    <td class="text-center">{{ $jumlah }}</td>
                    <td class="text-center">{{ $totalPelanggaran > 0 ? round(($jumlah / $totalPelanggaran) * 100, 1) : 0 }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="chart-section">
        <h3>🏆 Top 5 Kelas Terdisiplin</h3>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Kelas</th>
                    <th>Jumlah Siswa</th>
                    <th>Pelanggaran</th>
                    <th>Skor Disiplin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelasTerdisiplin ?? [] as $index => $kelas)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $kelas->nama_kelas ?? 'N/A' }}</td>
                    <td class="text-center">{{ $kelas->jumlah_siswa ?? 0 }}</td>
                    <td class="text-center">{{ $kelas->total_pelanggaran ?? 0 }}</td>
                    <td class="text-center">{{ $kelas->skor_disiplin ?? 0 }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="chart-section">
        <h3>⚠️ Jenis Pelanggaran Terbanyak</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Jumlah Kasus</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggaranTerbanyak ?? [] as $index => $pelanggaran)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                    <td class="text-center">{{ $pelanggaran->total ?? 0 }}</td>
                    <td class="text-center">{{ $totalPelanggaran > 0 ? round(($pelanggaran->total / $totalPelanggaran) * 100, 1) : 0 }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Wakil Kepala Sekolah Bidang Kesiswaan</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->nama ?? 'Wakasek Kesiswaan' }}</p>
        <p>NIP. -</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>