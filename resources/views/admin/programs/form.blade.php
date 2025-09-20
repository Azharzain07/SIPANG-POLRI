<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($program) ? 'Edit Program' : 'Tambah Program Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ isset($program) ? route('programs.update', $program) : route('programs.store') }}">
                        @csrf
                        @if (isset($program))
                            @method('PUT')
                        @endif

                        <div>
                            <label for="nama_program" class="block font-medium text-sm text-gray-700">Nama Program</label>
                            <input id="nama_program" name="nama_program" type="text" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nama_program', $program->nama_program ?? '') }}" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
