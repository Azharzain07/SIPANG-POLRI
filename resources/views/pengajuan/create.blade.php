<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Anggaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <label for="tanggal_pengajuan">{{ __('Tanggal Pengajuan') }}</label>
                            <input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}" />
                        </div>
                        
                        <div class="mt-4">
                            <label>{{ __('Nama Polsek') }}</label>
                            <input class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md" type="text" value="{{ Auth::user()->nama_polsek }}" disabled />
                        </div>

                        <div class="mt-4">
                            <label for="category_id">{{ __('Bagian (Opsional)') }}</label>
                            <select name="category_id" id="category_id" class="block mt-1 w-full">
                                <option value="">-- Pilih Bagian --</option>
                                @foreach ($bagianList as $bagian)
                                    <option value="{{ $bagian->id }}" {{ old('category_id') == $bagian->id ? 'selected' : '' }}>
                                        {{ $bagian->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="judul">{{ __('Judul Pengajuan') }}</label>
                            <input id="judul" class="block mt-1 w-full" type="text" name="judul" value="{{ old('judul') }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="jumlah_dana">{{ __('Jumlah Dana (Rp)') }}</label>
                            <input id="jumlah_dana" class="block mt-1 w-full" type="number" name="jumlah_dana" value="{{ old('jumlah_dana') }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="deskripsi">{{ __('Deskripsi') }}</label>
                            <textarea id="deskripsi" name="deskripsi" class="block mt-1 w-full" rows="4" required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="lampiran">{{ __('Lampiran (Opsional, maks: 2MB)') }}</label>
                            <input id="lampiran" class="block mt-1 w-full" type="file" name="lampiran" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase">
                                {{ __('Ajukan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>