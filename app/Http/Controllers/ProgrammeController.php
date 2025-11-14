<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Programme;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ProgrammeController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    public function index(Request $request)
    {
        $query = Programme::with('media')->where('is_deleted', false)->latest();

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

        $programmes = $query->paginate(12);

        // Préparer chaque variable pour la vue et JS
        $programmesData = collect($programmes->items())->map(function ($programme) {
            $isAudio = $programme->media && $programme->media->type === 'audio';
            $isVideoLink = $programme->media && $programme->media->type === 'link';
            $isVideoFile = $programme->media && $programme->media->type === 'video';
            $isPdf = $programme->media && $programme->media->type === 'pdf';
            $isImages = $programme->media && $programme->media->type === 'images';
            

            $thumbnailUrl = null;

            if ($isVideoLink) {
                $rawUrl = $programme->media->url_fichier;

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
            } elseif ($isVideoFile) {
                // Pour les vidéos fichiers, utiliser l'image de couverture si disponible
                if ($programme->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $programme->media->thumbnail);
                } else {
                    $thumbnailUrl = asset('storage/' . $programme->media->url_fichier);
                }
            } elseif ($isAudio || $isPdf) {
                // Pour les audios et PDFs, utiliser l'image de couverture si disponible
                if ($programme->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $programme->media->thumbnail);
                } else {
                    $thumbnailUrl = null; // Pas d'image, on utilisera l'icône par défaut
                }
            } elseif ($isImages) {
                // Pour les images, utiliser la couverture si dispo, sinon la première image de url_fichier (JSON)
                if ($programme->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $programme->media->thumbnail);
                } else {
                    $imagesArr = [];
                    if (!empty($programme->media->url_fichier)) {
                        $decoded = json_decode($programme->media->url_fichier, true);
                        $imagesArr = is_array($decoded) ? $decoded : [];
                    }
                    $first = count($imagesArr) > 0 ? $imagesArr[0] : null;
                    $thumbnailUrl = $first ? asset('storage/' . $first) : null;
                }
            }
            return (object)[
                'id' => $programme->id,
                'nom' => $programme->nom,
                'description' => $programme->description,
                'created_at' => $programme->created_at,
                'media_type' => $isAudio ? 'audio' : ($isVideoLink ? 'video_link' : ($isVideoFile ? 'video_file' : ($isPdf ? 'pdf' : ($isImages ? 'images' : null)))),
                'thumbnail_url' => $thumbnailUrl,
                'video_url' => $isVideoFile ? asset('storage/' . $programme->media->url_fichier) : $thumbnailUrl,
                'media_url' => $programme->media && !$isImages ? asset('storage/' . $programme->media->url_fichier) : null,
                'has_thumbnail' => $programme->media && $programme->media->thumbnail ? true : ($isImages && !empty(json_decode($programme->media->url_fichier ?? '[]', true))),
                'is_published' => $programme->media->is_published ?? true,
                'images' => $isImages ? array_map(function ($p) { return asset('storage/' . $p); }, (array)(json_decode($programme->media->url_fichier ?? '[]', true) ?: [])) : [],
            ];
        });
        // Envoyer chaque programme comme variable séparée
        return view('admin.medias.programmes.index', [
            'programmes' => $programmes,
            'programmesData' => $programmesData,
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

        // === CRÉATION DU PROGRAMME ===
        $programme = Programme::create([
            'id_media' => $media->id,
            'nom' => $request->nom,
            'description' => $request->description,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Programme ajouté avec succès.');
        return redirect()->route('programmes.index');
    }

    public function edit(Programme $programme)
    {
        $programme->load('media');
        return response()->json([
            'nom' => $programme->nom,
            'description' => $programme->description,
            'media' => $programme->media
        ]);
    }

    public function update(Request $request, $id)
    {
        $programme = Programme::findOrFail($id);
        $media = $programme->media;

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

        $programme->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Programme mis à jour avec succès.');
        return redirect()->route('programmes.index');
    }

    public function voirProgramme($id)
    {
        try {
            $programme = Programme::with('media')->findOrFail($id);
            $media = $programme->media;

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
                    'id' => $programme->id,
                    'nom' => $programme->nom,
                    'description' => $programme->description,
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
                'message' => 'Erreur lors du chargement du programme : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $programme = Programme::findOrFail($id);
        try {
            DB::beginTransaction();

            $programme->update([
                'is_deleted' => true,
                'update_by' => auth()->id(),
            ]);

            DB::commit();
            notify()->success('Succès', 'Programme supprimé avec succès.');
            return redirect()->route('programmes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProgrammeController@destroy: erreur', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Alert::error('Erreur', 'Impossible de supprimer le programme: ' . $e->getMessage());
            return back();
        }
    }

    public function publish(Request $request, $id)
    {
        try {
            $programme = Programme::findOrFail($id);
            $programme->media->update([
                'is_published' => true,
                'update_by' => auth()->id(),
            ]);
            
            notify()->success('Succès', 'Programme publié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la publication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de publier le programme.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }

    public function unpublish(Request $request, $id)
    {
        try {
            $programme = Programme::findOrFail($id);
            $programme->media->update([
                'is_published' => false,
                'update_by' => auth()->id(),
            ]);
            
            notify()->success('Succès', 'Programme dépublié avec succès.');
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la dépublication: ' . $e->getMessage());
            Alert::error('Erreur', 'Impossible de dépublier le programme.');
            if ($request->ajax()) {
                return response()->json(['success' => false], 500);
            }
            return redirect()->back();
        }
    }
}