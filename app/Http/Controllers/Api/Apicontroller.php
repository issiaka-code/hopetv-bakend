<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InfoBulle;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Apicontroller extends Controller
{

    public function getInfoBulles()
    {
        $infoBulles = InfoBulle::where('is_deleted', false)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $infoBulles->count(),
            'data' => $infoBulles,
            'status' => 200
        ], 200);
    }

    public function getPlaylistDuJour()
    {
        $today = Carbon::today();

        // On cherche la playlist du jour
        $playlist = Playlist::whereDate('date_debut', $today)
            ->where('is_deleted', false)
            ->where('etat', true)
            ->with([
                'items.video.media' // On charge les vidéos et leurs médias
            ])
            ->first();

        // Si aucune playlist aujourd’hui → prendre la dernière playlist active
        if (!$playlist) {
            $playlist = Playlist::where('is_deleted', false)
                ->where('etat', true)
                ->whereDate('date_debut', '<', $today)
                ->with([
                    'items.video.media'
                ])
                ->latest('date_debut')
                ->first();
        }

        if (!$playlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aucune playlist disponible',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'playlist' => $playlist,
            'status' => 200
        ], 200);
    }



    public function getVideos()
    {
        $videos = Video::where('is_deleted', false)
            ->with('media') // relation vers medias
            ->latest()
            ->paginate(10); // pagination de 10 vidéos

        if ($videos->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aucune vidéo disponible',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'videos' => $videos,
            'status' => 200

        ], 200);
    }

    public function getVideossearch(Request $request)
{
    $search = $request->query('search'); // mot-clé envoyé par ?search=

    $videos = Video::where('is_deleted', false)
        ->with('media') // relation vers medias
        ->when($search, function ($query, $search) {
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10);

    if ($videos->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Aucune vidéo trouvée'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'videos' => $videos
    ], 200);
}
}
