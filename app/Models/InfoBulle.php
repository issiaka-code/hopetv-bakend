<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoBulle extends Model
{
    use HasFactory;
    protected $table = 'info_bulles';
    
    protected $fillable = ['titre', 'texte', 'is_active', 'insert_by', 'update_by', 'is_deleted'];


    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insert_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}