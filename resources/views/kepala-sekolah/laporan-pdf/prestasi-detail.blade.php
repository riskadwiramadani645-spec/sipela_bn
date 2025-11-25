<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prestasi Detail - Kepala Sekolah</title>
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
        .tingkat-sekolah { background-color: #d1ecf1; }
        .tingkat-kota { background-color: #d4edda; }
        .tingkat-provinsi { background-color: #fff3cd; }
        .tingkat-nasional { background-color: #f8d7da; }
        .tingkat-internasional { background-color: #e2e3e5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PRESTASI DETAIL</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="summary">
        <h3>🏆 Ringkasan Prestasi</h3>
        <ul>
            <li><strong>Total Prestasi:</strong> {{ isset($data['data']) ? count($data['data']) : 0 }} prestasi</li>
            <li><strong>Sudah Diverifikasi:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_verifikasi', 'diverifikasi')->count() : 0 }} prestasi</li>
            <li><strong>Belum Diverifikasi:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_verifikasi', 'pending')->count() : 0 }} prestasi</li>
            <li><strong>Prestasi Tertinggi:</strong> {{ isset($data['data']) ? collect($data['data'])->where('tingkat', 'internasional')->count() : 0 }} internasional, {{ isset($data['data']) ? collect($data['data'])->where('tingkat', 'nasional')->count() : 0 }} nasional</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th width="20%">Jenis Prestasi</th>
                <th width="10%">Tingkat</th>
                <th width="8%">Peringkat</th>
                <th width="10%">Tanggal</th>
                <th width="12%">Penyelenggara</th>
                <th width="8%">Status</th>
                <th width="6%">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $prestasi)
            <tr class="{{ 
                ($prestasi->tingkat ?? '') == 'sekolah' ? 'tingkat-sekolah' : 
                (($prestasi->tingkat ?? '') == 'kota' ? 'tingkat-kota' : 
                (($prestasi->tingkat ?? '') == 'provinsi' ? 'tingkat-provinsi' : 
                (($prestasi->tingkat ?? '') == 'nasional' ? 'tingkat-nasional' : 
                (($prestasi->tingkat ?? '') == 'internasional' ? 'tingkat-internasional' : ''))))
            }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $prestasi->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td class="text-center">{{ $prestasi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                <td class="text-center">
                    <span style="font-weight: bold; color: {{ 
                        ($prestasi->tingkat ?? '') == 'sekolah' ? '#17a2b8' : 
                        (($prestasi->tingkat ?? '') == 'kota' ? '#28a745' : 
                        (($prestasi->tingkat ?? '') == 'provinsi' ? '#ffc107' : 
                        (($prestasi->tingkat ?? '') == 'nasional' ? '#dc3545' : 
                        (($prestasi->tingkat ?? '') == 'internasional' ? '#6c757d' : '#6c757d'))))
                    }};">
                        {{ ucfirst($prestasi->tingkat ?? 'N/A') }}
                    </span>
                </td>
                <td class="text-center">
                    @if($prestasi->peringkat)
                        <span style="font-weight: bold; color: {{ 
                            $prestasi->peringkat == 1 ? '#ffd700' : 
                            ($prestasi->peringkat == 2 ? '#c0c0c0' : 
                            ($prestasi->peringkat == 3 ? '#cd7f32' : '#6c757d'))
                        }};">
                            {{ $prestasi->peringkat == 1 ? '🥇' : ($prestasi->peringkat == 2 ? '🥈' : ($prestasi->peringkat == 3 ? '🥉' : $prestasi->peringkat)) }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ $prestasi->tanggal ? date('d/m/Y', strtotime($prestasi->tanggal)) : '-' }}</td>
                <td>{{ $prestasi->penyelenggara ?? 'N/A' }}</td>
                <td class="text-center">
                    <span style="background: {{ $prestasi->status_verifikasi == 'diverifikasi' ? '#28a745' : '#ffc107' }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">
                        {{ ucfirst($prestasi->status_verifikasi ?? 'pending') }}
                    </span>
                </td>
                <td class="text-center">{{ $prestasi->jenisPrestasi->poin ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data prestasi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($data['data']) && count($data['data']) > 0)
    <div class="summary">
        <h3>📈 Analisis Tingkat Prestasi</h3>
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Tingkat</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                    <th>Total Poin</th>
                    <th>Rata-rata Peringkat</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tingkats = collect($data['data'])->groupBy(function($item) {
                        return $item->tingkat ?? 'tidak_diketahui';
                    });
                    $totalPrestasi = count($data['data']);
                @endphp
                @foreach($tingkats as $tingkat => $items)
                <tr>
                    <td>{{ ucfirst($tingkat) }}</td>
                    <td class="text-center">{{ count($items) }}</td>
                    <td class="text-center">{{ $totalPrestasi > 0 ? round((count($items) / $totalPrestasi) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $items->sum(function($item) { return $item->jenisPrestasi->poin ?? 0; }) }}</td>
                    <td class="text-center">{{ $items->whereNotNull('peringkat')->count() > 0 ? round($items->whereNotNull('peringkat')->avg('peringkat'), 1) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h3>🎯 Top Performer</h3>
        @php
            $topSiswa = collect($data['data'])->groupBy(function($item) {
                return $item->siswa->nama_siswa ?? 'N/A';
            })->map(function($prestasis, $nama) {
                return [
                    'nama' => $nama,
                    'kelas' => $prestasis->first()->siswa->kelas->nama_kelas ?? 'N/A',
                    'jumlah' => count($prestasis),
                    'total_poin' => $prestasis->sum(function($item) { return $item->jenisPrestasi->poin ?? 0; })
                ];
            })->sortByDesc('total_poin')->take(5);
        @endphp
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jumlah Prestasi</th>
                    <th>Total Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSiswa as $index => $siswa)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $siswa['nama'] }}</strong></td>
                    <td class="text-center">{{ $siswa['kelas'] }}</td>
                    <td class="text-center">{{ $siswa['jumlah'] }}</td>
                    <td class="text-center">{{ $siswa['total_poin'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
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