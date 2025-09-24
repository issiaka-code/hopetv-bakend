<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvenirItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_avenir',
        'id_video',
        'duree_video',
        'position',
        'insert_by',
        'update_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function avenir()
    {
        return $this->belongsTo(Avenir::class, 'id_avenir');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'id_video');
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }
}



