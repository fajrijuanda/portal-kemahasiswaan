<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit',
        'ormawa_id',
        'semester_id',
        'prodi_id',
        'judul',
        'penanggung_jawab',
        'tanggal',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
