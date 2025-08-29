<?php

namespace App\Http\Controllers;

use App\Models\LienUtile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LienUtileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LienUtile::where('is_deleted', false);
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nom', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('lien', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $liens = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('admin.lien_utiles.index', compact('liens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.liens-utiles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'lien' => 'required|url|max:500',
        ]);

        try {
            LienUtile::create([
                'nom' => $validated['nom'],
                'lien' => $validated['lien'],
                'insert_by' => Auth::id(),
                'update_by' => Auth::id(),
            ]);

            return redirect()->route('liens-utiles.index')
                ->with('success', 'Lien utile ajouté avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout du lien: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lien = LienUtile::where('id', $id)->where('is_deleted', false)->firstOrFail();
        return view('admin.liens-utiles.show', compact('lien'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lien = LienUtile::where('id', $id)->where('is_deleted', false)->firstOrFail();
        return view('admin.liens-utiles.edit', compact('lien'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lien = LienUtile::where('id', $id)->where('is_deleted', false)->firstOrFail();
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'lien' => 'required|url|max:500',
        ]);

        try {
            $lien->update([
                'nom' => $validated['nom'],
                'lien' => $validated['lien'],
                'update_by' => Auth::id(),
            ]);

            return redirect()->route('liens-utiles.index')
                ->with('success', 'Lien utile modifié avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification du lien: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lien = LienUtile::where('id', $id)->where('is_deleted', false)->firstOrFail();
        
        try {
            // Soft delete
            $lien->update([
                'is_deleted' => true,
                'update_by' => Auth::id(),
            ]);

            return redirect()->route('liens-utiles.index')
                ->with('success', 'Lien utile supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du lien: ' . $e->getMessage());
        }
    }
}