<?php

namespace App\Http\Controllers;

use App\Models\Avenir;
use App\Models\AvenirItem;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class AvenirController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $avenirs = Avenir::notDeleted()
            ->with(['items' => function ($q) { $q->notDeleted()->with('video')->orderBy('created_at', 'asc'); }])
            ->when($search, function ($query, $search) {
                return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.a-venir.index', compact('avenirs', 'search'));
    }

    public function create()
    {
        $videos = Video::where('is_deleted', false)->whereHas('media', function ($q) {
            $q->where('type', 'video')->where('is_published', false);
        })->orderBy('nom', 'asc')->get();

        foreach ($videos as $video) {
            if ($video->media && $video->media->type === 'video') {
                try {
                    $seconds = FFMpeg::fromDisk('public')->open($video->media->url_fichier)->getDurationInSeconds();
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $secs = $seconds % 60;
                    $video->duree = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
                } catch (\Exception $e) {
                    $video->duree = null;
                }
            } else {
                $video->duree = null;
            }
        }

        return view('admin.a-venir.create', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'etat' => 'required|boolean',
            'selected_videos' => 'required|string',
        ]);

        try {
            $selectedVideos = json_decode($request->selected_videos, true);
            if (empty($selectedVideos)) {
                notify()->error('Erreur', 'Veuillez sélectionner au moins une vidéo.');
                return redirect()->back();
            }
            DB::beginTransaction();
            $avenir = Avenir::create([
                'nom' => $request->nom,
                'description' => $request->description,
                'date_debut' => $request->date_debut,
                'etat' => $request->etat,
                'insert_by' => Auth::id(),
                'update_by' => Auth::id(),
            ]);
            foreach ($selectedVideos as $index => $videoData) {
                AvenirItem::create([
                    'id_avenir' => $avenir->id,
                    'id_video' => $videoData['id'],
                    'duree_video' => $videoData['duration'],
                    'position' => $videoData['order'] ?? ($index + 1),
                    'insert_by' => Auth::id(),
                    'update_by' => Auth::id(),
                ]);
            }
            DB::commit();
            notify()->success('Succès', 'Programmation "À venir" créée avec succès.');
            return redirect()->route('a-venir.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Avenir store error', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création.');
        }
    }

    public function show($id)
    {
        $avenir = Avenir::notDeleted()->with(['items' => function ($q) { $q->notDeleted()->with('video', 'video.media')->orderBy('created_at', 'asc'); }])->findOrFail($id);
        return view('admin.a-venir.show', compact('avenir'));
    }

    public function edit($id)
    {
        $avenir = Avenir::notDeleted()->with(['items' => function ($q) { $q->notDeleted()->with('video')->orderBy('created_at', 'asc'); }])->findOrFail($id);
        $videos = Video::where('is_deleted', false)->whereHas('media', function ($q) {
            $q->where('type', 'video')->where('is_published', false);
        })->orderBy('nom', 'asc')->get();
        foreach ($videos as $video) {
            if ($video->media && $video->media->type === 'video') {
                try {
                    $seconds = FFMpeg::fromDisk('public')->open($video->media->url_fichier)->getDurationInSeconds();
                    $hours = floor($seconds / 3600);
                    $minutes = floor(($seconds % 3600) / 60);
                    $secs = $seconds % 60;
                    $video->duree = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
                } catch (\Exception $e) {
                    $video->duree = null;
                }
            } else {
                $video->duree = null;
            }
        }
        return view('admin.a-venir.edit', compact('avenir', 'videos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'etat' => 'required|boolean',
            'selected_videos' => 'required|string',
        ]);
        try {
            DB::beginTransaction();
            $avenir = Avenir::findOrFail($id);
            $avenir->update([
                'nom' => $request->nom,
                'description' => $request->description,
                'date_debut' => $request->date_debut,
                'etat' => $request->etat,
                'update_by' => Auth::id(),
            ]);
            $selectedVideos = json_decode($request->selected_videos, true) ?: [];
            // Soft delete existing
            AvenirItem::where('id_avenir', $avenir->id)->update(['is_deleted' => true, 'update_by' => Auth::id()]);
            foreach ($selectedVideos as $index => $videoData) {
                AvenirItem::create([
                    'id_avenir' => $avenir->id,
                    'id_video' => $videoData['id'],
                    'duree_video' => $videoData['duration'],
                    'position' => $videoData['order'] ?? ($index + 1),
                    'insert_by' => Auth::id(),
                    'update_by' => Auth::id(),
                ]);
            }
            DB::commit();
            notify()->success('Succès', 'Programmation "À venir" mise à jour.');
            return redirect()->route('a-venir.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    public function destroy($id)
    {
        $avenir = Avenir::findOrFail($id);
        $avenir->update(['is_deleted' => true, 'update_by' => Auth::id()]);
        AvenirItem::where('id_avenir', $avenir->id)->update(['is_deleted' => true, 'update_by' => Auth::id()]);
        notify()->success('Succès', 'Programmation supprimée.');
        return redirect()->route('a-venir.index');
    }
}



