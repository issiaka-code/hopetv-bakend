<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 
        'description', 
        'date_debut', 
        'etat',
        'insert_by', 
        'update_by', 
        'is_deleted'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    /**
     * Relations
     */
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
        return $this->hasMany(PlaylistItem::class, 'id_playlist')
                    ->orderBy('created_at', 'asc');
    }

    public function activeItems()
    {
        return $this->hasMany(PlaylistItem::class, 'id_playlist')
                    ->where('is_deleted', false)
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Scopes
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date_debut', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date_debut', today());
    }

    public function scopeActive($query)
    {
        return $query->where('date_debut', '<=', now())
                    ->where('date_debut', '>=', now()->subDays(1));
    }

    /**
     * Accesseurs
     */
    public function getStatusAttribute()
    {
        $now = now();
        $dateDebut = Carbon::parse($this->date_debut);
        
        if ($dateDebut->isFuture()) {
            return 'À venir';
        } elseif ($dateDebut->isToday()) {
            return 'Aujourd\'hui';
        } else {
            return 'Passée';
        }
    }

    public function getFormattedDateDebutAttribute()
    {
        return Carbon::parse($this->date_debut)->format('d/m/Y à H:i');
    }

    public function getVideoCountAttribute()
    {
        return $this->activeItems()
                    ->whereHas('video', function($query) {
                        $query->where('is_deleted', false);
                    })
                    ->count();
    }

    public function getTotalDurationAttribute()
    {
        $totalSeconds = 0;
        
        foreach ($this->activeItems as $item) {
            if ($item->video && !$item->video->is_deleted && $item->duree_video) {
                $timeParts = explode(':', $item->duree_video);
                if (count($timeParts) === 3) {
                    $hours = (int)$timeParts[0];
                    $minutes = (int)$timeParts[1];
                    $seconds = (int)$timeParts[2];
                    $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                }
            }
        }
        
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Méthodes utilitaires
     */
    public function canBeEdited()
    {
        // Une playlist peut être éditée si elle n'a pas encore commencé
        return Carbon::parse($this->date_debut)->isFuture();
    }

    public function canBeDeleted()
    {
        // Une playlist peut être supprimée si elle n'est pas en cours de lecture
        return true; // Ou ajouter votre logique spécifique
    }

    public function hasValidVideos()
    {
        return $this->activeItems()
                    ->whereHas('video', function($query) {
                        $query->where('is_deleted', false);
                    })
                    ->exists();
    }
}

