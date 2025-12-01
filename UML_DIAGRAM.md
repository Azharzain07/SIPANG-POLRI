# 📐 UML Diagram - SIPANG POLRI System

## 1. Class Diagram (Mermaid Format)

```mermaid
classDiagram
    %% Core Classes
    class Database {
        -PDO $pdo
        -static Database $instance
        +getInstance() Database
        +getConnection() PDO
        +close() void
        +query(string sql, array params) array
        +execute(string sql, array params) bool
    }

    class Auth {
        -array $user
        -bool $loggedIn
        +__construct()
        +login(string username, string password) bool
        +logout() void
        +isLoggedIn() bool
        +getUser() array
        +getUserId() int
        +getRole() string
        +checkPermission(string permission) bool
    }

    class PengajuanManager {
        -Database $db
        -Auth $auth
        +__construct(Database db, Auth auth)
        +createPengajuan(array data) bool
        +getPengajuan(int id) array
        +getPengajuanByUser(int userId) array
        +updateStatus(int id, string status) bool
        +approvePengajuan(int id, string notes) bool
        +rejectPengajuan(int id, string reason) bool
        +deletePengajuan(int id) bool
    }

    class FileManager {
        -string $uploadDir
        +__construct()
        +uploadFile(file) string
        +getFile(string filePath) array
        +deleteFile(string filePath) bool
        +validateFile(file) bool
        +sanitizeFileName(string name) string
    }

    %% Domain Models
    class User {
        -int id
        -string username
        -string password
        -string nama_lengkap
        -string email
        -enum role
        -int polsek_id
        -string jabatan
        -string nip
        -bool is_active
        -timestamp created_at
        -timestamp updated_at
    }

    class Polsek {
        -int id
        -string nama
        -string kode
        -text alamat
        -timestamp created_at
        -timestamp updated_at
    }

    class Kegiatan {
        -int id
        -string nama
        -string kode
        -decimal pagu
        -enum sumber_dana
        -timestamp created_at
        -timestamp updated_at
    }

    class Pengajuan {
        -int id
        -string nomor_surat
        -date tanggal_pengajuan
        -string bulan_pengajuan
        -year tahun_pengajuan
        -enum sumber_dana
        -text uraian
        -string penanggung_jawab
        -string bendahara_pengeluaran_pembantu
        -int kegiatan_id
        -decimal jumlah_diajukan
        -decimal jumlah_pagu
        -decimal sisa_pagu
        -enum status
        -text status_keterangan
        -int user_id
        -int polsek_id
        -string file_path
        -timestamp created_at
        -timestamp updated_at
    }

    class PengajuanDetail {
        -int id
        -int pengajuan_id
        -int kegiatan_id
        -string kode
        -text uraian_detail
        -decimal jumlah
        -timestamp created_at
    }

    class PengajuanStatusLog {
        -int id
        -int pengajuan_id
        -enum status_lama
        -enum status_baru
        -text keterangan
        -int user_id
        -timestamp created_at
    }

    %% Request/Response Classes
    class APIRequest {
        -string action
        -array parameters
        -string method
        +getAction() string
        +getParameter(string key) mixed
        +validate(array rules) bool
    }

    class APIResponse {
        -bool success
        -string message
        -mixed data
        -string code
        +toJSON() string
        +send() void
    }

    %% Relationships
    Database --> Auth : uses
    Auth --> User : authenticates
    PengajuanManager --> Database : uses
    PengajuanManager --> Pengajuan : manages
    FileManager --> Pengajuan : handles files
    
    User "1" --> "0..*" Pengajuan : creates
    User "1" --> "0..*" PengajuanStatusLog : logs
    Polsek "1" --> "0..*" User : has
    Polsek "1" --> "0..*" Pengajuan : submits
    Kegiatan "1" --> "0..*" Pengajuan : categorizes
    Kegiatan "1" --> "0..*" PengajuanDetail : contains
    Pengajuan "1" --> "0..*" PengajuanDetail : has
    Pengajuan "1" --> "0..*" PengajuanStatusLog : tracks
```

---

## 2. System Architecture Diagram

