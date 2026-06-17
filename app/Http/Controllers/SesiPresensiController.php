<?php

namespace App\Http\Controllers;

use App\Models\SesiPresensi;
use App\Models\MataKuliah;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SesiPresensiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN SESI PRESENSI (DOSEN)
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'dosen') {
            abort(403, 'Unauthorized access.');
        }

        $mataKuliahs = MataKuliah::where('dosen_id', $user->id)->get();

        // Auto-close expired sessions
        SesiPresensi::where('dosen_id', $user->id)
            ->where('is_active', true)
            ->where('expired_at', '<=', now())
            ->update(['is_active' => false]);

        $activeSesi = SesiPresensi::where('dosen_id', $user->id)
            ->where('is_active', true)
            ->with(['mataKuliah', 'kehadirans.user'])
            ->first();

        return Inertia::render('SesiPresensi', [
            'user' => $user,
            'mataKuliahs' => $mataKuliahs,
            'activeSesi' => $activeSesi,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BUKA SESI ABSENSI
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'durasi_menit' => 'required|integer|min:1|max:15',
        ]);

        $user = Auth::user();

        // Tutup sesi aktif sebelumnya
        SesiPresensi::where('dosen_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $now = now();

        SesiPresensi::create([
            'dosen_id' => $user->id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'token' => Str::uuid()->toString(),
            'durasi_menit' => $request->durasi_menit,
            'started_at' => $now,
            'expired_at' => $now->copy()->addMinutes($request->durasi_menit),
            'is_active' => true,
        ]);

        return redirect('/presensi');
    }

    /*
    |--------------------------------------------------------------------------
    | TUTUP SESI
    |--------------------------------------------------------------------------
    */

    public function close($id)
    {
        $sesi = SesiPresensi::findOrFail($id);
        $sesi->update(['is_active' => false]);

        return redirect('/presensi');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS SESI (JSON - untuk polling)
    |--------------------------------------------------------------------------
    */

    public function status($id)
    {
        $sesi = SesiPresensi::with('kehadirans.user')->findOrFail($id);

        // Auto-close if expired
        if ($sesi->is_active && $sesi->isExpired()) {
            $sesi->update(['is_active' => false]);
            $sesi->refresh();
        }

        return response()->json([
            'is_active' => $sesi->isActive(),
            'expired_at' => $sesi->expired_at,
            'kehadirans' => $sesi->kehadirans->map(function ($k) {
                return [
                    'id' => $k->id,
                    'user_name' => $k->user->name,
                    'user_nim' => $k->user->nim,
                    'waktu_scan' => $k->waktu_scan,
                    'status' => $k->status,
                ];
            }),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SCAN ABSENSI (MAHASISWA)
    |--------------------------------------------------------------------------
    */

    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $sesi = SesiPresensi::where('token', $request->token)
            ->where('is_active', true)
            ->first();

        if (!$sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan atau sudah ditutup.',
            ], 404);
        }

        // Cek apakah sesi sudah expired
        if ($sesi->isExpired()) {
            $sesi->update(['is_active' => false]);
            return response()->json([
                'success' => false,
                'message' => 'Sesi sudah berakhir.',
            ], 410);
        }

        $userId = Auth::id();

        // Cek apakah sudah pernah scan di sesi ini
        $exists = Kehadiran::where('sesi_presensi_id', $sesi->id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah tercatat hadir di sesi ini.',
            ], 409);
        }

        // Tentukan status: telat jika melewati 1/3 durasi sesi
        $threshold = max(1, floor($sesi->durasi_menit / 3));
        $minutesSinceStart = now()->diffInMinutes($sesi->started_at);
        $status = $minutesSinceStart >= $threshold ? 'telat' : 'hadir';

        Kehadiran::create([
            'user_id' => $userId,
            'mata_kuliah_id' => $sesi->mata_kuliah_id,
            'sesi_presensi_id' => $sesi->id,
            'waktu_scan' => now(),
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran berhasil dicatat!',
            'status' => $status,
        ]);
    }
}
