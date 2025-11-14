<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Video;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Jobs\ProcessVideoJob;

class VideoController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    public function index(Request $request)
    {
        $query = Video::with('media')->where('is_deleted', false)->latest();

        // Recherche
        if ($request->filled('search')) {
            $query->where('nom', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }

        // Filtrage par type
        if ($request->filled('type')) {
            $query->whereHas('media', function ($q) use ($request) {
                if ($request->type === 'audio') {
                    $q->where('type', 'audio');
                } elseif ($request->type === 'video_file') {
                    $q->where('type', 'video');
                } elseif ($request->type === 'video_link') {
                    $q->where('type', 'link');
                } elseif ($request->type === 'pdf') {
                    $q->where('type', 'pdf');
                } elseif ($request->type === 'images') {
                    $q->where('type', 'images');
                }
            });
        }

        $videos = $query->paginate(12);

        // Préparer chaque variable pour la vue et JS
        $videosData = collect($videos->items())->map(function ($video) {
            $isAudio = $video->media && $video->media->type === 'audio';
            $isVideoLink = $video->media && $video->media->type === 'link';
            $isVideoFile = $video->media && $video->media->type === 'video';
            $isPdf = $video->media && $video->media->type === 'pdf';
            $isImages = $video->media && $video->media->type === 'images';

            $thumbnailUrl = null;
            $hasThumbnail = false;

            if ($isVideoLink) {
                // Pour les liens vidéo, utiliser l'image de couverture si disponible, sinon générer l'URL embed
                if ($video->media && $video->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $video->media->thumbnail);
                    $hasThumbnail = true;
                } else {
                    // Générer l'URL embed YouTube/Vimeo pour l'iframe
                    $rawUrl = $video->media->url_fichier;
                    if (Str::contains($rawUrl, 'youtube.com/watch?v=')) {
                        $videoId = explode('v=', parse_url($rawUrl, PHP_URL_QUERY))[1] ?? null;
                        $videoId = explode('&', $videoId)[0];
                        $thumbnailUrl = $videoId ? "https://www.youtube.com/embed/$videoId" : $rawUrl;
                    } elseif (Str::contains($rawUrl, 'youtu.be/')) {
                        $videoId = basename(parse_url($rawUrl, PHP_URL_PATH));
                        $thumbnailUrl = "https://www.youtube.com/embed/$videoId";
                    } else {
                        $thumbnailUrl = $rawUrl;
                    }
                }
            } elseif ($isVideoFile) {
                // Pour les vidéos fichiers, utiliser l'image de couverture si disponible
                if ($video->media && $video->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $video->media->thumbnail);
                    $hasThumbnail = true;
                }
            }

            return (object)[
                'id' => $video->id,
                'nom' => $video->nom,
                'description' => $video->description,
                'created_at' => $video->created_at,
                'media_type' => $isVideoLink ? 'video_link' : 'video_file',
                'thumbnail_url' => $thumbnailUrl,
                'media_url' => $video->media && $isVideoFile ? asset('storage/' . $video->media->url_fichier) : null,
                'has_thumbnail' => $hasThumbnail,
                'is_published' => $video->media->is_published ?? true,
            ];
        });

        return view('admin.medias.videos.index', [
            'videos' => $videos,
            'videosData' => $videosData,
        ]);
    }

    public function store(Request $request)
    {
        $result = $this->mediaService->createMedia($request);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $errors = $result['errors'];

            if ($errors instanceof \Illuminate\Support\MessageBag) {
                // Erreurs de validation Laravel
                foreach ($errors->all() as $error) {
                    notify()->error($error);
                }
            } elseif (is_array($errors)) {
                // Si jamais tu retournes un tableau d'erreurs
                foreach ($errors as $error) {
                    notify()->error($error);
                }
            } elseif (is_string($errors)) {
                // Erreur simple sous forme de message texte
                notify()->error($errors);
            }

            return back()->withInput();
        }
        $media = $result;

        // === CRÉATION DU VIDEO ===
        $video = Video::create([
            'id_media' => $media->id,
            'nom' => $request->nom,
            'description' => $request->description,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Vidéo ajoutée avec succès.');
        return redirect()->route('videos.index');
    }



    public function edit(Video $video)
    {
        $video->load('media');
        return response()->json([
            'nom' => $video->nom,
            'description' => $video->description,
            'media' => $video->media
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = Video::findOrFail($id);
        $media = $data->media;

        $result = $this->mediaService->updateMedia($request, $media);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $errors = $result['errors'];

            if ($errors instanceof \Illuminate\Support\MessageBag) {
                // Erreurs de validation Laravel
                foreach ($errors->all() as $error) {
                    notify()->error($error);
                }
            } elseif (is_array($errors)) {
                // Si jamais tu retournes un tableau d'erreurs
                foreach ($errors as $error) {
                    notify()->error($error);
                }
            } elseif (is_string($errors)) {
                // Erreur simple sous forme de message texte
                notify()->error($errors);
            }

            return back()->withInput();
        }

        $data->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Vidéo mise à jour avec succès.');
        return redirect()->route('videos.index');
    }


    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        try {
            DB::beginTransaction();

            $video->update([
                'is_deleted' => true,
                'update_by' => auth()->id(),
            ]);

            // Marquer également le média comme supprimé
            if ($video->media) {
                $video->media->update([
                    'is_deleted' => true,
                    'update_by' => auth()->id(),
                ]);
            }

            DB::commit();
            notify()->success('Succès', 'Vidéo supprimée avec succès.');
            return redirect()->route('videos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function publish(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        if (!$video->media) {
            Alert::error('Erreur', 'Aucun média associé.');
            return redirect()->back();
        }

        try {
            $video->media->update([
                'is_published' => true,
                'update_by' => auth()->id(),
            ]);

            notify()->success('Succès', 'Média publié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la publication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de publier la vidéo.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }

    public function unpublish(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        if (!$video->media) {
            Alert::error('Erreur', 'Aucun média associé.');
            return redirect()->back();
        }

        try {
            $video->media->update([
                'is_published' => false,
                'update_by' => auth()->id(),
            ]);

            notify()->success('Succès', 'Média dépublié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la dépublication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de dépublier la vidéo.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }

    public function voirVideo($id)
    {
        try {
            $video = Video::with('media')->findOrFail($id);
            $media = $video->media;

            if ($media->status !== 'ready') {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'Le média est en cours de traitement. Veuillez réessayer plus tard.'
                ], 200);
            }

            $url = $media->url_fichier;

            if ($media->type === 'link' && !empty($url)) {
                if (str_contains($url, 'youtube.com/watch?v=')) {
                    $url = str_replace('watch?v=', 'embed/', $url);
                } elseif (str_contains($url, 'youtu.be/')) {
                    $url = str_replace('youtu.be/', 'www.youtube.com/embed/', $url);
                } elseif (str_contains($url, 'vimeo.com/')) {
                    $videoId = basename(parse_url($url, PHP_URL_PATH));
                    $url = "https://player.vimeo.com/video/" . $videoId;
                }
            }

            if ($media->type === 'images') {
                $images = json_decode($media->url_fichier, true) ?? [];
                $imageUrls = array_map(fn($path) => asset('storage/' . $path), $images);
            }

            return response()->json([
                'status' => 'ready',
                'data' => [
                    'id' => $video->id,
                    'nom' => $video->nom,
                    'description' => $video->description,
                    'media' => [
                        'url' => $media->type === 'images' ? ($imageUrls ?? []) : (
                            in_array($media->type, ['audio', 'video', 'pdf']) ? asset('storage/' . $url) : $url
                        ),
                        'thumbnail' => $media->thumbnail ? asset('storage/' . $media->thumbnail) : null,
                        'type' => $media->type,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement de la vidéo : ' . $e->getMessage(),
            ], 500);
        }
    }
}