```mermaid
graph TB
    subgraph Presentation["Presentation Layer"]
        Admin["admin.php<br/>Admin Dashboard"]
        Riwayat["riwayat.php<br/>History Page"]
        Pengajuan["pengajuan.php<br/>Submission Form"]
        Login["login.php<br/>Login Page"]
    end

    subgraph API["API Layer"]
        Main["api/main.php<br/>Main Handler"]
        AdminDash["api/admin_dashboard.php<br/>Admin Data"]
        FileServ["api/get_file.php<br/>File Service"]
    end

    subgraph Business["Business Logic Layer"]
        Auth["Auth Class<br/>Authentication"]
        PengajuanMgr["PengajuanManager<br/>Submission Logic"]
        FileMgr["FileManager<br/>File Handling"]
    end

    subgraph Data["Data Access Layer"]
        Database["Database Class<br/>PDO Connection"]
        DatabaseConfig["database_config.php<br/>Configuration"]
    end

    subgraph Storage["Storage Layer"]
        MySQL["MySQL Database<br/>sipang_polri"]
        FileUpload["uploads/<br/>File Storage"]
    end

    %% Connections
    Admin -->|JSON| Main
    Riwayat -->|JSON| Main
    Pengajuan -->|JSON| Main
    Login -->|Credentials| Main
    
    Main -->|Routes| Auth
    Main -->|Routes| PengajuanMgr
    AdminDash -->|Get Data| PengajuanMgr
    FileServ -->|Serve Files| FileMgr
    
    Auth -->|Check Permission| Database
    PengajuanMgr -->|Query/Execute| Database
    FileMgr -->|Read/Write| FileUpload
    
    Database -->|PDO| DatabaseConfig
    DatabaseConfig -->|Connection| MySQL
    DatabaseConfig -->|Config| Database
```

---

## 3. Sequence Diagram: Login Flow

```mermaid
sequenceDiagram
    participant User as User
    participant Browser as Browser
    participant API as api/main.php
    participant Auth as Auth Class
    participant DB as Database
    participant Session as Session

    User->>Browser: Enter credentials
    Browser->>API: POST action=login
    API->>Auth: new Auth()
    Auth->>DB: Query user by username
    DB-->>Auth: User data
    Auth->>Auth: verify password (bcrypt)
    Auth->>Session: $_SESSION['user_id'] = id
    Auth->>Session: $_SESSION['role'] = role
    Auth-->>API: Login success
    API-->>Browser: JSON {success: true}
    Browser->>Browser: Redirect to dashboard
```

---

## 4. Sequence Diagram: Pengajuan Approval Flow

```mermaid
sequenceDiagram
    participant User as Admin User
    participant Browser as Browser
    participant API as api/main.php
    participant Modal as Confirmation Modal
    participant PengajuanMgr as PengajuanManager
    participant DB as Database
    participant Log as Status Log

    User->>Browser: Click Setuju button
    Browser->>Modal: showApprovalConfirmation()
    Modal->>Browser: Show confirmation modal
    User->>Browser: Click Setuju in modal
    Browser->>API: POST action=approve_pengajuan_bagren
    API->>PengajuanMgr: approvePengajuan(id)
    PengajuanMgr->>DB: UPDATE pengajuan SET status=TERIMA_BERKAS
    DB-->>PengajuanMgr: Success
    PengajuanMgr->>Log: INSERT INTO pengajuan_status_log
    Log-->>PengajuanMgr: Log created
    PengajuanMgr-->>API: Success
    API-->>Browser: JSON {success: true}
    Browser->>Browser: Close modal
    Browser->>Browser: Reload dashboard
```

---

## 5. Sequence Diagram: File Upload & Serving

```mermaid
sequenceDiagram
    participant User as User
    participant Browser as Browser
    participant Form as pengajuan.php
    participant API as api/main.php
    participant FileServ as api/get_file.php
    participant FileManager as FileManager
    participant DB as Database
    participant Storage as uploads/

    User->>Form: Select file
    Form->>Form: Validate (size, type)
    User->>Form: Submit pengajuan
    Form->>API: POST action=create_pengajuan + file
    API->>FileManager: uploadFile(file)
    FileManager->>FileManager: Sanitize filename
    FileManager->>Storage: Save file
    FileManager-->>API: Return file_path
    API->>DB: INSERT pengajuan (file_path)
    DB-->>API: pengajuan_id
    API-->>Form: Success + pengajuan_id
    
    Note over User,Browser: Later: User views file
    User->>Browser: Click "Lihat Berkas"
    Browser->>FileServ: GET ?id=pengajuan_id&action=view
    FileServ->>FileServ: Check auth & permission
    FileServ->>DB: SELECT pengajuan WHERE id
    DB-->>FileServ: file_path
    FileServ->>Storage: Read file
    FileServ-->>Browser: File content (PDF)
    Browser->>Browser: Open/Download file
```

