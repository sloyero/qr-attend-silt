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
        User::create([

            'name' => $request->name,

            'nidn' => $request->nidn,

            'matkul' => $request->matkul,

            'email' => $request->email,

            'password' => bcrypt($request->password),

            'role' => 'dosen',

        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH MAHASISWA
    |--------------------------------------------------------------------------
    */

    public function storeMahasiswa(Request $request)
    {
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
        User::findOrFail($id)->delete();

        return back();
    }

    public function updateMatkul(Request $request, $id)
    {
        $dosen = User::findOrFail($id);

        $dosen->update([

            'matkul' => $request->matkul,

        ]);

        return response()->json([

            'message' => 'Matakuliah berhasil diupdate'

        ]);
    }
}