<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'date_debut', 'id_user', 'insert_by', 'update_by', 'is_deleted'];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insert_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function items()
    {
        return $this->hasMany(PlaylistItem::class, 'id_playlist');
    }
}