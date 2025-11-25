<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Pribadi - {{ $data['siswa']->nama_siswa }}</title>
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
        .student-info { margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK BAKTI NUSANTARA 666</h1>
        <h2>RIWAYAT PRIBADI SISWA</h2>
        <p>Generated: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB | Periode: {{ ucfirst(str_replace('_', ' ', $data['periode'] ?? 'Semua Data')) }}</p>
    </div>

    <div class="student-info">
        <h3>📋 Informasi Siswa</h3>
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 20%;"><strong>Nama Siswa:</strong></td>
                <td style="border: none;">{{ $data['siswa']->nama_siswa }}</td>
                <td style="border: none; width: 20%;"><strong>NIS:</strong></td>
                <td style="border: none;">{{ $data['siswa']->nis }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"><strong>Kelas:</strong></td>
                <td style="border: none;">{{ $data['siswa']->kelas->nama_kelas ?? 'N/A' }}</td>
                <td style="border: none;"><strong>Periode:</strong></td>
                <td style="border: none;">{{ ucfirst(str_replace('_', ' ', $data['periode'])) }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <h3>📊 Ringkasan Riwayat</h3>
        <ul>
            <li><strong>Total Pelanggaran:</strong> {{ isset($data['pelanggaran']) ? count($data['pelanggaran']) : 0 }} kasus</li>
            <li><strong>Total Prestasi:</strong> {{ isset($data['prestasi']) ? count($data['prestasi']) : 0 }} prestasi</li>
            <li><strong>Poin Pelanggaran:</strong> {{ isset($data['pelanggaran']) ? collect($data['pelanggaran'])->sum(function($p) { return $p->jenisPelanggaran->poin ?? 0; }) : 0 }} poin</li>
            <li><strong>Poin Prestasi:</strong> {{ isset($data['prestasi']) ? collect($data['prestasi'])->sum(function($pr) { return $pr->jenisPrestasi->poin ?? 0; }) : 0 }} poin</li>
        </ul>
    </div>

    @if(isset($data['pelanggaran']) && count($data['pelanggaran']) > 0)
    <h3>⚠️ Riwayat Pelanggaran</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Jenis Pelanggaran</th>
                <th width="10%">Kategori</th>
                <th width="8%">Poin</th>
                <th width="20%">Guru Pencatat</th>
                <th width="10%">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['pelanggaran'] as $index => $pelanggaran)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $pelanggaran->tanggal ? date('d/m/Y', strtotime($pelanggaran->tanggal)) : '-' }}</td>
                <td>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                <td class="text-center">{{ ucfirst($pelanggaran->jenisPelanggaran->kategori ?? 'N/A') }}</td>
                <td class="text-center">{{ $pelanggaran->jenisPelanggaran->poin ?? 0 }}</td>
                <td>{{ $pelanggaran->guruPencatat->nama_guru ?? 'N/A' }}</td>
                <td class="text-center">{{ ucfirst($pelanggaran->status_verifikasi ?? 'pending') }}</td>
                <td>{{ $pelanggaran->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($data['prestasi']) && count($data['prestasi']) > 0)
    <h3>🏆 Riwayat Prestasi</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Jenis Prestasi</th>
                <th width="10%">Tingkat</th>
                <th width="8%">Peringkat</th>
                <th width="8%">Poin</th>
                <th width="20%">Penyelenggara</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['prestasi'] as $index => $prestasi)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $prestasi->tanggal ? date('d/m/Y', strtotime($prestasi->tanggal)) : '-' }}</td>
                <td>{{ $prestasi->jenisPrestasi->nama_prestasi ?? 'N/A' }}</td>
                <td class="text-center">{{ ucfirst($prestasi->tingkat ?? 'N/A') }}</td>
                <td class="text-center">
                    @if($prestasi->peringkat)
                        {{ $prestasi->peringkat == 1 ? '🥇' : ($prestasi->peringkat == 2 ? '🥈' : ($prestasi->peringkat == 3 ? '🥉' : $prestasi->peringkat)) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ $prestasi->jenisPrestasi->poin ?? 0 }}</td>
                <td>{{ $prestasi->penyelenggara ?? 'N/A' }}</td>
                <td class="text-center">{{ ucfirst($prestasi->status_verifikasi ?? 'pending') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if((!isset($data['pelanggaran']) || count($data['pelanggaran']) == 0) && (!isset($data['prestasi']) || count($data['prestasi']) == 0))
    <div class="text-center" style="margin: 50px 0;">
        <h4>Tidak ada data dalam periode ini</h4>
        <p>Siswa tidak memiliki riwayat pelanggaran atau prestasi dalam periode yang dipilih.</p>
    </div>
    @endif

    <div style="margin-top: 50px; text-align: right;">
        <p>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y') }}</p>
        <p>Sistem Informasi Pelanggaran Siswa</p>
        <br><br><br>
        <p>_________________________</p>
        <p>{{ $data['siswa']->nama_siswa }}</p>
        <p>NIS: {{ $data['siswa']->nis }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>