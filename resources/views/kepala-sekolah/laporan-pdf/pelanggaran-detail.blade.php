<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelanggaran Detail - Kepala Sekolah</title>
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
        .kategori-ringan { background-color: #d4edda; }
        .kategori-sedang { background-color: #fff3cd; }
        .kategori-berat { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN PELANGGARAN DETAIL</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Pelanggaran</h3>
        <ul>
            <li><strong>Total Pelanggaran:</strong> {{ isset($data['data']) ? count($data['data']) : 0 }} kasus</li>
            <li><strong>Sudah Diverifikasi:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_verifikasi', 'diverifikasi')->count() : 0 }} kasus</li>
            <li><strong>Belum Diverifikasi:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_verifikasi', 'pending')->count() : 0 }} kasus</li>
            <li><strong>Sudah Ada Sanksi:</strong> {{ isset($data['data']) ? collect($data['data'])->whereNotNull('sanksi')->count() : 0 }} kasus</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th width="20%">Jenis Pelanggaran</th>
                <th width="8%">Kategori</th>
                <th width="10%">Tanggal</th>
                <th width="12%">Guru Pencatat</th>
                <th width="8%">Status</th>
                <th width="8%">Sanksi</th>
                <th width="8%">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $pelanggaran)
            <tr class="{{ 
                ($pelanggaran->jenisPelanggaran->kategori ?? '') == 'ringan' ? 'kategori-ringan' : 
                (($pelanggaran->jenisPelanggaran->kategori ?? '') == 'sedang' ? 'kategori-sedang' : 
                (($pelanggaran->jenisPelanggaran->kategori ?? '') == 'berat' ? 'kategori-berat' : ''))
            }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td class="text-center">{{ $pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                <td class="text-center">
                    <span style="font-weight: bold; color: {{ 
                        ($pelanggaran->jenisPelanggaran->kategori ?? '') == 'ringan' ? '#28a745' : 
                        (($pelanggaran->jenisPelanggaran->kategori ?? '') == 'sedang' ? '#ffc107' : 
                        (($pelanggaran->jenisPelanggaran->kategori ?? '') == 'berat' ? '#dc3545' : '#6c757d'))
                    }};">
                        {{ ucfirst($pelanggaran->jenisPelanggaran->kategori ?? 'N/A') }}
                    </span>
                </td>
                <td class="text-center">{{ $pelanggaran->tanggal ? date('d/m/Y', strtotime($pelanggaran->tanggal)) : '-' }}</td>
                <td>{{ $pelanggaran->guruPencatat->nama_guru ?? 'N/A' }}</td>
                <td class="text-center">
                    <span style="background: {{ $pelanggaran->status_verifikasi == 'diverifikasi' ? '#28a745' : '#ffc107' }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">
                        {{ ucfirst($pelanggaran->status_verifikasi ?? 'pending') }}
                    </span>
                </td>
                <td class="text-center">
                    @if($pelanggaran->sanksi ?? false)
                        <span style="color: #28a745;">✓</span>
                    @else
                        <span style="color: #dc3545;">✗</span>
                    @endif
                </td>
                <td class="text-center">{{ $pelanggaran->jenisPelanggaran->poin ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data pelanggaran</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($data['data']) && count($data['data']) > 0)
    <div class="summary">
        <h3>📈 Analisis Kategori Pelanggaran</h3>
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                    <th>Total Poin</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kategoris = collect($data['data'])->groupBy(function($item) {
                        return $item->jenisPelanggaran->kategori ?? 'tidak_diketahui';
                    });
                    $totalPelanggaran = count($data['data']);
                @endphp
                @foreach($kategoris as $kategori => $items)
                <tr>
                    <td>{{ ucfirst($kategori) }}</td>
                    <td class="text-center">{{ count($items) }}</td>
                    <td class="text-center">{{ $totalPelanggaran > 0 ? round((count($items) / $totalPelanggaran) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $items->sum(function($item) { return $item->jenisPelanggaran->poin ?? 0; }) }}</td>
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