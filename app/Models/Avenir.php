<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avenir extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'etat',
        'insert_by',
        'update_by',
        'is_deleted',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(AvenirItem::class, 'id_avenir')->orderBy('created_at', 'asc');
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }
}



