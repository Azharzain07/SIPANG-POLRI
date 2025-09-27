<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pengajuan Anggaran Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uraian</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Anggaran</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status NPWP</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status PPK</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr>
                                        {{-- 1. Tanggal diformat lebih sederhana berkat $casts di Model --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->tanggal_pengajuan->format('d-m-Y') }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($pengajuan->uraian, 50) }}</td>
                                        
                                       <td class="px-6 py-4 whitespace-nowrap text-right font-semibold">
                                            Rp {{ number_format($pengajuan->details->sum('jumlah_diajukan'), 0, ',', '.') }}
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
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>

                                            {{-- ========================================================== --}}
                                            {{-- ========= 2. LOGIKA AKSI DIPERBAIKI DI SINI ============= --}}
                                            {{-- ========================================================== --}}
                                            
                                            {{-- Tombol Edit & Hapus hanya muncul jika:
                                                 1. User adalah PEMILIK pengajuan (dicek oleh Policy via @can).
                                                 2. DAN status pengajuan masih 'pending'.
                                            --}}
                                            @if ($pengajuan->status_npwp == 'pending' && $pengajuan->status_ppk == 'pending')
                                                @can('update', $pengajuan)
                                                    <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="text-blue-600 hover:text-blue-900 ml-4">Edit</a>
                                                @endcan

                                                @can('delete', $pengajuan)
                                                    <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Anda belum memiliki riwayat pengajuan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>