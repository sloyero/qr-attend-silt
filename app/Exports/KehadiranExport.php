<?php

namespace App\Exports;

use App\Models\Kehadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KehadiranExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Kehadiran::with([
            'user',
            'mataKuliah'
        ])
        ->latest()
        ->get()
        ->map(function ($item) {
            return [
                'nama_mahasiswa' => $item->user->name ?? '-',
                'nim' => $item->user->nim ?? '-',
                'mata_kuliah' => $item->mataKuliah->nama_matkul ?? '-',
                'waktu_scan' => $item->waktu_scan,
                'status' => ucfirst($item->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NIM',
            'Mata Kuliah',
            'Waktu Scan',
            'Status',
        ];
    }
}