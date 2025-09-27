<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Pengajuan Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Gagal!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4">
                        <form action="{{ route('admin.pengajuan.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="text" name="search" class="w-full md:w-1/3 rounded-md shadow-sm border-gray-300" value="{{ request('search') }}" placeholder="Cari berdasarkan judul...">
                            <button type="submit" class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Cari
                            </button>
                            <a href="{{ route('admin.pengajuan.exportPDF', ['search' => request('search')]) }}" class="px-4 py-2 bg-red-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                Ekspor PDF
                            </a>
                            <a href="{{ route('admin.pengajuan.exportExcel', ['search' => request('search')]) }}" class="px-4 py-2 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                Ekspor Excel
                            </a>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-yellow-100 p-4 rounded-lg shadow">
                            <h3 class="font-bold text-yellow-800">Pengajuan Pending</h3>
                            <p class="text-2xl font-semibold text-yellow-900">{{ $pendingCount }}</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg shadow">
                            <h3 class="font-bold text-green-800">Total Dana Disetujui</h3>
                            <p class="text-2xl font-semibold text-green-900">Rp {{ number_format($totalDanaDiterima, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-lg shadow">
                            <h3 class="font-bold text-blue-800">Total Dana Diajukan</h3>
                            <p class="text-2xl font-semibold text-blue-900">Rp {{ number_format($totalDanaDiajukan, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pengaju</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status NPWP</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status PPK</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Persetujuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->tanggal_pengajuan->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ Str::limit($pengajuan->uraian, 40) }}
                                        @if ($pengajuan->lampiran)
                                            <a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank" class="text-xs text-blue-500 block hover:underline">[Lihat Lampiran]</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($pengajuan->status_npwp == 'pending') bg-gray-100 text-gray-800 @endif
                                            @if($pengajuan->status_npwp == 'diterima') bg-green-100 text-green-800 @endif
                                            @if($pengajuan->status_npwp == 'ditolak') bg-red-100 text-red-800 @endif
                                        ">
                                            {{ ucfirst($pengajuan->status_npwp) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($pengajuan->status_ppk == 'pending') bg-gray-100 text-gray-800 @endif
                                            @if($pengajuan->status_ppk == 'diterima') bg-green-100 text-green-800 @endif
                                            @if($pengajuan->status_ppk == 'ditolak') bg-red-100 text-red-800 @endif
                                        ">
                                            {{ ucfirst($pengajuan->status_ppk) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Detail</a>
                                        
                                        {{-- Tampilkan tombol hanya jika status NPWP sudah diterima & status PPK masih pending --}}
                                        @if ($pengajuan->status_npwp == 'diterima' && $pengajuan->status_ppk == 'pending')
                                            <form action="{{ route('admin.pengajuan.approvePpk', $pengajuan) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin MENYETUJUI pengajuan ini?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.pengajuan.rejectPpk', $pengajuan) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin MENOLAK pengajuan ini?');">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Reject</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 ml-4 italic">No Action</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Tidak ada data pengajuan yang cocok.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pengajuans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>