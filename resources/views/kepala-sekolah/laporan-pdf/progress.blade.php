<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Progress Siswa - Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #666; }
        .header p { margin: 10px 0; color: #888; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .summary { margin-top: 30px; padding: 20px; background-color: #e9ecef; border-radius: 5px; }
        .progress-excellent { background-color: #d4edda; }
        .progress-good { background-color: #d1ecf1; }
        .progress-fair { background-color: #fff3cd; }
        .progress-poor { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PROGRESS SISWA</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Progress Siswa</h3>
        <ul>
            <li><strong>Total Siswa:</strong> {{ isset($data['data']) ? count($data['data']) : 0 }} siswa</li>
            <li><strong>Siswa Berprestasi:</strong> {{ isset($data['data']) ? collect($data['data'])->filter(function($siswa) { return count($siswa->prestasi) > 0; })->count() : 0 }} siswa</li>
            <li><strong>Siswa Bermasalah:</strong> {{ isset($data['data']) ? collect($data['data'])->filter(function($siswa) { return count($siswa->pelanggaran) > 3; })->count() : 0 }} siswa</li>
            <li><strong>Siswa Perlu Perhatian:</strong> {{ isset($data['data']) ? collect($data['data'])->filter(function($siswa) { return count($siswa->pelanggaran) > 0 && count($siswa->pelanggaran) <= 3; })->count() : 0 }} siswa</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th width="8%">Pelanggaran</th>
                <th width="8%">Prestasi</th>
                <th width="10%">Total Poin</th>
                <th width="10%">Skor Disiplin</th>
                <th width="10%">Status Progress</th>
                <th width="13%">Rekomendasi</th>
                <th width="10%">Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $siswa)
            @php
                $totalPelanggaran = count($siswa->pelanggaran);
                $totalPrestasi = count($siswa->prestasi);
                $totalPoin = collect($siswa->pelanggaran)->sum(function($p) { return $p->jenisPelanggaran->poin ?? 0; }) - collect($siswa->prestasi)->sum(function($pr) { return $pr->jenisPrestasi->poin ?? 0; });
                $skorDisiplin = max(0, 100 - ($totalPoin * 2));
                
                $statusProgress = 'Excellent';
                $cssClass = 'progress-excellent';
                $rekomendasi = 'Pertahankan prestasi';
                
                if ($totalPelanggaran > 5 || $skorDisiplin < 60) {
                    $statusProgress = 'Poor';
                    $cssClass = 'progress-poor';
                    $rekomendasi = 'Konseling intensif';
                } elseif ($totalPelanggaran > 2 || $skorDisiplin < 75) {
                    $statusProgress = 'Fair';
                    $cssClass = 'progress-fair';
                    $rekomendasi = 'Monitoring ketat';
                } elseif ($totalPelanggaran > 0 || $skorDisiplin < 90) {
                    $statusProgress = 'Good';
                    $cssClass = 'progress-good';
                    $rekomendasi = 'Pembinaan rutin';
                }
                
                $terakhirUpdate = collect([$siswa->pelanggaran->max('created_at'), $siswa->prestasi->max('created_at')])->filter()->max();
            @endphp
            <tr class="{{ $cssClass }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td class="text-center">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td class="text-center">{{ $totalPelanggaran }}</td>
                <td class="text-center">{{ $totalPrestasi }}</td>
                <td class="text-center">{{ $totalPoin }}</td>
                <td class="text-center">{{ round($skorDisiplin, 1) }}%</td>
                <td class="text-center">
                    <span style="background: {{ 
                        $statusProgress == 'Excellent' ? '#28a745' : 
                        ($statusProgress == 'Good' ? '#17a2b8' : 
                        ($statusProgress == 'Fair' ? '#ffc107' : '#dc3545'))
                    }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">
                        {{ $statusProgress }}
                    </span>
                </td>
                <td>{{ $rekomendasi }}</td>
                <td class="text-center">{{ $terakhirUpdate ? date('d/m/Y', strtotime($terakhirUpdate)) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($data['data']) && count($data['data']) > 0)
    <div class="summary">
        <h3>🏆 Top 10 Siswa Berprestasi</h3>
        @php
            $topSiswa = collect($data['data'])->map(function($siswa) {
                $totalPrestasi = count($siswa->prestasi);
                $poinPrestasi = collect($siswa->prestasi)->sum(function($pr) { return $pr->jenisPrestasi->poin ?? 0; });
                return [
                    'nama' => $siswa->nama_siswa,
                    'kelas' => $siswa->kelas->nama_kelas ?? 'N/A',
                    'total_prestasi' => $totalPrestasi,
                    'poin_prestasi' => $poinPrestasi,
                    'pelanggaran' => count($siswa->pelanggaran)
                ];
            })->sortByDesc('poin_prestasi')->take(10);
        @endphp
        
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Prestasi</th>
                    <th>Poin Prestasi</th>
                    <th>Pelanggaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSiswa as $index => $siswa)
                <tr>
                    <td class="text-center">
                        @if($index == 0)
                            🥇 1
                        @elseif($index == 1)
                            🥈 2
                        @elseif($index == 2)
                            🥉 3
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td><strong>{{ $siswa['nama'] }}</strong></td>
                    <td class="text-center">{{ $siswa['kelas'] }}</td>
                    <td class="text-center">{{ $siswa['total_prestasi'] }}</td>
                    <td class="text-center">{{ $siswa['poin_prestasi'] }}</td>
                    <td class="text-center">{{ $siswa['pelanggaran'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h3>⚠️ Siswa Perlu Perhatian Khusus</h3>
        @php
            $siswaPerluPerhatian = collect($data['data'])->filter(function($siswa) {
                return count($siswa->pelanggaran) > 3;
            })->map(function($siswa) {
                $totalPelanggaran = count($siswa->pelanggaran);
                $poinPelanggaran = collect($siswa->pelanggaran)->sum(function($p) { return $p->jenisPelanggaran->poin ?? 0; });
                return [
                    'nama' => $siswa->nama_siswa,
                    'kelas' => $siswa->kelas->nama_kelas ?? 'N/A',
                    'total_pelanggaran' => $totalPelanggaran,
                    'poin_pelanggaran' => $poinPelanggaran,
                    'prestasi' => count($siswa->prestasi)
                ];
            })->sortByDesc('poin_pelanggaran')->take(10);
        @endphp
        
        @if($siswaPerluPerhatian->count() > 0)
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Pelanggaran</th>
                    <th>Poin Pelanggaran</th>
                    <th>Prestasi</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaPerluPerhatian as $index => $siswa)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $siswa['nama'] }}</strong></td>
                    <td class="text-center">{{ $siswa['kelas'] }}</td>
                    <td class="text-center">{{ $siswa['total_pelanggaran'] }}</td>
                    <td class="text-center">{{ $siswa['poin_pelanggaran'] }}</td>
                    <td class="text-center">{{ $siswa['prestasi'] }}</td>
                    <td>
                        @if($siswa['poin_pelanggaran'] > 50)
                            Konseling intensif + Orang tua
                        @elseif($siswa['poin_pelanggaran'] > 30)
                            Konseling rutin
                        @else
                            Monitoring ketat
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center">Tidak ada siswa yang perlu perhatian khusus 👍</p>
        @endif
    </div>
    @endif

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ session('user')->nama ?? 'Kepala Sekolah' }}</p>
        <p>Kepala Sekolah</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>