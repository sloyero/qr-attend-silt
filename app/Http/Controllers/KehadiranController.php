<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Exports\KehadiranExport;
use Maatwebsite\Excel\Facades\Excel;

class KehadiranController extends Controller
{
    // Rekap kehadiran dosen/admin
    public function index()
    {
        $kehadirans = Kehadiran::with([
            'user',
            'mataKuliah'
        ])->latest()->get();

        return Inertia::render('Rekap/Index', [
            'kehadirans' => $kehadirans
        ]);
    }

    // Riwayat absensi mahasiswa
    public function riwayat()
    {
        $riwayat = Kehadiran::where('user_id', Auth::id())
            ->with('mataKuliah')
            ->latest()
            ->get();

        return Inertia::render('Rekap/Riwayat', [
            'riwayat' => $riwayat
        ]);
    }

    public function export()
    {
        return Excel::download(
            new KehadiranExport(),
            'rekap-kehadiran.xlsx'
        );
    }
}