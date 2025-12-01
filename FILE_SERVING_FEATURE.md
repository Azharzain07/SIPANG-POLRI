# 📄 Fitur "Lihat Berkas" - Implementation & Fixes

## Masalah yang Ditemukan

❌ Saat user klik tombol "Lihat Berkas", muncul error 404 (halaman tidak tersedia)
❌ File upload tidak tersimpan ke database
❌ Tidak ada endpoint untuk serve/download file

---

## Solusi yang Diimplementasikan

### 1. ✅ Tambah Kolom file_path ke Database

**File:** `migrate_add_file_path.php` (NEW)

```sql
ALTER TABLE pengajuan ADD COLUMN file_path VARCHAR(255) NULL AFTER status_keterangan;
```

**Cara Jalankan:**

```
http://localhost/migrate_add_file_path.php
```

Hasilnya: Column `file_path` ditambah ke tabel `pengajuan`

---

### 2. ✅ Update handleCreatePengajuan - Simpan File Path

**File:** `api/main.php`
**Perubahan:**

- File diupload dan disimpan di folder `uploads/`
- Path file tersimpan di database kolom `file_path`
- Insert statement sekarang include `file_path` parameter

**Alur:**

```
User upload file
    ↓
handleCreatePengajuan() di-jalankan
    ↓
File dipindahkan ke uploads/
    ↓
Path disimpan di pengajuan.file_path
    ↓
✓ File tersimpan
```

---

### 3. ✅ Buat Endpoint Serve File

**File:** `api/get_file.php` (NEW)
**Fitur:**

- Authenticate user (hanya user yang login bisa akses)
- Check permission (hanya owner atau admin bisa lihat file)
- Support mode `view` (inline) dan `download`
- Log semua akses file

**Endpoint Format:**

```
GET /api/get_file.php?id=<pengajuan_id>&action=<view|download>

Contoh:
- View:     /api/get_file.php?id=5&action=view
- Download: /api/get_file.php?id=5&action=download
```

---

### 4. ✅ Update Frontend untuk Pakai Endpoint Baru

#### riwayat.php

**Perubahan:**

- `viewPDF()` sekarang terima `pengajuanId` bukan `filePath`
- Endpoint: `api/get_file.php?id=${pengajuanId}&action=view`
- Button "Lihat Berkas" pass item.id

#### admin.php

**Perubahan:**

- `viewPDF()` update sama seperti riwayat.php
- Download button: `api/get_file.php?id=${item.id}&action=download`
- Endpoint menangani permission check

---

## Files yang Dimodifikasi/Dibuat

| File                         | Status     | Deskripsi                                |
| ---------------------------- | ---------- | ---------------------------------------- |
| `migrate_add_file_path.php`  | NEW        | Script untuk add kolom ke database       |
| `api/get_file.php`           | NEW        | Endpoint untuk serve/download file       |
| `api/main.php`               | ✏️ UPDATED | handleCreatePengajuan - simpan file_path |
| `api/admin_dashboard.php`    | ✏️ UPDATED | Query include file_path                  |
| `riwayat.php`                | ✏️ UPDATED | viewPDF() & button onclick               |
| `admin.php`                  | ✏️ UPDATED | viewPDF() & file serving links           |
| `config/database_config.php` | ✏️ UPDATED | createPengajuan() include file_path      |

---

## Setup & Testing

### Step 1: Jalankan Migration

```
1. Buka browser: http://localhost/migrate_add_file_path.php
2. Tunggu hasilnya ✓ Column 'file_path' added successfully
```

### Step 2: Test Pengajuan dengan File

```
1. Login sebagai USER_SATFUNG
2. Buat pengajuan baru
3. Upload dokumen (PDF/JPG/PNG/XLS/XLSX)
4. Klik tombol "Kirim Pengajuan"
5. Periksa database: file_path harus ada nilainya
```

### Step 3: Test View Berkas

```
1. Login sebagai USER_SATFUNG
2. Buka halaman Riwayat
3. Klik tombol "Lihat Berkas"
4. Modal harus menampilkan file PDF/gambar
5. Tidak boleh 404 error
```

### Step 4: Test Download Berkas

```
1. Login sebagai ADMIN_BAGREN
2. Buka Admin Dashboard
3. Klik icon Download pada pengajuan
4. File harus ter-download dengan nama original
```

---

## Security Features

✅ **Authentication:** Hanya user yang login bisa akses
✅ **Authorization:** Hanya owner atau admin bisa lihat file
✅ **File Validation:** Mime type checked
✅ **Path Validation:** No directory traversal allowed
✅ **Logging:** Semua akses file di-log

---

## Troubleshooting

### Q: File tidak ter-download

**A:** Periksa:

1. File ada di folder `uploads/`
2. Permission folder 755
3. `file_path` ada di database

### Q: 404 saat klik Lihat Berkas

**A:** Kemungkinan:

1. Kolom `file_path` belum di-add (jalankan migrate_add_file_path.php)
2. File_path NULL di database (re-upload pengajuan)
3. File tidak ada di uploads/ folder

### Q: File tidak ter-upload

**A:** Periksa:

1. Folder `uploads/` writable (chmod 755)
2. File size < 5MB
3. Format file diizinkan (PDF, JPG, PNG, XLS, XLSX)

---

## Database Check

```sql
-- Periksa kolom file_path ada
DESCRIBE pengajuan;

-- Periksa data ada
SELECT id, nomor_surat, file_path FROM pengajuan WHERE file_path IS NOT NULL;
```

---

## Endpoint Testing (dengan curl/Postman)

```bash
# Test View (Browser inline)
curl -b cookies.txt \
  "http://localhost/api/get_file.php?id=1&action=view"

# Test Download
curl -b cookies.txt \
  "http://localhost/api/get_file.php?id=1&action=download" \
  -o downloaded_file.pdf
```

---

## Next Steps

1. ✅ Run `migrate_add_file_path.php`
2. ✅ Test upload file dengan pengajuan baru
3. ✅ Test view berkas di riwayat page
4. ✅ Test download di admin dashboard
5. Monitor error logs untuk issues

**Status:** Fitur "Lihat Berkas" seharusnya sudah berfungsi 100% ✓
