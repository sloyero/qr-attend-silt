<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Exports\KehadiranExport;
use App\Models\MataKuliah;
use App\Models\SesiPresensi;
use App\Models\Kehadiran;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class KehadiranController extends Controller
{
    // Rekap kehadiran dosen/admin
    public function index()
    {
        $matkuls = MataKuliah::where('dosen_id', Auth::id())
            ->withCount('kehadirans')
            ->get();

        return Inertia::render('Rekap/Index', [
            'matkuls' => $matkuls
        ]);
    }

    // Riwayat absensi mahasiswa
    public function riwayat()
    {
        $matkuls = MataKuliah::whereHas(
            'kehadirans',
            function ($q) {
                $q->where('user_id', Auth::id());
            }
        )
        ->withCount([
            'kehadirans as total_pertemuan' => function ($q) {
                $q->where('user_id', Auth::id());
            }
        ])
        ->get();

        return Inertia::render('Rekap/Riwayat', [
            'matkuls' => $matkuls
        ]);
    }

    public function riwayatMatkul($id)
    {
        $riwayat = Kehadiran::where(
                'user_id',
                Auth::id()
            )
            ->where(
                'mata_kuliah_id',
                $id
            )
            ->with([
                'sesiPresensi',
                'mataKuliah'
            ])
            ->orderBy('waktu_scan')
            ->get();

        $matkul = MataKuliah::findOrFail($id);

        return Inertia::render(
            'Rekap/RiwayatMatkul',
            [
                'riwayat' => $riwayat,
                'matkul' => $matkul
            ]
        );
    }

    public function detail($id)
    {
        $sesis = SesiPresensi::where('mata_kuliah_id', $id)
            ->orderBy('started_at')
            ->get();

        $matkul = MataKuliah::findOrFail($id);

        return Inertia::render('Rekap/Detail', [
            'sesis' => $sesis,
            'matkul' => $matkul,
        ]);
    }

    public function detailSesi($id)
    {
        $sesi = SesiPresensi::with('mataKuliah')->findOrFail($id);

        // Ambil semua mahasiswa beserta data kehadirannya untuk sesi ini
        $mahasiswas = User::where('role', 'mahasiswa')
            ->with(['kehadirans' => function ($query) use ($id) {
                $query->where('sesi_presensi_id', $id);
            }])
            ->get();

        $data = $mahasiswas->map(function ($mhs) {

            $kehadiran = $mhs->kehadirans->first();

            return [
                'id' => $mhs->id,
                'nim' => $mhs->nim,
                'name' => $mhs->name,
                'status' => $kehadiran ? $kehadiran->status : 'alpha',
                'waktu_scan' => $kehadiran?->waktu_scan,
            ];
        });

        return Inertia::render('Rekap/DetailSesi', [
            'sesi' => $sesi,
            'mahasiswas' => $data,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'sesi_presensi_id' => 'required',
            'status' => 'required|in:hadir,telat,izin,alpha',
        ]);

        $kehadiran = Kehadiran::where(
            'user_id',
            $request->user_id
        )->where(
            'sesi_presensi_id',
            $request->sesi_presensi_id
        )->first();

        if ($kehadiran) {

            $kehadiran->update([
                'status' => $request->status
            ]);

        } else {

            Kehadiran::create([
                'user_id' => $request->user_id,
                'sesi_presensi_id' => $request->sesi_presensi_id,
                'mata_kuliah_id' => $request->mata_kuliah_id,
                'status' => $request->status,
                'waktu_scan' => now(),
            ]);

        }

        return back();
    }

    public function exportRekap($id)
    {
        $matkul = MataKuliah::findOrFail($id);

        return Excel::download(
            new KehadiranExport($id),
            'rekap-' . $matkul->nama_matkul . '.xlsx'
        );
    }
}