---

## 6. Role-Based Access Control (RBAC)

```mermaid
graph TD
    User["User"]
    
    subgraph Roles["User Roles"]
        SATFUNG["USER_SATFUNG<br/>Functional Unit Staff"]
        POLSEK["USER_POLSEK<br/>Police Station"]
        BAGREN["ADMIN_BAGREN<br/>Budget Admin"]
        SIKEU["ADMIN_SIKEU<br/>Finance Admin"]
    end
    
    subgraph Permissions["Permissions"]
        CREATE["can_create_pengajuan"]
        EDIT["can_edit_own_pengajuan"]
        VIEW["can_view_own_pengajuan"]
        SUBMIT["can_submit_pengajuan"]
        VIEWALL["can_view_all_pengajuan"]
        APPROVE["can_approve_pengajuan"]
        EXPORT["can_export_data"]
        MANAGE["can_manage_users"]
        CHANGE_STATUS["can_change_status"]
        DISPOSISI["can_disposisi"]
        SEND_SIKEU["can_send_to_sikeu"]
        PROCESS_PAY["can_process_payment"]
    end
    
    User -->|SELECT| SATFUNG
    User -->|SELECT| POLSEK
    User -->|SELECT| BAGREN
    User -->|SELECT| SIKEU
    
    SATFUNG --> CREATE
    SATFUNG --> EDIT
    SATFUNG --> VIEW
    SATFUNG --> SUBMIT
    
    POLSEK --> CREATE
    POLSEK --> EDIT
    POLSEK --> VIEW
    POLSEK --> SUBMIT
    
    BAGREN --> VIEWALL
    BAGREN --> APPROVE
    BAGREN --> EXPORT
    BAGREN --> MANAGE
    BAGREN --> CHANGE_STATUS
    BAGREN --> DISPOSISI
    BAGREN --> SEND_SIKEU
    
    SIKEU --> VIEWALL
    SIKEU --> APPROVE
    SIKEU --> EXPORT
    SIKEU --> PROCESS_PAY
```

---

## 7. Database Entity Relationship

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
    }

    USERS {
        int id PK
        string username UK
        string password
        string nama_lengkap
        enum role
        int polsek_id FK
    }

    PENGAJUAN {
        int id PK
        string nomor_surat UK
        enum status
        int user_id FK
        int polsek_id FK
        int kegiatan_id FK
        string file_path
    }

    PENGAJUAN_DETAIL {
        int id PK
        int pengajuan_id FK
        int kegiatan_id FK
        decimal jumlah
    }

    PENGAJUAN_STATUS_LOG {
        int id PK
        int pengajuan_id FK
        int user_id FK
        enum status_baru
    }

    KEGIATAN {
        int id PK
        string kode UK
        decimal pagu
    }
```

---

## 8. State Diagram: Pengajuan Status Flow

```mermaid
stateDiagram-v2
    [*] --> DRAFT: User creates
    
    DRAFT --> TERKIRIM: User submits
    DRAFT --> [*]: User deletes
    
    TERKIRIM --> TERIMA_BERKAS: Bagren receives
    TERKIRIM --> DITOLAK: Bagren rejects
    
    TERIMA_BERKAS --> DISPOSISI_KABAG_REN: Assign to chief
    TERIMA_BERKAS --> DITOLAK: Bagren rejects
    
    DISPOSISI_KABAG_REN --> DISPOSISI_WAKA: Assign to deputy
    DISPOSISI_KABAG_REN --> DITOLAK: Chief rejects
    
    DISPOSISI_WAKA --> TERIMA_SIKEU: Deputy approves
    DISPOSISI_WAKA --> DITOLAK: Deputy rejects
    
    TERIMA_SIKEU --> DIBAYARKAN: Finance processes
    TERIMA_SIKEU --> DITOLAK: Finance rejects
    
    DIBAYARKAN --> [*]: Payment complete
    DITOLAK --> [*]: Rejected (end)
