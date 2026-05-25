<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MataKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );



        /*
        |--------------------------------------------------------------------------
        | DOSEN
        |--------------------------------------------------------------------------
        */

        $dosen = User::updateOrCreate(
            ['email' => 'dosen@gmail.com'],
            [
                'name' => 'Pak Fatan',
                'nidn' => '12345678',
                'matkul' => 'Pemrograman Web',
                'password' => bcrypt('password'),
                'role' => 'dosen',
            ]
        );



        /*
        |--------------------------------------------------------------------------
        | MAHASISWA
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'mahasiswa@gmail.com'],
            [
                'name' => 'Wildan',
                'nim' => '231110027',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa2@gmail.com'],
            [
                'name' => 'Nasrul Fawzi',
                'nim' => '231110028',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa3@gmail.com'],
            [
                'name' => 'Fikri Karunia',
                'nim' => '231110029',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa4@gmail.com'],
            [
                'name' => 'Sayyid Ashhabussnan',
                'nim' => '231110030',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
            ]
        );



        /*
        |--------------------------------------------------------------------------
        | MATA KULIAH
        |--------------------------------------------------------------------------
        */

        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Pemrograman Web', 'dosen_id' => $dosen->id],
        );

        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Basis Data', 'dosen_id' => $dosen->id],
        );

        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Struktur Data', 'dosen_id' => $dosen->id],
        );
    }
}