# 📊 Entity Relationship Diagram (ERD) - SIPANG POLRI

## 1. Mermaid ERD (Copy ke mermaid.live)

```mermaid
erDiagram
    POLSEK ||--o{ USERS : has
    POLSEK ||--o{ PENGAJUAN : submits
    USERS ||--o{ PENGAJUAN : creates
    USERS ||--o{ PENGAJUAN_STATUS_LOG : logs
    KEGIATAN ||--o{ PENGAJUAN : categorized_by
    KEGIATAN ||--o{ PENGAJUAN_DETAIL : contains
    PENGAJUAN ||--o{ PENGAJUAN_DETAIL : has
    PENGAJUAN ||--o{ PENGAJUAN_STATUS_LOG : tracks

    POLSEK {
        int id PK
        string nama
        string kode UK
        text alamat
        timestamp created_at
        timestamp updated_at
    }

    USERS {
        int id PK
        string username UK
        string password
        string nama_lengkap
        string email
        enum role "USER_SATFUNG|USER_POLSEK|ADMIN_BAGREN|ADMIN_SIKEU"
        int polsek_id FK
        string jabatan
        string nip
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    KEGIATAN {
        int id PK
        string nama
        string kode UK
        decimal pagu
        enum sumber_dana "RM|PNBP"
        timestamp created_at
        timestamp updated_at
    }

    PENGAJUAN {
        int id PK
        string nomor_surat UK
        date tanggal_pengajuan
        string bulan_pengajuan
        year tahun_pengajuan
        enum sumber_dana "RM|PNBP"
        text uraian
        string penanggung_jawab
        string bendahara_pengeluaran_pembantu
        int kegiatan_id FK
        decimal jumlah_diajukan
        decimal jumlah_pagu
        decimal sisa_pagu
        enum status "DRAFT|TERKIRIM|TERIMA_BERKAS|..."
        text status_keterangan
        int user_id FK
        int polsek_id FK
        string file_path
        timestamp created_at
        timestamp updated_at
    }

    PENGAJUAN_DETAIL {
        int id PK
        int pengajuan_id FK
        int kegiatan_id FK
        string kode
        text uraian_detail
        decimal jumlah
        timestamp created_at
    }

    PENGAJUAN_STATUS_LOG {
        int id PK
        int pengajuan_id FK
        enum status_lama "DRAFT|TERKIRIM|..."
        enum status_baru "DRAFT|TERKIRIM|..."
        text keterangan
        int user_id FK
        timestamp created_at
    }
```

---

## 2. PlantUML Format

```plantuml
@startuml sipang_polri_erd
!define TABLENAME(x) class x << (T,#FFAAAA) >>
!define PK(x) <b>PK: x</b>
!define FK(x) <color:blue><b>FK: x</b></color>

TABLENAME(POLSEK) {
    PK(id) : INT
    nama : VARCHAR(100)
    kode : VARCHAR(20) [UNIQUE]
    alamat : TEXT
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

TABLENAME(USERS) {
    PK(id) : INT
    username : VARCHAR(50) [UNIQUE]
    password : VARCHAR(255)
    nama_lengkap : VARCHAR(100)
    email : VARCHAR(100)
    role : ENUM
    FK(polsek_id) : INT
    jabatan : VARCHAR(100)
    nip : VARCHAR(20)
    is_active : BOOLEAN
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

TABLENAME(KEGIATAN) {
    PK(id) : INT
    nama : VARCHAR(255)
    kode : VARCHAR(50) [UNIQUE]
    pagu : DECIMAL(15,2)
    sumber_dana : ENUM(RM,PNBP)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

TABLENAME(PENGAJUAN) {
    PK(id) : INT
    nomor_surat : VARCHAR(50) [UNIQUE]
    tanggal_pengajuan : DATE
    bulan_pengajuan : VARCHAR(20)
    tahun_pengajuan : YEAR
    sumber_dana : ENUM(RM,PNBP)
    uraian : TEXT
    penanggung_jawab : VARCHAR(100)
    bendahara_pengeluaran_pembantu : VARCHAR(100)
    FK(kegiatan_id) : INT
    jumlah_diajukan : DECIMAL(15,2)
    jumlah_pagu : DECIMAL(15,2)
    sisa_pagu : DECIMAL(15,2)
    status : ENUM
    status_keterangan : TEXT
    FK(user_id) : INT
    FK(polsek_id) : INT
    file_path : VARCHAR(255)
    created_at : TIMESTAMP
    updated_at : TIMESTAMP
}

TABLENAME(PENGAJUAN_DETAIL) {
    PK(id) : INT
    FK(pengajuan_id) : INT
    FK(kegiatan_id) : INT
    kode : VARCHAR(50)
    uraian_detail : TEXT
    jumlah : DECIMAL(15,2)
    created_at : TIMESTAMP
}

TABLENAME(PENGAJUAN_STATUS_LOG) {
    PK(id) : INT
    FK(pengajuan_id) : INT
    status_lama : ENUM
    status_baru : ENUM
    keterangan : TEXT
    FK(user_id) : INT
    created_at : TIMESTAMP
}

POLSEK "1" -- "0..N" USERS : has
POLSEK "1" -- "0..N" PENGAJUAN : submits
USERS "1" -- "0..N" PENGAJUAN : creates
USERS "1" -- "0..N" PENGAJUAN_STATUS_LOG : logs
KEGIATAN "1" -- "0..N" PENGAJUAN : categorized_by
KEGIATAN "1" -- "0..N" PENGAJUAN_DETAIL : contains
PENGAJUAN "1" -- "0..N" PENGAJUAN_DETAIL : has
PENGAJUAN "1" -- "0..N" PENGAJUAN_STATUS_LOG : tracks

@enduml
```

