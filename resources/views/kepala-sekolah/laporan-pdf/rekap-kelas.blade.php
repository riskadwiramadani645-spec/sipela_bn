<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Per Kelas - Kepala Sekolah</title>
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
        .kelas-baik { background-color: #d4edda; }
        .kelas-sedang { background-color: #fff3cd; }
        .kelas-perlu-perhatian { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>REKAP LAPORAN PER KELAS</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="summary">
        <h3>📚 Ringkasan Per Kelas</h3>
        <ul>
            <li><strong>Total Kelas:</strong> {{ isset($data['data']) ? count($data['data']) : 0 }} kelas</li>
            <li><strong>Total Siswa:</strong> {{ isset($data['data']) ? collect($data['data'])->sum(function($kelas) { return count($kelas->siswa); }) : 0 }} siswa</li>
            <li><strong>Rata-rata Siswa per Kelas:</strong> {{ isset($data['data']) && count($data['data']) > 0 ? round(collect($data['data'])->sum(function($kelas) { return count($kelas->siswa); }) / count($data['data']), 1) : 0 }} siswa</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Nama Kelas</th>
                <th width="15%">Wali Kelas</th>
                <th width="8%">Jml Siswa</th>
                <th width="8%">Pelanggaran</th>
                <th width="8%">Prestasi</th>
                <th width="10%">Skor Disiplin</th>
                <th width="10%">Rata² Poin</th>
                <th width="10%">Status Kelas</th>
                <th width="11%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $kelas)
            @php
                $jumlahSiswa = count($kelas->siswa);
                $totalPelanggaran = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->pelanggaran); });
                $totalPrestasi = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->prestasi); });
                $skorDisiplin = $jumlahSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $jumlahSiswa * 10)) : 100;
                $rataPoin = $jumlahSiswa > 0 ? round($totalPelanggaran / $jumlahSiswa, 1) : 0;
                
                $statusKelas = 'Baik';
                $cssClass = 'kelas-baik';
                if ($skorDisiplin < 70) {
                    $statusKelas = 'Perlu Perhatian';
                    $cssClass = 'kelas-perlu-perhatian';
                } elseif ($skorDisiplin < 85) {
                    $statusKelas = 'Sedang';
                    $cssClass = 'kelas-sedang';
                }
            @endphp
            <tr class="{{ $cssClass }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $kelas->nama_kelas ?? 'N/A' }}</strong></td>
                <td>{{ $kelas->waliKelas->nama_guru ?? 'Belum Ada' }}</td>
                <td class="text-center">{{ $jumlahSiswa }}</td>
                <td class="text-center">{{ $totalPelanggaran }}</td>
                <td class="text-center">{{ $totalPrestasi }}</td>
                <td class="text-center">{{ round($skorDisiplin, 1) }}%</td>
                <td class="text-center">{{ $rataPoin }}</td>
                <td class="text-center">
                    <span style="background: {{ 
                        $statusKelas == 'Baik' ? '#28a745' : 
                        ($statusKelas == 'Sedang' ? '#ffc107' : '#dc3545')
                    }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">
                        {{ $statusKelas }}
                    </span>
                </td>
                <td>
                    @if($statusKelas == 'Perlu Perhatian')
                        Tingkatkan pembinaan
                    @elseif($statusKelas == 'Sedang')
                        Monitoring rutin
                    @else
                        Pertahankan prestasi
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data kelas</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($data['data']) && count($data['data']) > 0)
    <div class="summary">
        <h3>🏆 Ranking Kelas Terdisiplin</h3>
        @php
            $rankingKelas = collect($data['data'])->map(function($kelas) {
                $jumlahSiswa = count($kelas->siswa);
                $totalPelanggaran = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->pelanggaran); });
                $totalPrestasi = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->prestasi); });
                $skorDisiplin = $jumlahSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $jumlahSiswa * 10)) : 100;
                
                return [
                    'nama_kelas' => $kelas->nama_kelas,
                    'wali_kelas' => $kelas->waliKelas->nama_guru ?? 'Belum Ada',
                    'jumlah_siswa' => $jumlahSiswa,
                    'total_pelanggaran' => $totalPelanggaran,
                    'total_prestasi' => $totalPrestasi,
                    'skor_disiplin' => $skorDisiplin
                ];
            })->sortByDesc('skor_disiplin')->take(10);
        @endphp
        
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Skor Disiplin</th>
                    <th>Prestasi</th>
                    <th>Pelanggaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingKelas as $index => $kelas)
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
                    <td><strong>{{ $kelas['nama_kelas'] }}</strong></td>
                    <td>{{ $kelas['wali_kelas'] }}</td>
                    <td class="text-center">{{ round($kelas['skor_disiplin'], 1) }}%</td>
                    <td class="text-center">{{ $kelas['total_prestasi'] }}</td>
                    <td class="text-center">{{ $kelas['total_pelanggaran'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h3>📊 Statistik Keseluruhan</h3>
        @php
            $totalKelasBaik = collect($data['data'])->filter(function($kelas) {
                $jumlahSiswa = count($kelas->siswa);
                $totalPelanggaran = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->pelanggaran); });
                $skorDisiplin = $jumlahSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $jumlahSiswa * 10)) : 100;
                return $skorDisiplin >= 85;
            })->count();
            
            $totalKelasSedang = collect($data['data'])->filter(function($kelas) {
                $jumlahSiswa = count($kelas->siswa);
                $totalPelanggaran = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->pelanggaran); });
                $skorDisiplin = $jumlahSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $jumlahSiswa * 10)) : 100;
                return $skorDisiplin >= 70 && $skorDisiplin < 85;
            })->count();
            
            $totalKelasPerluPerhatian = collect($data['data'])->filter(function($kelas) {
                $jumlahSiswa = count($kelas->siswa);
                $totalPelanggaran = collect($kelas->siswa)->sum(function($siswa) { return count($siswa->pelanggaran); });
                $skorDisiplin = $jumlahSiswa > 0 ? max(0, 100 - ($totalPelanggaran / $jumlahSiswa * 10)) : 100;
                return $skorDisiplin < 70;
            })->count();
        @endphp
        
        <ul>
            <li><strong>Kelas Kategori Baik (≥85%):</strong> {{ $totalKelasBaik }} kelas</li>
            <li><strong>Kelas Kategori Sedang (70-84%):</strong> {{ $totalKelasSedang }} kelas</li>
            <li><strong>Kelas Perlu Perhatian (<70%):</strong> {{ $totalKelasPerluPerhatian }} kelas</li>
        </ul>
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