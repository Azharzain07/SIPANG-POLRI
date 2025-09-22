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
                    <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            
                            <fieldset id="informasi-dasar" class="border p-4 rounded-md">
                                <legend class="text-lg font-medium px-2">Informasi Dasar</legend>
                                <div class="space-y-4 pt-4">
                                    <div>
                                        <label for="tanggal_pengajuan" class="block font-medium text-sm text-gray-700">Tanggal Pengajuan</label>
                                        <input id="tanggal_pengajuan" type="date" name="tanggal_pengajuan" class="form-input mt-1 block w-full rounded-md shadow-sm border-gray-300" required min="{{ date('Y-m-d') }}" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}">
                                    </div>
                                    <div>
                                        <label for="ppk" class="block font-medium text-sm text-gray-700">PPK</label>
                                        <select id="ppk" name="ppk_user_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" disabled required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($ppkUsers as $ppk) <option value="{{ $ppk->id }}">{{ $ppk->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="npwp" class="block font-medium text-sm text-gray-700">NPWP</label>
                                        <select id="npwp" name="npwp_user_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" disabled required>
                                            <option value="">-- Pilih --</option>
                                             @foreach ($npwpUsers as $npwp) <option value="{{ $npwp->id }}">{{ $npwp->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="sumber_dana" class="block font-medium text-sm text-gray-700">Sumber Dana</label>
                                        <select id="sumber_dana" name="sumber_dana_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" disabled required>
                                            <option value="">-- Pilih --</option>
                                            @foreach ($sumberDanas as $sumber) <option value="{{ $sumber->id }}">{{ $sumber->nama_sumber_dana }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="uraian" class="block font-medium text-sm text-gray-700">Uraian</label>
                                        <textarea id="uraian" name="uraian" class="form-textarea mt-1 block w-full rounded-md shadow-sm border-gray-300" rows="3" disabled required>{{ old('uraian') }}</textarea>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset id="detail-kegiatan" class="border p-4 rounded-md" style="display: none;">
                                <legend class="text-lg font-medium px-2">Detail Kegiatan</legend>
                                <div class="space-y-4 pt-4">
                                    <div>
                                        <label for="program" class="block font-medium text-sm text-gray-700">Program</label>
                                        <select id="program" name="program_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" required>
                                            <option value="">-- Pilih Program --</option>
                                            @foreach ($programs as $program) <option value="{{ $program->id }}">{{ $program->nama_program }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="aktivitas" class="block font-medium text-sm text-gray-700">Aktivitas</label>
                                        <select id="aktivitas" name="activity_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" disabled required>
                                            <option value="">-- Pilih Program Dulu --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="kro" class="block font-medium text-sm text-gray-700">KRO</label>
                                        <select id="kro" name="kro_id" class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300" disabled required>
                                            <option value="">-- Pilih Aktivitas Dulu --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="lokasi" class="block font-medium text-sm text-gray-700">Lokasi</label>
                                        <input id="lokasi" type="text" class="form-input mt-1 block w-full bg-gray-100 rounded-md shadow-sm border-gray-300" placeholder="Akan terisi otomatis" readonly>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset id="rincian-anggaran" class="border p-4 rounded-md mt-6" style="display: none;">
                                <legend class="text-lg font-medium px-2">Rincian Anggaran</legend>
                                <div class="space-y-4 pt-4" x-data="{ openModal: false, closeModal() { this.openModal = false; } }">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Akun Belanja</th>
                                                    <th class="px-4 py-2 text-left">COA</th>
                                                    <th class="px-4 py-2 text-right">Jumlah Diajukan</th>
                                                    <th class="px-4 py-2 text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rincian-body"></tbody>
                                        </table>
                                    </div>
                                    <button @click="openModal = true" type="button" id="tombol-akun-belanja" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md">
                                        + Tambah Akun Belanja
                                    </button>
                                    <div x-show="openModal" @keydown.escape.window="closeModal()" class="fixed inset-0 bg-gray-600 bg-opacity-50 h-full w-full z-50 flex items-center justify-center" x-cloak>
                                        <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                                            <div class="mt-3">
                                                <h3 class="text-lg font-medium text-gray-900">Pilih Akun Belanja dan COA</h3>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <label for="popup_akun">Akun Belanja</label>
                                                        <select id="popup_akun" class="mt-1 block w-full"></select>
                                                    </div>
                                                    <div>
                                                        <label for="popup_coa">COA</label>
                                                        <select id="popup_coa" class="mt-1 block w-full" disabled></select>
                                                    </div>
                                                    <div id="info_pagu" class="mt-2 text-sm text-gray-600" style="display: none;">
                                                        Pagu: <span id="pagu_value"></span> | Sisa Pagu: <span id="sisa_pagu_value"></span>
                                                    </div>
                                                    <div>
                                                        <label for="popup_jumlah">Jumlah Diajukan</label>
                                                        <input id="popup_jumlah" type="number" class="mt-1 block w-full" disabled>
                                                        <small id="error_pagu" class="text-red-500" style="display: none;">Jumlah melebihi sisa pagu!</small>
                                                    </div>
                                                </div>
                                                <div class="items-center px-4 py-3 mt-4 text-right">
                                                    <button @click="closeModal()" type="button" class="px-4 py-2 bg-gray-200 rounded-md mr-2">Batal</button>
                                                    <button id="tambah-rincian-btn" type="button" @click="closeModal()" class="px-4 py-2 bg-green-600 text-white rounded-md" disabled>Tambah ke Rincian</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                             <div class="flex justify-end pt-4">
                                <button type="submit" id="submit-button" class="px-6 py-2 bg-gray-800 text-white rounded-md disabled:opacity-50" disabled>
                                    Kirim Pengajuan
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Referensi ke semua elemen form ---
            const elements = {
                tanggal: document.getElementById('tanggal_pengajuan'),
                ppk: document.getElementById('ppk'),
                npwp: document.getElementById('npwp'),
                sumberDana: document.getElementById('sumber_dana'),
                uraian: document.getElementById('uraian'),
                detailKegiatan: document.getElementById('detail-kegiatan'),
                program: document.getElementById('program'),
                aktivitas: document.getElementById('aktivitas'),
                kro: document.getElementById('kro'),
                lokasi: document.getElementById('lokasi'),
                rincianAnggaran: document.getElementById('rincian-anggaran'),
                tombolAkunBelanja: document.getElementById('tombol-akun-belanja'),
                submitButton: document.getElementById('submit-button')
            };

            const popupElements = {
                akun: document.getElementById('popup_akun'),
                coa: document.getElementById('popup_coa'),
                infoPagu: document.getElementById('info_pagu'),
                paguValue: document.getElementById('pagu_value'),
                sisaPaguValue: document.getElementById('sisa_pagu_value'),
                jumlah: document.getElementById('popup_jumlah'),
                errorPagu: document.getElementById('error_pagu'),
                tambahBtn: document.getElementById('tambah-rincian-btn'),
                rincianBody: document.getElementById('rincian-body'),
            };
            
            let coaDataStore = [];

            // --- Fungsi Helper ---
            function populateDropdown(selectElement, data, defaultOption, valueKey, textKey) {
                selectElement.innerHTML = `<option value="">-- ${defaultOption} --</option>`;
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    selectElement.appendChild(option);
                });
            }
            
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            }

            // --- Logika Berurutan ---
            elements.tanggal.addEventListener('change', () => elements.ppk.disabled = !elements.tanggal.value);
            elements.ppk.addEventListener('change', () => elements.npwp.disabled = !elements.ppk.value);
            elements.npwp.addEventListener('change', () => elements.sumberDana.disabled = !elements.npwp.value);
            elements.sumberDana.addEventListener('change', () => elements.uraian.disabled = !elements.sumberDana.value);
            elements.uraian.addEventListener('input', () => {
                elements.detailKegiatan.style.display = elements.uraian.value.trim() !== '' ? 'block' : 'none';
                elements.program.disabled = elements.uraian.value.trim() === '';
            });

            // --- Logika Dropdown Dinamis ---
            elements.program.addEventListener('change', function() {
                const programId = this.value;
                elements.aktivitas.disabled = true;
                elements.aktivitas.innerHTML = '<option value="">Memuat...</option>';
                elements.kro.disabled = true;
                elements.kro.innerHTML = '<option value="">-- Pilih Aktivitas Dulu --</option>';
                elements.lokasi.value = '';
                elements.rincianAnggaran.style.display = 'none';

                if (programId) {
                    fetch(`/get-activities/${programId}`)
                        .then(response => response.json())
                        .then(data => {
                            populateDropdown(elements.aktivitas, data, 'Pilih Aktivitas', 'id', 'nama_aktivitas');
                            elements.aktivitas.disabled = false;
                        });
                } else {
                    elements.aktivitas.innerHTML = '<option value="">-- Pilih Program Dulu --</option>';
                }
            });

            elements.aktivitas.addEventListener('change', function() {
                const activityId = this.value;
                elements.kro.disabled = true;
                elements.kro.innerHTML = '<option value="">Memuat...</option>';
                elements.lokasi.value = '';
                elements.rincianAnggaran.style.display = 'none';

                if (activityId) {
                    fetch(`/get-kros/${activityId}`)
                        .then(response => response.json())
                        .then(data => {
                            populateDropdown(elements.kro, data, 'Pilih KRO', 'id', 'nama_kro');
                            elements.kro.disabled = false;
                        });
                } else {
                    elements.kro.innerHTML = '<option value="">-- Pilih Aktivitas Dulu --</option>';
                }
            });
            
            elements.kro.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                elements.lokasi.value = selectedOption.dataset.lokasi || '';
                elements.rincianAnggaran.style.display = this.value ? 'block' : 'none';
            });
            
            // --- Logika Popup ---
            elements.tombolAkunBelanja.addEventListener('click', function() {
                fetch('/get-accounts')
                    .then(response => response.json())
                    .then(data => {
                        populateDropdown(popupElements.akun, data, 'Pilih Akun Belanja', 'id', 'nama_akun_belanja');
                    });
            });

            popupElements.akun.addEventListener('change', function() {
                const accountId = this.value;
                popupElements.coa.disabled = true;
                popupElements.coa.innerHTML = '<option value="">Memuat...</option>';
                popupElements.infoPagu.style.display = 'none';
                popupElements.jumlah.disabled = true;
                popupElements.jumlah.value = '';
                popupElements.tambahBtn.disabled = true;

                if (accountId) {
                    fetch(`/get-coas/${accountId}`)
                        .then(response => response.json())
                        .then(data => {
                            coaDataStore = data;
                            populateDropdown(popupElements.coa, data, 'Pilih COA', 'id', 'nama_coa');
                            popupElements.coa.disabled = false;
                        });
                } else {
                    popupElements.coa.innerHTML = '<option value="">-- Pilih Akun Belanja Dulu --</option>';
                }
            });

            popupElements.coa.addEventListener('change', function() {
                const coaId = this.value;
                popupElements.infoPagu.style.display = 'none';
                popupElements.jumlah.disabled = true;
                popupElements.jumlah.value = '';
                popupElements.tambahBtn.disabled = true;

                if (coaId) {
                    const selectedCoa = coaDataStore.find(coa => coa.id == coaId);
                    if (selectedCoa) {
                        popupElements.paguValue.textContent = formatRupiah(selectedCoa.pagu);
                        popupElements.sisaPaguValue.textContent = formatRupiah(selectedCoa.sisa_pagu);
                        popupElements.infoPagu.style.display = 'block';
                        popupElements.jumlah.disabled = false;
                        popupElements.jumlah.setAttribute('max', selectedCoa.sisa_pagu);
                    }
                }
            });
            
            popupElements.jumlah.addEventListener('input', function() {
                const jumlah = parseFloat(this.value);
                const max = parseFloat(this.getAttribute('max'));
                if (jumlah > max || jumlah <= 0 || isNaN(jumlah)) {
                    popupElements.errorPagu.style.display = 'block';
                    popupElements.tambahBtn.disabled = true;
                } else {
                    popupElements.errorPagu.style.display = 'none';
                    popupElements.tambahBtn.disabled = false;
                }
            });

            popupElements.tambahBtn.addEventListener('click', function() {
                const coaId = popupElements.coa.value;
                const coaText = popupElements.coa.options[popupElements.coa.selectedIndex].text;
                const akunText = popupElements.akun.options[popupElements.akun.selectedIndex].text;
                const jumlah = parseFloat(popupElements.jumlah.value);
                
                if (!coaId || !jumlah) { return; }

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td class="px-4 py-2 border-b">${akunText}</td>
                    <td class="px-4 py-2 border-b">${coaText}</td>
                    <td class="px-4 py-2 text-right border-b">${formatRupiah(jumlah)}</td>
                    <td class="px-4 py-2 text-center border-b">
                        <button type="button" class="text-red-500 hover:text-red-700 remove-item-btn">Hapus</button>
                    </td>
                    <input type="hidden" name="details[${coaId}][coa_id]" value="${coaId}">
                    <input type="hidden" name="details[${coaId}][jumlah_diajukan]" value="${jumlah}">
                `;
                popupElements.rincianBody.appendChild(newRow);
                elements.submitButton.disabled = false;
                // Reset popup
            });
            
            popupElements.rincianBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-item-btn')) {
                    event.target.closest('tr').remove();
                    if (popupElements.rincianBody.children.length === 0) {
                        elements.submitButton.disabled = true;
                    }
                }
            });
        });
    </script>
</x-app-layout>