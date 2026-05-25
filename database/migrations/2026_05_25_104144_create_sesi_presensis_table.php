<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dosen_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('mata_kuliah_id')
                  ->constrained('mata_kuliahs')
                  ->onDelete('cascade');

            $table->string('token')->unique();

            $table->integer('durasi_menit')->default(15);

            $table->timestamp('started_at');

            $table->timestamp('expired_at');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensis');
    }
};
