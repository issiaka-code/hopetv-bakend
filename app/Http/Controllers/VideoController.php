<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Media;
use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with(['media'])
            ->where('is_deleted', false)
            ->paginate(10);
        return view('admin.medias.videos.index', compact('videos'));
    }


public function store(Request $request)
{
    // Validation de base
    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'required|string',
        'video_type' => 'required|in:file,link',
    ]);

    // Validation conditionnelle selon le type
    if ($request->video_type === 'file') {
        $request->validate([
            'fichier_video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,mkv,webm|max:1024000', // Augmentez la taille si nécessaire
        ]);
    } elseif ($request->video_type === 'link') {
        $request->validate([
            'lien_video' => 'required|url',
        ]);
    }

    try {
        DB::beginTransaction();

        $filePaths = [];
        $type = $request->video_type === 'file' ? 'video' : 'link';

        // Traitement selon le type
        if ($request->video_type === 'file' && $request->hasFile('fichier_video')) {
            $file = $request->file('fichier_video');
            
            if (!$file->isValid()) {
                throw new \Exception('Fichier vidéo invalide');
            }

            // Générer un nom unique
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueName = $originalName . '_' . now()->format('Ymd_His');
            
            // Stocker le fichier original temporairement
            $tempPath = $file->storeAs('temp/videos', $uniqueName . '_original.' . $file->getClientOriginalExtension());
            
            // Convertir la vidéo en différentes qualités
            $filePaths = $this->convertVideoForStreaming($tempPath, $uniqueName);
            
            // Supprimer le fichier temporaire
            Storage::delete($tempPath);

        } elseif ($request->video_type === 'link') {
            $filePaths['original'] = $request->lien_video;
            
            if (!filter_var($filePaths['original'], FILTER_VALIDATE_URL)) {
                throw new \Exception('URL de vidéo invalide');
            }
        } else {
            throw new \Exception('Type de vidéo ou fichier manquant');
        }

        // Vérification finale
        if (empty($filePaths)) {
            throw new \Exception('Aucun fichier ou lien de vidéo fourni');
        }

        // Création du média
        $media = Media::create([
            'url_fichier' => json_encode($filePaths), // Stocker toutes les qualités
            'type' => $type,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        if (!$media) {
            throw new \Exception('Erreur lors de la création du média');
        }

        // Création de la vidéo
        $video = Video::create([
            'id_media' => $media->id,
            'nom' => $request->nom,
            'description' => $request->description,
            'insert_by' => auth()->id(),
            'update_by' => auth()->id(),
        ]);

        if (!$video) {
            throw new \Exception('Erreur lors de la création de la vidéo');
        }

        DB::commit();
        Alert::success('Succès', 'Vidéo créée avec succès');
        return redirect()->route('videos.index');

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        Alert::error('Erreur', $e->getMessage());
        return redirect()->back()
            ->with('error', 'Erreur lors de la création de la vidéo: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Convertit une vidéo pour le streaming avec différentes qualités
 */
private function convertVideoForStreaming($tempPath, $uniqueName)
{
    $outputPaths = [];
    
    try {
        $lowBitrate = (new X264('aac'))->setKiloBitrate(500);
        $midBitrate = (new X264('aac'))->setKiloBitrate(1000);
        $highBitrate = (new X264('aac'))->setKiloBitrate(2500);
        
        // Convertir en différentes qualités
        FFMpeg::fromDisk('local')
            ->open($tempPath)
            ->export()
            ->toDisk('public')
            ->inFormat($lowBitrate)
            ->save("medias/videos/{$uniqueName}_360p.mp4");
            
        FFMpeg::fromDisk('local')
            ->open($tempPath)
            ->export()
            ->toDisk('public')
            ->inFormat($midBitrate)
            ->save("medias/videos/{$uniqueName}_720p.mp4");
            
        FFMpeg::fromDisk('local')
            ->open($tempPath)
            ->export()
            ->toDisk('public')
            ->inFormat($highBitrate)
            ->save("medias/videos/{$uniqueName}_1080p.mp4");
            
        $outputPaths = [
            '360p' => "medias/videos/{$uniqueName}_360p.mp4",
            '720p' => "medias/videos/{$uniqueName}_720p.mp4", 
            '1080p' => "medias/videos/{$uniqueName}_1080p.mp4"
        ];
        
    } catch (\Exception $e) {
        // Fallback: si la conversion échoue, utiliser le fichier original
        $originalFile = Storage::get($tempPath);
        $fallbackPath = "medias/videos/{$uniqueName}.mp4";
        Storage::disk('public')->put($fallbackPath, $originalFile);
        
        $outputPaths = ['original' => $fallbackPath];
    }
    
    return $outputPaths;
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
        // Validation de base
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'video_type' => 'required|in:file,link',
        ]);

        try {
            DB::beginTransaction();

            // Récupération de la vidéo
            $video = Video::with('media')->findOrFail($id);
            $media = $video->media;

            $filePath = $media->url_fichier;
            $type = $media->type;

            // Mise à jour conditionnelle
            if ($request->video_type === 'file') {
                $request->validate([
                    'fichier_video' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,mkv|max:102400',
                ]);

                if ($request->hasFile('fichier_video')) {
                    $file = $request->file('fichier_video');

                    if (!$file->isValid()) {
                        throw new \Exception('Fichier vidéo invalide');
                    }

                    // Suppression de l'ancien fichier si c'était un fichier
                    if ($type === 'video' && Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }

                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = $originalName . '_' . now()->format('Ymd_His') . '.' . $extension;

                    $filePath = $file->storeAs('medias/videos', $uniqueName, 'public');
                    $type = 'video';
                }

            } elseif ($request->video_type === 'link') {
                $request->validate([
                    'lien_video' => 'required|url',
                ]);

                $filePath = $request->lien_video;
                $type = 'link';

                if (!filter_var($filePath, FILTER_VALIDATE_URL)) {
                    throw new \Exception('URL de vidéo invalide');
                }

                // Suppression de l'ancien fichier si avant c'était un fichier
                if ($media->type === 'video' && Storage::disk('public')->exists($media->url_fichier)) {
                    Storage::disk('public')->delete($media->url_fichier);
                }
            }

            // Vérification finale
            if (empty($filePath)) {
                throw new \Exception('Aucun fichier ou lien de vidéo fourni');
            }

            // Mise à jour du média
            $media->update([
                'url_fichier' => $filePath,
                'type' => $type,
                'update_by' => auth()->id(),
            ]);

            // Mise à jour de la vidéo
            $video->update([
                'nom' => $request->nom,
                'description' => $request->description,
                'update_by' => auth()->id(),
            ]);

            DB::commit();
            Alert::success('Succès', 'Vidéo mise à jour avec succès');
            return redirect()->route('videos.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la vidéo: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function destroy(Video $video)
    {
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

            return redirect()->route('videos.index')
                ->with('success', 'Vidéo supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}