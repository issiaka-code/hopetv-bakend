<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\InfoImportante;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class InfoImportanteController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    public function index(Request $request)
    {
        $query = InfoImportante::with('media')->where('is_deleted', false)->latest();

        // Recherche
        if ($request->filled('search')) {
            $query->where('nom', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }

        // Filtrage par type de média
        if ($request->filled('type')) {
            $query->whereHas('media', function ($q) use ($request) {
                if ($request->type === 'audio') {
                    $q->where('type', 'audio');
                } elseif ($request->type === 'video_file') {
                    $q->where('type', 'video');
                }
            });
        }

        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $infoImportantes = $query->paginate(12);

        // Préparer les données pour la vue
        $infoData = $infoImportantes->map(function ($info) {
            $isAudio = $info->media && $info->media->type === 'audio';
            $isVideoFile = $info->media && $info->media->type === 'video';

            $thumbnailUrl = null;
            $hasThumbnail = false;

            if ($isVideoFile) {
                // Pour les vidéos fichiers, utiliser l'image de couverture si disponible
                if ($info->media && $info->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $info->media->thumbnail);
                    $hasThumbnail = true;
                }
            } elseif ($isAudio) {
                // Pour les audios, utiliser l'image de couverture si disponible
                if ($info->media && $info->media->thumbnail) {
                    $thumbnailUrl = asset('storage/' . $info->media->thumbnail);
                    $hasThumbnail = true;
                }
            }

            return (object)[
                'id' => $info->id,
                'nom' => $info->nom,
                'description' => $info->description,
                'is_active' => $info->is_active,
                'created_at' => $info->created_at,
                'media_type' => $isAudio ? 'audio' : 'video_file',
                'thumbnail_url' => $thumbnailUrl,
                'media_url' => $info->media ? ($isVideoFile ? asset('storage/' . $info->media->url_fichier) : ($isAudio ? asset('storage/' . $info->media->url_fichier) : null)) : null,
                'has_thumbnail' => $hasThumbnail,
            ];
        });

        return view('admin.medias.info_importantes.index', [
            'infoImportantes' => $infoImportantes,
            'infoData' => $infoData,
        ]);
    }

    public function store(Request $request)
    {
        $result = $this->mediaService->createMedia($request);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $errors = $result['errors'];

            if ($errors instanceof \Illuminate\Support\MessageBag) {
                foreach ($errors->all() as $error) {
                    notify()->error($error);
                }
            } elseif (is_array($errors)) {
                foreach ($errors as $error) {
                    notify()->error($error);
                }
            } elseif (is_string($errors)) {
                notify()->error($errors);
            }

            return back()->withInput();
        }
        $media = $result;

        // === CRÉATION DE L'INFORMATION IMPORTANTE ===
        InfoImportante::create([
            'id_media' => $media->id,
            'nom' => $request->nom,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Information importante ajoutée avec succès.');
        return redirect()->route('info_importantes.index');
    }

    public function edit(InfoImportante $infoImportante)
    {
        $infoImportante->load('media');
        return response()->json([
            'nom' => $infoImportante->nom,
            'description' => $infoImportante->description,
            'is_active' => $infoImportante->is_active,
            'media' => $infoImportante->media
        ]);
    }

    public function update(Request $request, $id)
    {
        $infoImportante = InfoImportante::findOrFail($id);
        $media = $infoImportante->media;

        $result = $this->mediaService->updateMedia($request, $media);

        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            $errors = $result['errors'];

            if ($errors instanceof \Illuminate\Support\MessageBag) {
                foreach ($errors->all() as $error) {
                    notify()->error($error);
                }
            } elseif (is_array($errors)) {
                foreach ($errors as $error) {
                    notify()->error($error);
                }
            } elseif (is_string($errors)) {
                notify()->error($errors);
            }

            return back()->withInput();
        }

        $infoImportante->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'update_by' => auth()->id(),
        ]);

        notify()->success('Succès', 'Information importante mise à jour avec succès.');
        return redirect()->route('info_importantes.index');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $infoImportante = InfoImportante::findOrFail($id);
            
            $infoImportante->update([
                'is_active' => $request->is_active,
                'update_by' => auth()->id(),
            ]);

            $status = $request->is_active ? 'activée' : 'désactivée';
            notify()->success('Succès', "Information importante {$status} avec succès.");
            return response()->json([
                'success' => true,
            ]);

        } catch (\Exception $e) {
            notify()->error('Erreur', 'Erreur lors du changement de statut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $infoImportante = InfoImportante::findOrFail($id);

            // Supprimer l'information importante
            $infoImportante->is_deleted = true;
            $infoImportante->save();

            DB::commit();
            notify()->success('Succès', 'Information importante supprimée avec succès.');
            return redirect()->route('info_importantes.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function voirInfoImportante($id)
    {
        try {
            $infoImportante = InfoImportante::with('media')->findOrFail($id);
            $media = $infoImportante->media;

            if ($media->status !== 'ready') {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'Le média est en cours de traitement. Veuillez réessayer plus tard.'
                ], 200);
            }

            $url = $media->url_fichier;

            return response()->json([
                'status' => 'ready',
                'data' => [
                    'id' => $infoImportante->id,
                    'nom' => $infoImportante->nom,
                    'description' => $infoImportante->description,
                    'media' => [
                        'url' => asset('storage/' . $url),
                        'thumbnail' => $media->thumbnail ? asset('storage/' . $media->thumbnail) : null,
                        'type' => $media->type,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement de l\'information importante : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(InfoImportante $infoImportante)
    {
        $infoImportante->load('media');
        return response()->json($infoImportante);
    }
}