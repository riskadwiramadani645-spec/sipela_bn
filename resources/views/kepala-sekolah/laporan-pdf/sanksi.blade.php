<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sanksi Detail - Kepala Sekolah</title>
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
        .status-selesai { background-color: #d4edda; }
        .status-proses { background-color: #fff3cd; }
        .status-pending { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>LAPORAN SANKSI DETAIL</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="summary">
        <h3>⚖️ Ringkasan Sanksi</h3>
        <ul>
            <li><strong>Total Sanksi:</strong> {{ isset($data['data']) ? count($data['data']) : 0 }} sanksi</li>
            <li><strong>Sanksi Selesai:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_pelaksanaan', 'selesai')->count() : 0 }} sanksi</li>
            <li><strong>Sanksi Dalam Proses:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_pelaksanaan', 'proses')->count() : 0 }} sanksi</li>
            <li><strong>Sanksi Pending:</strong> {{ isset($data['data']) ? collect($data['data'])->where('status_pelaksanaan', 'pending')->count() : 0 }} sanksi</li>
        </ul>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th width="18%">Pelanggaran</th>
                <th width="15%">Jenis Sanksi</th>
                <th width="10%">Tanggal Mulai</th>
                <th width="10%">Tanggal Selesai</th>
                <th width="8%">Status</th>
                <th width="13%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse((isset($data['data']) ? $data['data'] : []) as $index => $sanksi)
            <tr class="{{ 
                ($sanksi->status_pelaksanaan ?? '') == 'selesai' ? 'status-selesai' : 
                (($sanksi->status_pelaksanaan ?? '') == 'proses' ? 'status-proses' : 'status-pending')
            }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $sanksi->pelanggaran->siswa->nama_siswa ?? 'N/A' }}</strong></td>
                <td class="text-center">{{ $sanksi->pelanggaran->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                <td>{{ $sanksi->pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                <td>{{ $sanksi->jenisSanksi->nama_sanksi ?? 'N/A' }}</td>
                <td class="text-center">{{ $sanksi->tanggal_mulai ? date('d/m/Y', strtotime($sanksi->tanggal_mulai)) : '-' }}</td>
                <td class="text-center">{{ $sanksi->tanggal_selesai ? date('d/m/Y', strtotime($sanksi->tanggal_selesai)) : '-' }}</td>
                <td class="text-center">
                    <span style="background: {{ 
                        ($sanksi->status_pelaksanaan ?? '') == 'selesai' ? '#28a745' : 
                        (($sanksi->status_pelaksanaan ?? '') == 'proses' ? '#ffc107' : '#dc3545')
                    }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">
                        {{ ucfirst($sanksi->status_pelaksanaan ?? 'pending') }}
                    </span>
                </td>
                <td>{{ $sanksi->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data sanksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($data['data']) && count($data['data']) > 0)
    <div class="summary">
        <h3>📊 Efektivitas Sanksi</h3>
        @php
            $totalSanksi = count($data['data']);
            $sanksiSelesai = collect($data['data'])->where('status_pelaksanaan', 'selesai')->count();
            $efektivitas = $totalSanksi > 0 ? round(($sanksiSelesai / $totalSanksi) * 100, 1) : 0;
        @endphp
        <p><strong>Tingkat Penyelesaian Sanksi:</strong> {{ $efektivitas }}% ({{ $sanksiSelesai }} dari {{ $totalSanksi }} sanksi)</p>
        
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Jenis Sanksi</th>
                    <th>Total</th>
                    <th>Selesai</th>
                    <th>Proses</th>
                    <th>Pending</th>
                    <th>Efektivitas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jenisSanksi = collect($data['data'])->groupBy(function($item) {
                        return $item->jenisSanksi->nama_sanksi ?? 'N/A';
                    });
                @endphp
                @foreach($jenisSanksi as $jenis => $items)
                @php
                    $total = count($items);
                    $selesai = $items->where('status_pelaksanaan', 'selesai')->count();
                    $proses = $items->where('status_pelaksanaan', 'proses')->count();
                    $pending = $items->where('status_pelaksanaan', 'pending')->count();
                    $efektivitasJenis = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $jenis }}</td>
                    <td class="text-center">{{ $total }}</td>
                    <td class="text-center">{{ $selesai }}</td>
                    <td class="text-center">{{ $proses }}</td>
                    <td class="text-center">{{ $pending }}</td>
                    <td class="text-center">{{ $efektivitasJenis }}%</td>
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