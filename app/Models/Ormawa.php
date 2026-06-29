<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'nama', 'jenis', 'pembina', 'kontak', 'deskripsi', 'status'])]
class Ormawa extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function activities() { return $this->hasMany(UnitActivity::class); }
    public function proposals() { return $this->hasMany(OrmawaProposal::class); }
    public function reimbursements() { return $this->hasMany(Event::class); }
}
