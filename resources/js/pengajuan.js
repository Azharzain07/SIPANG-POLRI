// resources/js/pengajuan.js
document.addEventListener('DOMContentLoaded', function () {
    const programSelect = document.getElementById('program_id');
    const activitySelect = document.getElementById('activity_id');
    const kroSelect = document.getElementById('kro_id');

    console.log('pengajuan.js loaded');

    // helper untuk populate select dari data {id, name}
    function populate(selectEl, data, placeholder, selectedId = null) {
        if (!selectEl) return;
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name || item.nama_activity || item.nama_kro || item.nama; // fallback
            if (selectedId && String(selectedId) === String(item.id)) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    // fetch activities for a program
    async function loadActivities(programId, selectedActivityId = null) {
        if (!activitySelect) return;
        activitySelect.innerHTML = `<option value="">-- Memuat Kegiatan --</option>`;
        kroSelect && (kroSelect.innerHTML = `<option value="">-- Pilih KRO --</option>`);
        if (!programId) {
            activitySelect.innerHTML = `<option value="">-- Pilih Kegiatan --</option>`;
            return;
        }
        try {
            const res = await fetch(`/get-activities/${programId}`);
            const data = await res.json();
            populate(activitySelect, data, '-- Pilih Kegiatan --', selectedActivityId);
        } catch (err) {
            console.error('gagal load activities', err);
        }
    }

    // fetch kros for an activity
    async function loadKros(activityId, selectedKroId = null) {
        if (!kroSelect) return;
        kroSelect.innerHTML = `<option value="">-- Memuat KRO --</option>`;
        if (!activityId) {
            kroSelect.innerHTML = `<option value="">-- Pilih KRO --</option>`;
            return;
        }
        try {
            const res = await fetch(`/get-kros/${activityId}`);
            const data = await res.json();
            populate(kroSelect, data, '-- Pilih KRO --', selectedKroId);
        } catch (err) {
            console.error('gagal load kros', err);
        }
    }

    // Jika halaman edit: ambil nilai data-selected dari atribut dataset
    const selectedProgram = programSelect ? programSelect.dataset.selected : null;
    const selectedActivity = activitySelect ? activitySelect.dataset.selected : null;
    const selectedKro = kroSelect ? kroSelect.dataset.selected : null;

    // Preload jika sedang edit (selectedProgram ada)
    if (selectedProgram) {
        // load activities, dan jika ada selectedActivity -> load kros setelahnya
        loadActivities(selectedProgram, selectedActivity)
            .then(() => {
                if (selectedActivity) {
                    loadKros(selectedActivity, selectedKro);
                }
            });
    }

    // Event listeners (untuk create & edit ketika user mengganti)
    if (programSelect) {
        programSelect.addEventListener('change', function () {
            const programId = this.value;
            loadActivities(programId, null);
        });
    }

    if (activitySelect) {
        activitySelect.addEventListener('change', function () {
            const activityId = this.value;
            loadKros(activityId, null);
        });
    }
});
