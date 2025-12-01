#!/usr/bin/env python3
"""
ERD Generator untuk SIPANG POLRI Database
Buat visualisasi ERD menggunakan library graphviz
"""

import os

# DOT format untuk Graphviz
dot_code = """
digraph {
    rankdir=TB;
    overlap=false;
    splines=ortho;
    
    // Style definitions
    node [shape=plaintext];
    edge [dir=forward, arrowhead=crow];
    
    // Polsek Table
    polsek [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#FFD7D7"><B>POLSEK</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">nama : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT"><U>kode</U> : VARCHAR(20)</TD></TR>
            <TR><TD ALIGN="LEFT">alamat : TEXT</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
            <TR><TD ALIGN="LEFT">updated_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Users Table
    users [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#D7E8FF"><B>USERS</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><U>username</U> : VARCHAR(50)</TD></TR>
            <TR><TD ALIGN="LEFT">password : VARCHAR(255)</TD></TR>
            <TR><TD ALIGN="LEFT">nama_lengkap : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT">email : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT">role : ENUM</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">polsek_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">jabatan : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT">nip : VARCHAR(20)</TD></TR>
            <TR><TD ALIGN="LEFT">is_active : BOOLEAN</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
            <TR><TD ALIGN="LEFT">updated_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Kegiatan Table
    kegiatan [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#D7FFD7"><B>KEGIATAN</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><U>nama</U> : VARCHAR(255)</TD></TR>
            <TR><TD ALIGN="LEFT"><U>kode</U> : VARCHAR(50)</TD></TR>
            <TR><TD ALIGN="LEFT">pagu : DECIMAL(15,2)</TD></TR>
            <TR><TD ALIGN="LEFT">sumber_dana : ENUM(RM|PNBP)</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
            <TR><TD ALIGN="LEFT">updated_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Pengajuan Table
    pengajuan [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#FFFFD7"><B>PENGAJUAN</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><U>nomor_surat</U> : VARCHAR(50)</TD></TR>
            <TR><TD ALIGN="LEFT">tanggal_pengajuan : DATE</TD></TR>
            <TR><TD ALIGN="LEFT">bulan_pengajuan : VARCHAR(20)</TD></TR>
            <TR><TD ALIGN="LEFT">tahun_pengajuan : YEAR</TD></TR>
            <TR><TD ALIGN="LEFT">sumber_dana : ENUM(RM|PNBP)</TD></TR>
            <TR><TD ALIGN="LEFT">uraian : TEXT</TD></TR>
            <TR><TD ALIGN="LEFT">penanggung_jawab : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT">bendahara_pengeluaran_pembantu : VARCHAR(100)</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">kegiatan_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">jumlah_diajukan : DECIMAL(15,2)</TD></TR>
            <TR><TD ALIGN="LEFT">jumlah_pagu : DECIMAL(15,2)</TD></TR>
            <TR><TD ALIGN="LEFT">sisa_pagu : DECIMAL(15,2)</TD></TR>
            <TR><TD ALIGN="LEFT">status : ENUM(8 values)</TD></TR>
            <TR><TD ALIGN="LEFT">status_keterangan : TEXT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">user_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">polsek_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">file_path : VARCHAR(255)</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
            <TR><TD ALIGN="LEFT">updated_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Pengajuan Detail Table
    pengajuan_detail [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#FFD7FF"><B>PENGAJUAN_DETAIL</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">pengajuan_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">kegiatan_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">kode : VARCHAR(50)</TD></TR>
            <TR><TD ALIGN="LEFT">uraian_detail : TEXT</TD></TR>
            <TR><TD ALIGN="LEFT">jumlah : DECIMAL(15,2)</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Pengajuan Status Log Table
    pengajuan_status_log [label=<
        <TABLE BORDER="2" CELLBORDER="1" CELLSPACING="0">
            <TR><TD COLSPAN="2" BGCOLOR="#D7E8FF"><B>PENGAJUAN_STATUS_LOG</B></TD></TR>
            <TR><TD ALIGN="LEFT"><U>PK</U></TD><TD ALIGN="LEFT">id : INT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">pengajuan_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">status_lama : ENUM</TD></TR>
            <TR><TD ALIGN="LEFT">status_baru : ENUM</TD></TR>
            <TR><TD ALIGN="LEFT">keterangan : TEXT</TD></TR>
            <TR><TD ALIGN="LEFT"><FONT COLOR="red">FK</FONT></TD><TD ALIGN="LEFT">user_id : INT</TD></TR>
            <TR><TD ALIGN="LEFT">created_at : TIMESTAMP</TD></TR>
        </TABLE>
    >];
    
    // Relationships
    polsek -> users [label="1:N"];
    polsek -> pengajuan [label="1:N"];
    users -> pengajuan [label="1:N"];
    users -> pengajuan_status_log [label="1:N"];
    kegiatan -> pengajuan [label="1:N"];
    kegiatan -> pengajuan_detail [label="1:N"];
    pengajuan -> pengajuan_detail [label="1:N (CASCADE)"];
    pengajuan -> pengajuan_status_log [label="1:N (CASCADE)"];
}
"""

# Save DOT file
with open('sipang_polri_erd.dot', 'w') as f:
    f.write(dot_code)

print("✅ Generated sipang_polri_erd.dot")
print()
print("To convert DOT to image:")
print("  dot -Tpng sipang_polri_erd.dot -o sipang_polri_erd.png")
print("  dot -Tsvg sipang_polri_erd.dot -o sipang_polri_erd.svg")
print("  dot -Tpdf sipang_polri_erd.dot -o sipang_polri_erd.pdf")
print()
print("Requires: apt-get install graphviz (Linux)")
print("          brew install graphviz (macOS)")
print("          https://graphviz.org/download/ (Windows)")
