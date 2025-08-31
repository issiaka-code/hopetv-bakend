<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ParametreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parametres = Parametre::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.parametres.index', compact('parametres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.parametres.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_site' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'nom_site' => $request->nom_site,
                'telephone' => $request->telephone,
                'insert_by' => Auth::id(),
                'update_by' => Auth::id(),
            ];

            // Gestion du logo
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('parametres/logos', 'public');
                $data['logo'] = $logoPath;
            }

            Parametre::create($data);
            Alert::success('Succès', 'Paramètre créé avec succès.');
            return redirect()->route('parametres.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du paramètre: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $parametre = Parametre::where('id', $id)
            ->where('is_deleted', false)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nom_site' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'update_by' => Auth::id(),
            ];

            if ($request->filled('nom_site')) {
                $data['nom_site'] = $request->nom_site;
            }

            if ($request->filled('telephone')) {
                $data['telephone'] = $request->telephone;
            }

            // Gestion du logo
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($parametre->logo && Storage::disk('public')->exists($parametre->logo)) {
                    Storage::disk('public')->delete($parametre->logo);
                }

                $logoPath = $request->file('logo')->store('parametres/logos', 'public');
                $data['logo'] = $logoPath;
            }

            $parametre->update($data);
            notify()->success('Succès', 'Paramètre modifié avec succès.');
            return redirect()->route('parametres.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification du paramètre: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parametre = Parametre::where('id', $id)
            ->where('is_deleted', false)
            ->firstOrFail();

        try {
            // Soft delete
            $parametre->update([
                'is_deleted' => true,
                'update_by' => Auth::id(),
            ]);
            notify()->success('Succès', 'Paramètre supprimé avec succès.');
            return redirect()->route('parametres.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du paramètre: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les paramètres du site (pour utilisation dans les vues)
     */
    public static function getSiteParameters()
    {
        return Parametre::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