---

## 3. SQL ERD Code (Untuk Database Design Tools)

```sql
-- Complete ERD with all relationships
-- Tables: 6
-- Relationships: 8
-- Primary Keys: 6
-- Foreign Keys: 8

-- POLSEK (Police Stations)
-- Parent of: USERS, PENGAJUAN
-- No dependencies

-- USERS (Users/Accounts)
-- Parent of: PENGAJUAN, PENGAJUAN_STATUS_LOG
-- Foreign Keys: polsek_id -> POLSEK.id

-- KEGIATAN (Activities/Budget Items)
-- Parent of: PENGAJUAN, PENGAJUAN_DETAIL
-- No dependencies

-- PENGAJUAN (Budget Submissions)
-- Parent of: PENGAJUAN_DETAIL, PENGAJUAN_STATUS_LOG
-- Foreign Keys:
--   - kegiatan_id -> KEGIATAN.id
--   - user_id -> USERS.id
--   - polsek_id -> POLSEK.id

-- PENGAJUAN_DETAIL (Submission Line Items)
-- Parent of: None
-- Foreign Keys:
--   - pengajuan_id -> PENGAJUAN.id (CASCADE DELETE)
--   - kegiatan_id -> KEGIATAN.id

-- PENGAJUAN_STATUS_LOG (Status Change History)
-- Parent of: None
-- Foreign Keys:
--   - pengajuan_id -> PENGAJUAN.id (CASCADE DELETE)
--   - user_id -> USERS.id
```

---

## 4. Relational Model Summary

```
POLSEK (6 attributes)
├── 1:N → USERS
├── 1:N → PENGAJUAN

USERS (12 attributes)
├── N:1 ← POLSEK
├── 1:N → PENGAJUAN
└── 1:N → PENGAJUAN_STATUS_LOG

KEGIATAN (5 attributes)
├── 1:N → PENGAJUAN
└── 1:N → PENGAJUAN_DETAIL

PENGAJUAN (20 attributes)
├── N:1 ← KEGIATAN
├── N:1 ← USERS
├── N:1 ← POLSEK
├── 1:N → PENGAJUAN_DETAIL
└── 1:N → PENGAJUAN_STATUS_LOG

PENGAJUAN_DETAIL (5 attributes)
├── N:1 ← PENGAJUAN
└── N:1 ← KEGIATAN

PENGAJUAN_STATUS_LOG (5 attributes)
├── N:1 ← PENGAJUAN
└── N:1 ← USERS
```

---

## 5. Key Statistics

| Metric | Value |
|--------|-------|
| Total Tables | 6 |
| Total Columns | ~62 |
| Primary Keys | 6 |
| Foreign Keys | 8 |
| Unique Constraints | 5 |
| Indexes | 6 |
| Views | 2 |
| Relationships (1:N) | 8 |
| Cardinality | 1:N, N:1 |

---

## 6. Status Enum Values (PENGAJUAN)

```
DRAFT                  → Initial state, not submitted
TERKIRIM              → Submitted to Bagren
TERIMA_BERKAS         → Documents received by Bagren
DISPOSISI_KABAG_REN   → Assigned to Bagian Rena chief
DISPOSISI_WAKA        → Assigned to Deputy Chief
TERIMA_SIKEU          → Received by Finance (SIKEU)
DIBAYARKAN            → Paid/Disbursed
DITOLAK               → Rejected
```

---

## 7. Role Enum Values (USERS)

```
USER_SATFUNG          → Satuan Fungsi (Functional Unit) staff
USER_POLSEK           → Polsek (Police Station) staff
ADMIN_BAGREN          → Bagren (Budget) Admin
ADMIN_SIKEU           → SIKEU (Finance) Admin
```

---

## 8. Views Available

### v_pengajuan_complete
Join pengajuan with users, polsek, and kegiatan for complete submission data.

### v_status_tracking
Track status changes with user information and timestamps.

---

## How to Use This ERD

### Option 1: Mermaid (Free, Online)
1. Go to https://mermaid.live
2. Paste the Mermaid ERD code above
3. Export as image

### Option 2: PlantUML
1. Use PlantUML online: https://www.plantuml.com/plantuml/uml/
2. Paste the PlantUML code
3. Generate diagram

### Option 3: Database Design Tools
- **MySQL Workbench**: Can import SQL and visualize
- **Lucidchart**: Manual recreation with high detail
- **Diagrams.net**: Manual creation with shape library
- **DBDiagram.io**: Paste SQL or create visually

