<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'title', 'company', 'location', 'deadline', 'external_url', 'content', 'status', 'published_at', 'created_by'])]
class CareerPost extends Model
{
    protected function casts(): array
    {
        return ['deadline' => 'date', 'published_at' => 'datetime'];
    }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
