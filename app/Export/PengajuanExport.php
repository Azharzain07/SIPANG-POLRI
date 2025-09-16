<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pengajuans;

    public function __construct($pengajuans)
    {
        $this->pengajuans = $pengajuans;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->pengajuans;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Pengajuan',
            'Nama Pengaju',
            'Email Pengaju',
            'Judul',
            'Nama Polsek/Polres',
            'Jumlah Dana',
            'Status',
            'Tanggal Diajukan',
        ];
    }

    /**
     * @param mixed $pengajuan
     * @return array
     */
    public function map($pengajuan): array
    {
        return [
            $pengajuan->id,
            $pengajuan->user->name,
            $pengajuan->user->email,
            $pengajuan->judul,
            $pengajuan->category->nama_kategori,
            $pengajuan->jumlah_dana,
            ucfirst($pengajuan->status),
            $pengajuan->created_at->format('d-m-Y H:i:s'),
        ];
    }
}