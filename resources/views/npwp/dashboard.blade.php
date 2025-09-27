<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Persetujuan NPWP') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Daftar Pengajuan Menunggu Persetujuan Anda</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tanggal</th>
                                    <th class="px-6 py-3 text-left">Pengaju</th>
                                    <th class="px-6 py-3 text-left">Uraian</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4">{{ $pengajuan->tanggal_pengajuan->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->user->name }}</td>
                                        <td class="px-6 py-4">{{ Str::limit($pengajuan->uraian, 70) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Detail</a>
                                            
                                            {{-- ========================================================== --}}
                                            {{-- ============ PERBAIKAN NAMA RUTE DI SINI ============= --}}
                                            {{-- ========================================================== --}}

                                            <form action="{{ route('npwp.pengajuan.approveNpwp', $pengajuan) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin MENYETUJUI pengajuan ini?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold">Setujui</button>
                                            </form>
                                            
                                            <form action="{{ route('npwp.pengajuan.rejectNpwp', $pengajuan) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin MENOLAK pengajuan ini?');">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Tolak</button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada pengajuan yang perlu direview saat ini.
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