<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ormawa_id', 'semester_id', 'judul', 'tanggal', 'lokasi', 'deskripsi', 'proposal_path', 'status', 'catatan', 'created_by'])]
class OrmawaProposal extends Model
{
    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function ormawa() { return $this->belongsTo(Ormawa::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
