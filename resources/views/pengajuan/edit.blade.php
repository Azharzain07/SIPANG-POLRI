<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengajuan Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('pengajuan.update', $pengajuan->id) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="judul">Judul</label>
                            <input id="judul" class="block mt-1 w-full" type="text" name="judul" value="{{ old('judul', $pengajuan->judul) }}" required autofocus />
                        </div>

                        <div class="mt-4">
                            <label>Kategori</label>
                            <input class="block mt-1 w-full bg-gray-100" type="text" value="{{ $pengajuan->category->nama_kategori }}" disabled />
                        </div>

                        <div class="mt-4">
                            <label for="jumlah_dana">Jumlah Dana (Rp)</label>
                            <input id="jumlah_dana" class="block mt-1 w-full" type="number" name="jumlah_dana" value="{{ old('jumlah_dana', $pengajuan->jumlah_dana) }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="block mt-1 w-full" rows="4">{{ old('deskripsi', $pengajuan->deskripsi) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>