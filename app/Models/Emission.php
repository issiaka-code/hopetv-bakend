<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_media',
        'nom',
        'description',
        'insert_by',
        'update_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Relation avec les items d'émission
     */
    public function items()
    {
        return $this->hasMany(EmissionItem::class, 'id_Emission')->where('is_deleted', false);
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'id_media');
    }

    /**
     * Relation avec les items actifs
     */
    public function activeItems()
    {
        return $this->hasMany(EmissionItem::class, 'id_Emission')
            ->where('is_deleted', false)
            ->where('is_active', true)
            ->orderBy('created_at', 'asc');
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

    /**
     * Scope pour exclure les émissions supprimées
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function getVideosCountAttribute()
    {
        return $this->items()->count();
    }

    /**
     * Compter le nombre de vidéos actives
     */
    public function getActiveVideosCountAttribute()
    {
        return $this->items()->where('is_active', true)->count();
    }
}
