# 📑 Dokumentasi Proyek SIPANG POLRI  
_Sistem Anggaran Polisi Republik Indonesia_

---

## 💡 Konsep Sistem

**SIPANG POLRI** adalah sistem berbasis web untuk mengelola proses **pengajuan anggaran** dan **riwayat anggaran** pada lingkungan Kepolisian Republik Indonesia.  
Sistem ini dibuat menggunakan **Vanilla PHP** mulai dari nol, dengan tujuan:

- Mempermudah proses pengajuan anggaran oleh unit Polsek  
- Mempercepat proses verifikasi dan persetujuan anggaran oleh Admin Bagren  
- Menyediakan pelacakan status pengajuan secara real-time  
- Mengurangi proses manual dan meningkatkan efisiensi  

---

## 🚀 Fitur Utama

### 🏠 Halaman Awal
- Dashboard  
- Login  
- Pengajuan Anggaran  
- Riwayat Pengajuan  
- Logout  

---

## 🔐 Autentikasi
- Login menggunakan Username & Password  
- Dilengkapi **CAPTCHA** untuk keamanan login  

---

## 👥 Multi User

### 👮‍♂️ Admin (Bagian Perencanaan / Bagren)
- Mengelola Data Master  
- Melihat semua pengajuan dari seluruh unit  
- Menyetujui pengajuan  
- Menolak pengajuan  
- Menghapus pengajuan  

### 👤 User (Polsek)
- Login  
- Mengisi Formulir Pengajuan Anggaran  
- Melihat Riwayat Pengajuan  
- Menghapus Riwayat (opsional)  

---

## 🔑 Akun Default

| Role | Username | Password | Keterangan |
|------|-----------|-----------|-------------|
| **Admin** | `ADMIN.BAGREN` | `password` | Mengelola data & pengajuan |
| **User** | `plsk.grt.kta` | `password` | Membuat & melihat pengajuan |

---

## 🗂️ ERD  
![ERD SIPANG POLRI](ERDAJAY.png)

---

## 🔷 UML Diagram  
![UML SIPANG POLRI](UMLAJAY.png)

---

## 🖥️ Teknologi yang Digunakan

| Kategori | Teknologi |
|---------|-----------|
| Backend | **Vanilla PHP** |
| Frontend | HTML, CSS, JavaScript |
| Database | MySQL |
| Web Server | Apache (XAMPP) |
| Editor | VSCode |
| Browser | Chrome |

---

## 🛠️ Tools Pendukung
- XAMPP  
- VSCode  
- Chrome  
- phpMyAdmin  

---

# ⚙️ Persyaratan Instalasi

Pastikan perangkat Anda memiliki:

- PHP 7.x / 8.x  
- XAMPP (Apache & MySQL)  
- Web Browser  
- MySQL  

---

# 📥 Cara Instalasi SIPANG POLRI

### **1. Clone Repository**

```bash
git clone https://github.com/Azharzain07/SIPANG-POLRI.git

2. Masuk ke Direktori
cd SIPANG-POLRI

3. Pindahkan Folder ke XAMPP

Letakkan project ke folder berikut:

htdocs/SIPANG-POLRI

4. Siapkan Database

Buka phpMyAdmin

Buat database baru:

db_sipangpolri


Import file SQL bila disediakan (database.sql).

5. Jalankan Project

Buka browser dan akses:

http://localhost/SIPANG-POLRI/

🎉 Selesai!

Sekarang SIPANG POLRI sudah bisa dijalankan sepenuhnya.
Jika Anda membutuhkan dokumentasi tambahan seperti:

Flowchart

Sequence Diagram

Activity Diagram

Struktur Folder 
