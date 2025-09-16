<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pengajuan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Kartu Sisa Budget -->
            <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg shadow">
                <p class="font-semibold text-blue-800">Sisa Budget Tahunan Anda Saat Ini:</p>
                <p class="text-3xl font-bold text-blue-900">Rp {{ number_format(Auth::user()->budget_tahunan, 2, ',', '.') }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Polsek</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bagian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->judul }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->user->nama_polsek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->category->nama_kategori ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($pengajuan->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                            @if($pengajuan->status == 'diterima') bg-green-100 text-green-800 @endif
                                            @if($pengajuan->status == 'ditolak') bg-red-100 text-red-800 @endif
                                        ">
                                            {{ ucfirst($pengajuan->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if ($pengajuan->status == 'pending')
                                            <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
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

