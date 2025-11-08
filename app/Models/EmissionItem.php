<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmissionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_Emission',
        'nom',
        'description',
        'id_media', // 'video' (upload) ou 'link'
        'is_active',
        'insert_by',
        'update_by',
        'is_deleted'
    ];


    /**
     * Relation avec l'émission
     */
    public function emission()
    {
        return $this->belongsTo(Emission::class, 'id_Emission');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'id_media');
    }


    /**
     * Relation avec le modèle User (créateur)
     */
    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insert_by');
    }

    /**
     * Relation avec le modèle User (modificateur)
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}
