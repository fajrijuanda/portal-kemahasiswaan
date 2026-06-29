<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'nama_pengaju', 'nim', 'jenis_reimbursement', 'nama_kegiatan', 'tanggal', 'nominal', 'bukti_path', 'status', 'catatan', 'created_by'])]
class Event extends Model
{
    protected function casts(): array
    {
        return ['tanggal' => 'date', 'nominal' => 'decimal:2'];
    }

    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