```

---

## 9. Component Diagram

```mermaid
graph LR
    subgraph Client["Client Layer"]
        HTML["HTML/CSS/JS<br/>Frontend Pages"]
        MODAL["Modal Components<br/>Confirmation UI"]
    end
    
    subgraph Server["Server Layer"]
        PAGES["PHP Pages<br/>Presentation"]
        API["REST API<br/>main.php"]
        CONFIG["Configuration<br/>database_config.php"]
    end
    
    subgraph Business["Business Layer"]
        AUTH["Authentication<br/>Auth.php"]
        MANAGER["Pengajuan Manager<br/>Business Logic"]
        FILE["File Manager<br/>Upload/Download"]
    end
    
    subgraph Data["Data Layer"]
        DB["Database Class<br/>PDO"]
        MYSQL["MySQL<br/>sipang_polri"]
    end
    
    subgraph Storage["Storage"]
        UPLOADS["File Storage<br/>uploads/"]
    end
    
    HTML -->|AJAX| API
    MODAL -->|JavaScript| API
    PAGES -->|Include| CONFIG
    API -->|Use| AUTH
    API -->|Use| MANAGER
    API -->|Use| FILE
    AUTH -->|Query| DB
    MANAGER -->|Execute| DB
    FILE -->|Read/Write| UPLOADS
    DB -->|PDO| MYSQL
```

---

## 10. Activity Diagram: Create & Submit Pengajuan

```mermaid
graph TD
    A["Start<br/>User Access pengajuan.php"] -->B["Load Form<br/>Get kegiatan & polsek"]
    B -->C["User fills form"]
    C -->D["User selects file"]
    D -->E{Validate<br/>File size/type?}
    E -->|Invalid| F["Show Error<br/>Prompt to select again"]
    F -->D
    E -->|Valid| G["User clicks Submit"]
    G -->H["POST to api/main.php<br/>action=create_pengajuan"]
    H -->I["API receives<br/>request"]
    I -->J["Check authentication<br/>isLoggedIn?"]
    J -->|No| K["Return error<br/>Session expired"]
    K -->L["End<br/>Show login page"]
    J -->|Yes| M["Validate input<br/>Check required fields"]
    M -->|Invalid| N["Return error<br/>Show validation message"]
    N -->L
    M -->|Valid| O["Upload file<br/>Sanitize filename"]
    O -->P["Save file to<br/>uploads/"]
    P -->Q["Insert pengajuan<br/>with file_path"]
    Q -->R["Get pengajuan_id"]
    R -->S["Return success<br/>with pengajuan_id"]
    S -->T["Browser shows<br/>Success message"]
    T -->U["Redirect to<br/>riwayat.php"]
    U -->V["End"]
```

---

## 11. Package Diagram

```mermaid
graph TB
    subgraph Config["📁 config/"]
        DBCFG["database_config.php<br/>Constants & Config"]
    end
    
    subgraph API["📁 api/"]
        MAIN["main.php<br/>Request Router"]
        ADMIN["admin_dashboard.php<br/>Admin Data"]
        FILE["get_file.php<br/>File Service"]
    end
    
    subgraph Pages["📁 Pages/"]
        ADMIN_PAGE["admin.php"]
        RIWAYAT["riwayat.php"]
        PENGAJUAN_PAGE["pengajuan.php"]
        LOGIN["login.php"]
    end
    
    subgraph Includes["📁 includes/"]
        HEADER["header.php"]
        FOOTER["footer.php"]
        NAVBAR["navbar.php"]
        AUTH_GUARD["auth_guard.php"]
    end
    
    subgraph Assets["📁 assets/"]
        CSS["📁 css/<br/>Stylesheets"]
        JS["📁 js/<br/>JavaScript"]
    end
    
    subgraph Database["📁 database/"]
        SQL["📁 SQL files<br/>Schema & Data"]
    end
    
    subgraph Uploads["📁 uploads/"]
        FILES["📄 Pengajuan files"]
    end
    
    MAIN --> DBCFG
    MAIN --> Config
    ADMIN_PAGE --> MAIN
    RIWAYAT --> MAIN
    PENGAJUAN_PAGE --> MAIN
    LOGIN --> MAIN
    PAGES --> Includes
    Pages --> Assets
    Config --> Database
    MAIN -.Upload.-> Uploads
    FILE -.Serve.-> Uploads
