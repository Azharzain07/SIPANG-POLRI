<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Anggaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form method="POST" action="#">
                        @csrf
                        <div class="space-y-6">
                            
                            <div>
                                <label for="tanggal_pengajuan" class="block font-medium text-sm text-gray-700">Tanggal Pengajuan</label>
                                <input id="tanggal_pengajuan" type="date" name="tanggal_pengajuan" class="form-input mt-1 block w-full" required min="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label for="ppk" class="block font-medium text-sm text-gray-700">PPK</label>
                                <select id="ppk" name="ppk" class="form-select mt-1 block w-full" disabled required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($ppkUsers as $ppk)
                                        <option value="{{ $ppk->id }}">{{ $ppk->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="npwp" class="block font-medium text-sm text-gray-700">NPWP</label>
                                <select id="npwp" name="npwp" class="form-select mt-1 block w-full" disabled required>
                                    <option value="">-- Pilih --</option>
                                     @foreach ($npwpUsers as $npwp)
                                        <option value="{{ $npwp->id }}">{{ $npwp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sumber_dana" class="block font-medium text-sm text-gray-700">Sumber Dana</label>
                                <select id="sumber_dana" name="sumber_dana" class="form-select mt-1 block w-full" disabled required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($sumberDanas as $sumber)
                                        <option value="{{ $sumber->id }}">{{ $sumber->nama_sumber_dana }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="kppn" class="block font-medium text-sm text-gray-700">KPPN</label>
                                <input id="kppn" type="text" value="096-Garut" class="form-input mt-1 block w-full bg-gray-100" disabled>
                            </div>

                        </div>

                        </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Logika sederhana untuk mengaktifkan input berikutnya
        const tanggalInput = document.getElementById('tanggal_pengajuan');
        const ppkSelect = document.getElementById('ppk');
        const npwpSelect = document.getElementById('npwp');
        const sumberDanaSelect = document.getElementById('sumber_dana');

        tanggalInput.addEventListener('change', function() {
            if (this.value) {
                ppkSelect.disabled = false;
            } else {
                ppkSelect.disabled = true;
            }
        });

        ppkSelect.addEventListener('change', function() {
            if (this.value) {
                npwpSelect.disabled = false;
            } else {
                npwpSelect.disabled = true;
            }
        });
        
        npwpSelect.addEventListener('change', function() {
            if (this.value) {
                sumberDanaSelect.disabled = false;
            } else {
                sumberDanaSelect.disabled = true;
            }
        });

    </script>
</x-app-layout>