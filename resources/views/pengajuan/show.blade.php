<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                        <div class="p-6 text-gray-900 space-y-6">

                            <div>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-gray-500">Judul Pengajuan</p>
                                        <h3 class="text-2xl font-bold text-gray-800">{{ $pengajuan->uraian }}</h3>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <div>
                                            <span class="text-xs text-gray-500">Status NPWP:</span>
                                            <span class="px-3 py-1 text-sm leading-5 font-semibold rounded-full whitespace-nowrap
                                                @if($pengajuan->status_npwp == 'pending') bg-gray-100 text-gray-800 @endif
                                                @if($pengajuan->status_npwp == 'diterima') bg-green-100 text-green-800 @endif
                                                @if($pengajuan->status_npwp == 'ditolak') bg-red-100 text-red-800 @endif
                                            ">
                                                {{ ucfirst($pengajuan->status_npwp) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500">Status PPK:</span>
                                            <span class="px-3 py-1 text-sm leading-5 font-semibold rounded-full whitespace-nowrap
                                                @if($pengajuan->status_ppk == 'pending') bg-gray-100 text-gray-800 @endif
                                                @if($pengajuan->status_ppk == 'diterima') bg-green-100 text-green-800 @endif
                                                @if($pengajuan->status_ppk == 'ditolak') bg-red-100 text-red-800 @endif
                                            ">
                                                {{ ucfirst($pengajuan->status_ppk) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div>
                                <h4 class="font-semibold text-gray-600">Deskripsi Lengkap</h4>
                                <p class="mt-1 text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $pengajuan->uraian }}</p>
                            </div>

                            <hr>

                            <div>
                                <h4 class="font-semibold text-gray-600">Informasi Detail</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Diajukan oleh</p>
                                        <p class="font-semibold">{{ $pengajuan->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->translatedFormat('d F Y') }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="space-y-8">

                     <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h4 class="font-semibold text-gray-600">Total Dana Diajukan</h4>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">Rp {{ number_format($pengajuan->details->sum('jumlah_diajukan'), 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h4 class="font-semibold text-gray-600 mb-3">Rincian Belanja</h4>
                            <ul class="space-y-2 text-sm">
                                @foreach($pengajuan->details as $detail)
                                <li class="flex justify-between items-center">
                                    <span>{{ $detail->coa->nama_coa }}</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->jumlah_diajukan, 0, ',', '.') }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-200 border rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Kembali
                </a>

                {{-- Tombol aksi ini hanya muncul untuk pemilik pengajuan & JIKA KEDUA STATUS MASIH PENDING --}}
                @if(auth()->id() == $pengajuan->user_id && $pengajuan->status_npwp == 'pending' && $pengajuan->status_ppk == 'pending')
                    <a href="#" class="px-6 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        Edit Pengajuan
                    </a>
                    <form action="#" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                            Hapus
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>