```

---

## 12. Deployment Architecture

```mermaid
graph TB
    Users["👥 End Users<br/>Browser"]
    
    subgraph Server["🖥️ Web Server<br/>XAMPP/Apache"]
        PHP["PHP 7.0+<br/>Interpreter"]
        FILES["Static Files<br/>CSS, JS, Images"]
    end
    
    subgraph Database["🗄️ Database Server<br/>MySQL/MariaDB"]
        MYSQL["sipang_polri<br/>Database"]
    end
    
    subgraph Storage["💾 File Storage<br/>Local Filesystem"]
        UPLOADS["uploads/<br/>Directory"]
    end
    
    Users -->|HTTP/HTTPS| Server
    PHP -->|PDO| Database
    PHP -->|Read/Write| Storage
    Files -->|Static Content| Users
    MYSQL -->|Data| PHP
```

---

## 13. API Endpoint Mapping

```mermaid
graph LR
    API["API<br/>api/main.php"]
    
    AUTH["🔐 Authentication"]
    PENGAJUAN["📄 Pengajuan"]
    ADMIN["⚙️ Admin"]
    DATA["📊 Data"]
    UTIL["🔧 Utility"]
    
    API --> AUTH
    API --> PENGAJUAN
    API --> ADMIN
    API --> DATA
    API --> UTIL
    
    AUTH --> LOGIN["action=login"]
    AUTH --> LOGOUT["action=logout"]
    AUTH --> SESSION["action=check_session"]
    AUTH --> INFO["action=get_user_info"]
    
    PENGAJUAN --> CREATE["action=create_pengajuan"]
    PENGAJUAN --> GETRIWAYAT["action=get_riwayat"]
    PENGAJUAN --> UPDATE["action=update_status"]
    PENGAJUAN --> APPROVE["action=approve_pengajuan_*"]
    PENGAJUAN --> REJECT["action=reject_pengajuan"]
    PENGAJUAN --> DELETE["action=delete_pengajuan"]
    
    ADMIN --> DASHBOARD["action=get_dashboard_data"]
    ADMIN --> STATS["api/admin_dashboard.php"]
    
    DATA --> EXPORT["action=export_data"]
    DATA --> DOWNLOAD["action=download_*"]
    DATA --> LOG["action=get_status_log"]
    
    UTIL --> KEGIATAN["action=get_kegiatan"]
    UTIL --> POLSEK["action=get_polsek"]
    UTIL --> FILE["api/get_file.php"]
```

---

## UML Documentation Summary

| Diagram | Purpose | Key Elements |
|---------|---------|--------------|
| **Class** | Object structure & relationships | Database, Auth, PengajuanManager, Models |
| **Architecture** | System layers & communication | Presentation, API, Business, Data |
| **Sequence (Login)** | Authentication flow | User, Browser, Auth, DB |
| **Sequence (Approval)** | Pengajuan approval workflow | Modal, API, Manager, Database |
| **Sequence (Files)** | File upload & serving | Form, API, FileManager, Storage |
| **RBAC** | Role permissions mapping | 4 Roles × 12+ Permissions |
| **ERD** | Database structure | 6 Tables, 8 Relationships |
| **State** | Pengajuan status transitions | 8 States, Rejection path |
| **Component** | System components & interfaces | Client, Server, Business, Data |
| **Activity** | User workflows | Create/Submit process flow |
| **Package** | Directory structure | Code organization |
| **Deployment** | Runtime infrastructure | Servers, Database, Storage |
| **API Endpoints** | API operations mapping | 20+ Endpoints |

---

## How to Use These Diagrams

### Online Viewing:
1. **Mermaid** (all diagrams): Copy code → https://mermaid.live
2. **PlantUML**: Copy specific diagram → https://www.plantuml.com/plantuml/uml/

### Export as Image:
- Mermaid: Right-click → Export as PNG/SVG
- PlantUML: Generate → Download image

### Documentation:
- Share with team for architecture understanding
- Use for onboarding new developers
- Reference for maintenance & troubleshooting
