<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TAMPIL DOSEN
    |--------------------------------------------------------------------------
    */

    public function dosen()
    {
        $dosen = User::where('role', 'dosen')->get();

        return inertia('Admin/Dosen', [

            'dosen' => $dosen,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH DOSEN
    |--------------------------------------------------------------------------
    */

    public function storeDosen(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nidn' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $dosen = User::create([
            'name' => $request->name,
            'nidn' => $request->nidn,
            'matkul' => $request->matkul,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'dosen',
        ]);

        if ($request->matkul) {
            $subjects = explode(',', $request->matkul);
            foreach ($subjects as $subject) {
                $subjectName = trim($subject);
                if (!empty($subjectName)) {
                    \App\Models\MataKuliah::create([
                        'nama_matkul' => $subjectName,
                        'dosen_id' => $dosen->id,
                    ]);
                }
            }
        }

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH MAHASISWA
    |--------------------------------------------------------------------------
    */

    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([

            'name' => $request->name,

            'nim' => $request->nim,

            'email' => $request->email,

            'password' => bcrypt($request->password),

            'role' => 'mahasiswa',

        ]);

        return back();
    }

    public function destroyDosen($id)
    {
        $dosen = User::findOrFail($id);
        
        // Delete related mata_kuliahs first
        \App\Models\MataKuliah::where('dosen_id', $dosen->id)->delete();
        
        $dosen->delete();

        return response()->json([
            'message' => 'Dosen berhasil dihapus'
        ]);
    }

    public function destroyMahasiswa($id)
    {
        $mahasiswa = User::findOrFail($id);
        
        $mahasiswa->delete();

        return response()->json([
            'message' => 'Mahasiswa berhasil dihapus'
        ]);
    }

    public function updateMatkul(Request $request, $id)
    {
        $request->validate([
            'matkul' => 'nullable|string',
        ]);

        $dosen = User::findOrFail($id);

        $dosen->update([
            'matkul' => $request->matkul,
        ]);

        // Delete existing mata_kuliahs for this dosen
        \App\Models\MataKuliah::where('dosen_id', $dosen->id)->delete();

        // Create new ones
        if ($request->matkul) {
            $subjects = explode(',', $request->matkul);
            foreach ($subjects as $subject) {
                $subjectName = trim($subject);
                if (!empty($subjectName)) {
                    \App\Models\MataKuliah::create([
                        'nama_matkul' => $subjectName,
                        'dosen_id' => $dosen->id,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Matakuliah berhasil diupdate'
        ]);
    }
}