<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {

            $table->foreignId('sesi_presensi_id')
                  ->nullable()
                  ->constrained('sesi_presensis')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {

            $table->dropForeign(['sesi_presensi_id']);
            $table->dropColumn('sesi_presensi_id');

        });
    }
};
