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

            {{-- ========================================================== --}}
            {{-- ========= BAGIAN YANG DIPERBARUI DIMULAI DARI SINI ========= --}}
            {{-- ========================================================== --}}

            <div class="mt-8 flex justify-end items-center space-x-3">
                
                {{-- Tombol Kembali (Secondary Button) --}}
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Kembali
                </a>

                @php
                    $userIsOwner = (auth()->id() == $pengajuan->user_id);
                    $isPolsekOrBagian = (auth()->user()->role == 'polsek' || auth()->user()->role == 'bagian');
                    $isPending = ($pengajuan->status_npwp == 'pending' && $pengajuan->status_ppk == 'pending');
                @endphp

                {{-- Tombol aksi ini hanya muncul untuk pemilik pengajuan dengan role Polsek/Bagian & jika statusnya pending --}}
                @if($userIsOwner && $isPolsekOrBagian && $isPending)
                    
                    {{-- Tombol Edit (Primary Button - Biru) --}}
                    <a href="{{ route('pengajuan.edit', $pengajuan) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Edit Pengajuan
                    </a>
                    
                    {{-- Tombol Hapus (Danger Button - Merah) --}}
                    <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Hapus
                        </button>
                    </form>

                @endif
            </div>
            
            {{-- ========================================================== --}}
            {{-- ========== BAGIAN YANG DIPERBARUI BERAKHIR DI SINI ========== --}}
            {{-- ========================================================== --}}

        </div>
    </div>
</x-app-layout>