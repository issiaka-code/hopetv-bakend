<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    use HasFactory;

    protected $fillable = ['id_playlist', 'id_video', 'heure_debut', 'heure_fin', 'insert_by', 'update_by', 'is_deleted'];

    // Relations
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'id_playlist');
    }

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