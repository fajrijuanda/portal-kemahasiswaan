<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_activities', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->index();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->string('penanggung_jawab')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status')->default('Draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_activities');
    }
};
