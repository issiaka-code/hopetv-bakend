<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessVideoJob;
use App\Models\Media;
use App\Models\Podcast;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class PodcastController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    public function index(Request $request)
    {
        $query = Podcast::with('media')->where('is_deleted', false)->latest();

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

        $podcasts = $query->paginate(12);

        $podcastsData = collect($podcasts->items())->map(function ($podcast) {
            $isAudio = $podcast->media && $podcast->media->type === 'audio';
            $isVideoLink = $podcast->media && $podcast->media->type === 'link';
            $isVideoFile = $podcast->media && $podcast->media->type === 'video';
            $isPdf = $podcast->media && $podcast->media->type === 'pdf';
            $isImages = $podcast->media && $podcast->media->type === 'images';

            return (object)[
                'id' => $podcast->id,
                'nom' => $podcast->nom,
                'description' => $podcast->description,
                'created_at' => $podcast->created_at,
                'media_type' => $isAudio ? 'audio' : ($isVideoLink ? 'video_link' : ($isVideoFile ? 'video_file' : ($isPdf ? 'pdf' : ($isImages ? 'images' : null)))),
                'thumbnail_url' => asset('storage/' . $podcast->media->thumbnail),
                'media_url' => $podcast->media && !$isImages ? asset('storage/' . $podcast->media->url_fichier) : null,
                'has_thumbnail' => $podcast->media && $podcast->media->thumbnail ? true : ($isImages && !empty(json_decode($podcast->media->url_fichier ?? '[]', true))),
                'is_published' => $podcast->media->is_published ?? true,
                'images' => $isImages ? array_map(function ($p) {
                    return asset('storage/' . $p);
                }, (array)(json_decode($podcast->media->url_fichier ?? '[]', true) ?: [])) : [],
            ];
        });

        return view('admin.medias.podcasts.index', [
            'podcasts' => $podcasts,
            'podcastsData' => $podcastsData,
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

        $podcast = Podcast::create([
            'id_media' => $media->id,
            'nom' => $request->nom,
            'description' => $request->description,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Podcast ajouté avec succès.');
        return redirect()->route('podcasts.index');
    }

    public function edit(Podcast $podcast)
    {
        $podcast->load('media');
        return response()->json([
            'nom' => $podcast->nom,
            'description' => $podcast->description,
            'media' => $podcast->media
        ]);
    }

    public function update(Request $request, Podcast $podcast)
    {
        $media = $podcast->media;

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

        $podcast->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Podcast mis à jour avec succès.');
        return redirect()->route('podcasts.index');
    }


    public function destroy($id)
    {
        $podcast = Podcast::findOrFail($id);
        try {
            DB::beginTransaction();

            $podcast->update([
                'is_deleted' => true,
                'update_by' => auth()->id(),
            ]);

            // Marquer également le média comme supprimé
            if ($podcast->media) {
                $podcast->media->update([
                    'is_deleted' => true,
                    'update_by' => auth()->id(),
                ]);
            }

            DB::commit();
            notify()->success('Succès', 'Podcast supprimé avec succès.');
            return redirect()->route('podcasts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function publish(Request $request, $id)
    {
        $podcast = Podcast::findOrFail($id);

        if (!$podcast->media) {
            Alert::error('Erreur', 'Aucun média associé.');
            return redirect()->back();
        }

        try {
            $podcast->media->update([
                'is_published' => true,
                'update_by' => auth()->id(),
            ]);

            notify()->success('Succès', 'Podcast publié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la publication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de publier le podcast.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }

    public function unpublish(Request $request, $id)
    {
        $podcast = Podcast::findOrFail($id);

        if (!$podcast->media) {
            Alert::error('Erreur', 'Aucun média associé.');
            return redirect()->back();
        }

        try {
            $podcast->media->update([
                'is_published' => false,
                'update_by' => auth()->id(),
            ]);

            notify()->success('Succès', 'Podcast dépublié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la dépublication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de dépublier le podcast.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }

    public function voirpodcast($id)
    {
        try {
            $podcast = Podcast::with('media')->findOrFail($id);
            $media = $podcast->media;

            // Vérifier si le média est en traitement
            if ($media->status !== 'ready') {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'La vidéo ou le média est en cours de traitement. Veuillez réessayer plus tard.'
                ], 200);
            }

            // Préparer l'URL selon le type
            $url = $media->url_fichier;

            if ($media->type === 'link' && !empty($url)) {
                // Conversion automatique pour les liens YouTube
                if (str_contains($url, 'youtube.com/watch?v=')) {
                    $url = str_replace('watch?v=', 'embed/', $url);
                } elseif (str_contains($url, 'youtu.be/')) {
                    $url = str_replace('youtu.be/', 'www.youtube.com/embed/', $url);
                }

                // Conversion Vimeo
                if (str_contains($url, 'vimeo.com/')) {
                    $videoId = basename(parse_url($url, PHP_URL_PATH));
                    $url = "https://player.vimeo.com/video/" . $videoId;
                }
            }

            if ($media->type === 'images') {
                $images = json_decode($media->url_fichier, true) ?? [];
                $imageUrls = array_map(fn($path) => asset('storage/' . $path), $images);
            }

            // Si le média est prêt
            return response()->json([
                'status' => 'ready',
                'data' => [
                    'id' => $podcast->id,
                    'nom' => $podcast->nom,
                    'description' => $podcast->description,
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
                'message' => 'Erreur lors du chargement du podcast : ' . $e->getMessage(),
            ], 500);
        }
    }
}
