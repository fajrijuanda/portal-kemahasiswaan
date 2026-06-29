<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'nama_mahasiswa', 'nim', 'kegiatan', 'tujuan', 'tanggal', 'nominal', 'status', 'catatan', 'created_by'])]
class ClaimTransport extends Model
{
    protected function casts(): array
    {
        return ['tanggal' => 'date', 'nominal' => 'decimal:2'];
    }

    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
