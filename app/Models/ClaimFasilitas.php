<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'nama_pengaju', 'fasilitas', 'keperluan', 'tanggal', 'jumlah', 'status', 'catatan', 'created_by'])]
class ClaimFasilitas extends Model
{
    protected $table = 'claim_fasilitas';

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
