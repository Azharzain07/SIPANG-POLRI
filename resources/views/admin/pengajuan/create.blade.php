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
                    <form method="POST" action="{{ route('pengajuan.store') }}">
                        @csrf

                        <div>
                            <label for="judul">{{ __('Judul') }}</label>
                            <input id="judul" class="block mt-1 w-full" type="text" name="judul" :value="old('judul')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <label for="category_id">{{ __('Kategori') }}</label>
                            <select name="category_id" id="category_id" class="block mt-1 w-full">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="jumlah_dana">{{ __('Jumlah Dana (Rp)') }}</label>
                            <input id="jumlah_dana" class="block mt-1 w-full" type="number" name="jumlah_dana" :value="old('jumlah_dana')" required />
                        </div>

                        <div class="mt-4">
                            <label for="deskripsi">{{ __('Deskripsi') }}</label>
                            <textarea id="deskripsi" name="deskripsi" class="block mt-1 w-full" rows="4">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                {{ __('Ajukan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>