<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengajuan Anggaran</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .lampiran-img { max-width: 80px; max-height: 80px; }
    </style>
</head>
<body>
    <h1>Laporan Pengajuan Anggaran</h1>
    <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Pengaju</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Jumlah Dana (Rp)</th>
                <th>Status</th>
                <th>Lampiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuans as $pengajuan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pengajuan->user->name }}</td>
                <td>{{ $pengajuan->judul }}</td>
                <td>{{ $pengajuan->category->nama_kategori }}</td>
                <td>{{ number_format($pengajuan->jumlah_dana, 0, ',', '.') }}</td>
                <td>{{ ucfirst($pengajuan->status) }}</td>
                <td>
                    {{-- LOGIKA BARU UNTUK MENAMPILKAN GAMBAR --}}
                    @if ($pengajuan->lampiran)
                        @php
                            $path = public_path('storage/' . $pengajuan->lampiran);
                            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        @endphp

                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) && file_exists($path))
                            <img src="{{ $path }}" alt="Lampiran" class="lampiran-img">
                        @else
                            File: {{ basename($pengajuan->lampiran) }}
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data yang cocok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>