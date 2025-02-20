<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_done', 'checklist_id'];

    // Relasi ke Checklist (Many-to-One)
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
