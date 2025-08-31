<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlaylistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_playlist', 
        'id_video', 
        'duree_video', 
        'position',
        'insert_by', 
        'update_by', 
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Relations
     */
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

    /**
     * Scopes
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeForPlaylist($query, $playlistId)
    {
        return $query->where('id_playlist', $playlistId);
    }

    public function scopeWithValidVideo($query)
    {
        return $query->whereHas('video', function($q) {
            $q->where('is_deleted', false);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Accesseurs
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duree_video) {
            return 'N/A';
        }

        $timeParts = explode(':', $this->duree_video);
        if (count($timeParts) === 3) {
            $hours = (int)$timeParts[0];
            $minutes = (int)$timeParts[1];
            $seconds = (int)$timeParts[2];
            
            if ($hours > 0) {
                return sprintf('%dh %02dm %02ds', $hours, $minutes, $seconds);
            } else {
                return sprintf('%dm %02ds', $minutes, $seconds);
            }
        }
        
        return $this->duree_video;
    }

    public function getOrderInPlaylistAttribute()
    {
        return PlaylistItem::where('id_playlist', $this->id_playlist)
                          ->notDeleted()
                          ->where('created_at', '<', $this->created_at)
                          ->count() + 1;
    }

    /**
     * Méthodes utilitaires
     */
    public function isValid()
    {
        return !$this->is_deleted && 
               $this->video && 
               !$this->video->is_deleted;
    }

    public static function reorderPlaylistItems($playlistId, $videoIds)
    {
        DB::transaction(function() use ($playlistId, $videoIds) {
            // Marquer tous les anciens items comme supprimés
            self::where('id_playlist', $playlistId)
                ->update(['is_deleted' => true, 'update_by' => Auth::id()]);

            // Créer les nouveaux items dans l'ordre spécifié
            foreach ($videoIds as $index => $videoId) {
                $video = Video::find($videoId);
                if ($video && !$video->is_deleted) {
                    self::create([
                        'id_playlist' => $playlistId,
                        'id_video' => $videoId,
                        'duree_video' => $video->duree,
                        'insert_by' => Auth::id(),
                        'update_by' => Auth::id(),
                    ]);
                }
            }
        });
    }
}