<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Kolom Kiri: Detail Utama (Digabung menjadi satu kartu) -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                        <div class="p-6 text-gray-900 space-y-6">

                            <!-- Bagian Judul & Status -->
                            <div>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-gray-500">Judul Pengajuan</p>
                                        <h3 class="text-2xl font-bold text-gray-800">{{ $pengajuan->judul }}</h3>
                                    </div>
                                    <span class="px-3 py-1 text-sm leading-5 font-semibold rounded-full whitespace-nowrap
                                        @if($pengajuan->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                        @if($pengajuan->status == 'diterima') bg-green-100 text-green-800 @endif
                                        @if($pengajuan->status == 'ditolak') bg-red-100 text-red-800 @endif
                                    ">
                                        {{ ucfirst($pengajuan->status) }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <!-- Bagian Deskripsi -->
                            <div>
                                <h4 class="font-semibold text-gray-600">Deskripsi Lengkap</h4>
                                <p class="mt-1 text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $pengajuan->deskripsi }}</p>
                            </div>

                            <hr>

                            <!-- Bagian Informasi Detail -->
                            <div>
                                <h4 class="font-semibold text-gray-600">Informasi Detail</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Diajukan oleh</p>
                                        <p class="font-semibold">{{ $pengajuan->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Nama Polsek</p>
                                        <p class="font-semibold">{{ $pengajuan->user->nama_polsek }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->translatedFormat('d F Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Bagian</p>
                                        <p class="font-semibold">{{ $pengajuan->category->nama_kategori ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Dana & Lampiran -->
                <div class="space-y-8">

                     <!-- Kartu Jumlah Dana -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h4 class="font-semibold text-gray-600">Jumlah Dana Diajukan</h4>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">Rp {{ number_format($pengajuan->jumlah_dana, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <!-- Kartu Lampiran -->
                    @if($pengajuan->lampiran)
                        @php
                            $fileExtension = pathinfo($pengajuan->lampiran, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h4 class="font-semibold text-gray-600 mb-3">Lampiran</h4>
                                @if($isImage)
                                    <a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $pengajuan->lampiran) }}" alt="Lampiran Pengajuan" class="w-full h-auto rounded-md border border-gray-200">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Unduh Lampiran ({{ strtoupper($fileExtension) }})
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

            </div>

            <!-- Tombol Aksi -->
            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-200 border rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Kembali
                </a>

                {{-- Tombol aksi ini hanya muncul untuk pemilik pengajuan & jika statusnya pending --}}
                @if(auth()->id() == $pengajuan->user_id && $pengajuan->status == 'pending')
                    <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="px-6 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        Edit Pengajuan
                    </a>
                    <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
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

