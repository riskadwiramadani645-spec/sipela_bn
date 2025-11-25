<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Efektivitas Kebijakan Disiplin - Kepala Sekolah</title>
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
        .rekomendasi { margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .metric-box { display: inline-block; width: 23%; margin: 1%; padding: 15px; text-align: center; border-radius: 5px; }
        .metric-excellent { background-color: #d4edda; }
        .metric-good { background-color: #d1ecf1; }
        .metric-fair { background-color: #fff3cd; }
        .metric-poor { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>ANALISIS EFEKTIVITAS KEBIJAKAN DISIPLIN</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    @php
        // Calculate key metrics
        $totalSiswa = \App\Models\Siswa::count();
        $totalPelanggaran = isset($data['data']) ? count($data['data']) : 0;
        $pelanggaranDenganSanksi = isset($data['data']) ? collect($data['data'])->whereNotNull('sanksi')->count() : 0;
        $efektivitasSanksi = $totalPelanggaran > 0 ? round(($pelanggaranDenganSanksi / $totalPelanggaran) * 100, 1) : 0;
        $tingkatKedisiplinan = $totalSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $totalSiswa * 5)) : 100;
        $pelanggaranBerulang = isset($data['data']) ? collect($data['data'])->groupBy('siswa_id')->filter(function($group) { return count($group) > 1; })->count() : 0;
        $tingkatPencegahan = $totalSiswa > 0 ? max(0, 100 - ($pelanggaranBerulang / $totalSiswa * 10)) : 100;
    @endphp

    <div class="summary">
        <h3>📊 Key Performance Indicators (KPI)</h3>
        <div style="text-align: center;">
            <div class="metric-box {{ $efektivitasSanksi >= 90 ? 'metric-excellent' : ($efektivitasSanksi >= 75 ? 'metric-good' : ($efektivitasSanksi >= 60 ? 'metric-fair' : 'metric-poor')) }}">
                <h4>{{ $efektivitasSanksi }}%</h4>
                <p>Efektivitas Sanksi</p>
            </div>
            <div class="metric-box {{ $tingkatKedisiplinan >= 90 ? 'metric-excellent' : ($tingkatKedisiplinan >= 75 ? 'metric-good' : ($tingkatKedisiplinan >= 60 ? 'metric-fair' : 'metric-poor')) }}">
                <h4>{{ round($tingkatKedisiplinan, 1) }}%</h4>
                <p>Tingkat Kedisiplinan</p>
            </div>
            <div class="metric-box {{ $tingkatPencegahan >= 90 ? 'metric-excellent' : ($tingkatPencegahan >= 75 ? 'metric-good' : ($tingkatPencegahan >= 60 ? 'metric-fair' : 'metric-poor')) }}">
                <h4>{{ round($tingkatPencegahan, 1) }}%</h4>
                <p>Tingkat Pencegahan</p>
            </div>
            <div class="metric-box {{ $pelanggaranBerulang <= 5 ? 'metric-excellent' : ($pelanggaranBerulang <= 15 ? 'metric-good' : ($pelanggaranBerulang <= 30 ? 'metric-fair' : 'metric-poor')) }}">
                <h4>{{ $pelanggaranBerulang }}</h4>
                <p>Pelanggaran Berulang</p>
            </div>
        </div>
    </div>

    <div class="summary">
        <h3>📈 Tren Pelanggaran Bulanan</h3>
        @php
            $trenBulanan = collect();
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i);
                $jumlah = \App\Models\Pelanggaran::whereMonth('tanggal', $bulan->month)
                                                ->whereYear('tanggal', $bulan->year)
                                                ->count();
                $trenBulanan->push([
                    'bulan' => $bulan->format('M Y'),
                    'jumlah' => $jumlah
                ]);
            }
        @endphp
        
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Pelanggaran</th>
                    <th>Perubahan</th>
                    <th>Status Tren</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trenBulanan as $index => $bulan)
                @php
                    $perubahan = 0;
                    $statusTren = 'Stabil';
                    if ($index > 0) {
                        $sebelumnya = $trenBulanan[$index - 1]['jumlah'];
                        if ($sebelumnya > 0) {
                            $perubahan = round((($bulan['jumlah'] - $sebelumnya) / $sebelumnya) * 100, 1);
                            $statusTren = $perubahan > 10 ? 'Meningkat' : ($perubahan < -10 ? 'Menurun' : 'Stabil');
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $bulan['bulan'] }}</td>
                    <td class="text-center">{{ $bulan['jumlah'] }}</td>
                    <td class="text-center">
                        @if($perubahan > 0)
                            <span style="color: #dc3545;">+{{ $perubahan }}%</span>
                        @elseif($perubahan < 0)
                            <span style="color: #28a745;">{{ $perubahan }}%</span>
                        @else
                            <span style="color: #6c757d;">0%</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span style="color: {{ $statusTren == 'Menurun' ? '#28a745' : ($statusTren == 'Meningkat' ? '#dc3545' : '#6c757d') }};">
                            {{ $statusTren }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h3>⚖️ Analisis Efektivitas Sanksi per Kategori</h3>
        @php
            $analisisSanksi = \DB::table('pelanggaran')
                ->join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.jenis_pelanggaran_id')
                ->leftJoin('sanksi', 'pelanggaran.pelanggaran_id', '=', 'sanksi.pelanggaran_id')
                ->select(
                    'jenis_pelanggaran.kategori',
                    \DB::raw('COUNT(pelanggaran.pelanggaran_id) as total_pelanggaran'),
                    \DB::raw('COUNT(sanksi.sanksi_id) as total_sanksi'),
                    \DB::raw('COUNT(CASE WHEN sanksi.status_pelaksanaan = "selesai" THEN 1 END) as sanksi_selesai')
                )
                ->groupBy('jenis_pelanggaran.kategori')
                ->get();
        @endphp
        
        <table>
            <thead>
                <tr>
                    <th>Kategori Pelanggaran</th>
                    <th>Total Pelanggaran</th>
                    <th>Sanksi Diberikan</th>
                    <th>Sanksi Selesai</th>
                    <th>Efektivitas Pemberian</th>
                    <th>Efektivitas Pelaksanaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analisisSanksi as $kategori)
                @php
                    $efektivitasPemberian = $kategori->total_pelanggaran > 0 ? round(($kategori->total_sanksi / $kategori->total_pelanggaran) * 100, 1) : 0;
                    $efektivitasPelaksanaan = $kategori->total_sanksi > 0 ? round(($kategori->sanksi_selesai / $kategori->total_sanksi) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ ucfirst($kategori->kategori) }}</td>
                    <td class="text-center">{{ $kategori->total_pelanggaran }}</td>
                    <td class="text-center">{{ $kategori->total_sanksi }}</td>
                    <td class="text-center">{{ $kategori->sanksi_selesai }}</td>
                    <td class="text-center">{{ $efektivitasPemberian }}%</td>
                    <td class="text-center">{{ $efektivitasPelaksanaan }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="rekomendasi">
        <h3>💡 Rekomendasi Kebijakan</h3>
        <ol>
            @if($efektivitasSanksi < 75)
            <li><strong>Perbaikan Sistem Sanksi:</strong> Tingkatkan efektivitas pemberian sanksi dari {{ $efektivitasSanksi }}% menjadi minimal 85% melalui monitoring yang lebih ketat.</li>
            @endif
            
            @if($pelanggaranBerulang > 20)
            <li><strong>Program Pencegahan:</strong> Implementasikan program pencegahan khusus untuk mengurangi {{ $pelanggaranBerulang }} kasus pelanggaran berulang.</li>
            @endif
            
            @if($tingkatKedisiplinan < 80)
            <li><strong>Pembinaan Karakter:</strong> Perkuat program pembinaan karakter untuk meningkatkan tingkat kedisiplinan dari {{ round($tingkatKedisiplinan, 1) }}%.</li>
            @endif
            
            <li><strong>Monitoring Berkala:</strong> Lakukan evaluasi kebijakan disiplin setiap 3 bulan untuk memastikan efektivitas yang berkelanjutan.</li>
            
            <li><strong>Pelatihan Guru:</strong> Tingkatkan kapasitas guru dalam penanganan pelanggaran dan pemberian sanksi yang edukatif.</li>
            
            <li><strong>Keterlibatan Orang Tua:</strong> Perkuat komunikasi dengan orang tua untuk mendukung program kedisiplinan sekolah.</li>
        </ol>
    </div>

    <div class="summary">
        <h3>🎯 Target Perbaikan</h3>
        <table>
            <thead>
                <tr>
                    <th>Indikator</th>
                    <th>Kondisi Saat Ini</th>
                    <th>Target 3 Bulan</th>
                    <th>Target 6 Bulan</th>
                    <th>Strategi Utama</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Efektivitas Sanksi</td>
                    <td class="text-center">{{ $efektivitasSanksi }}%</td>
                    <td class="text-center">{{ min(100, $efektivitasSanksi + 10) }}%</td>
                    <td class="text-center">{{ min(100, $efektivitasSanksi + 20) }}%</td>
                    <td>Monitoring ketat & follow-up</td>
                </tr>
                <tr>
                    <td>Tingkat Kedisiplinan</td>
                    <td class="text-center">{{ round($tingkatKedisiplinan, 1) }}%</td>
                    <td class="text-center">{{ min(100, round($tingkatKedisiplinan + 5, 1)) }}%</td>
                    <td class="text-center">{{ min(100, round($tingkatKedisiplinan + 10, 1)) }}%</td>
                    <td>Program pembinaan karakter</td>
                </tr>
                <tr>
                    <td>Pelanggaran Berulang</td>
                    <td class="text-center">{{ $pelanggaranBerulang }} kasus</td>
                    <td class="text-center">{{ max(0, $pelanggaranBerulang - 5) }} kasus</td>
                    <td class="text-center">{{ max(0, $pelanggaranBerulang - 10) }} kasus</td>
                    <td>Konseling intensif</td>
                </tr>
            </tbody>
        </table>
    </div>

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