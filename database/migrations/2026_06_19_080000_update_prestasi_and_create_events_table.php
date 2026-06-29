<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            if (! Schema::hasColumn('prestasis', 'foto_path')) {
                $table->string('foto_path')->nullable()->after('catatan');
            }

            if (! Schema::hasColumn('prestasis', 'publikasi_url')) {
                $table->string('publikasi_url')->nullable()->after('foto_path');
            }
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pengaju');
            $table->string('nim')->nullable();
            $table->string('jenis_reimbursement');
            $table->string('nama_kegiatan');
            $table->date('tanggal')->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->string('bukti_path')->nullable();
            $table->string('status')->default('Diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');

        Schema::table('prestasis', function (Blueprint $table) {
            if (Schema::hasColumn('prestasis', 'publikasi_url')) {
                $table->dropColumn('publikasi_url');
            }

            if (Schema::hasColumn('prestasis', 'foto_path')) {
                $table->dropColumn('foto_path');
            }
        });
    }
};
