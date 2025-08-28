<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoBulle extends Model
{
    use HasFactory;

    protected $fillable = ['id_video', 'titre', 'texte', 'is_active', 'heure_apparition', 'insert_by', 'update_by', 'is_deleted'];

    // Relations
    public function video()
    {
        return $this->belongsTo(Video::class, 'id_video');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insert_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}