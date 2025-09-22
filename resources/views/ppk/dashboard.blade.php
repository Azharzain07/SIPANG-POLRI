<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Persetujuan PPK') }}
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
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Daftar Pengajuan Menunggu Persetujuan Final</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengaju</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uraian</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->user->name }}</td>
                                        <td class="px-6 py-4">{{ Str::limit($pengajuan->uraian, 70) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <form action="{{ route('ppk.pengajuan.approve', $pengajuan) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Setujui</button>
                                            </form>
                                            <form action="{{ route('ppk.pengajuan.reject', $pengajuan) }}" method="POST" class="inline-block ml-4">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Tolak</button>
                                            </form>
                                            <td class="px-6 py-4 text-center">
    <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600">Detail</a>
</td>
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