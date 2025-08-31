@extends('admin.master')

@section('title', 'Gestion des Paramètres du Site')

@push('styles')
    <style>
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
            margin-bottom: 25px;
            background: white;
        }

        .card-header {
            background: #f8f9fc;
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
            color: #4e73df;
            font-size: 1.3rem;
        }

        .card-body {
            padding: 25px;
        }

        .logo-preview-container {
            text-align: center;
            margin-bottom: 20px;
            background: #f8f9fc;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #d1d3e2;
        }

        .logo-preview {
            width: 100%;
            height: 150px;
            object-fit: contain;
            display: none;
        }

        .no-logo {
            font-size: 3rem;
            color: #d1d3e2;
            margin: 10px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 6px;
            display: block;
        }

        .form-control {
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #d1d3e2;
            width: 100%;
            transition: border-color 0.15s;
        }

        .form-control:focus {
            border-color: #4e73df;
            outline: none;
        }

        .file-input {
            padding: 8px;
            border: 1px solid #d1d3e2;
            border-radius: 6px;
            width: 100%;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #4e73df;
            color: white;
        }

        .btn-primary:hover {
            background: #3a56c4;
        }

        .btn-danger {
            background: #e74a3b;
            color: white;
        }

        .btn-danger:hover {
            background: #d52a1a;
        }

        .btn-info {
            background: #36b9cc;
            color: white;
        }

        .btn-info:hover {
            background: #2a96a6;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        .current-value {
            background: #f8f9fc;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6e707e;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .text-muted {
            color: #858796 !important;
        }

        @media (max-width: 768px) {
            .settings-container {
                padding: 15px;
            }

            .card-body {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
            border: none;
        }

        .section-title {
            font-size: 1.5rem;
            color: #4e73df;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .file-input-container {
            margin-bottom: 15px;
        }

        .preview-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #5a5c69;
        }
    </style>
@endpush

@section('content')
    <section class="section" style="margin-top: -25px;">
        <div class="settings-container">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- En-tête -->
            <div class="row mb-4">
                <div class="col-12 text-center d-flex flex-row justify-content-between align-items-center">
                    <h2 class="section-title">Paramètres </h2>
                    <p class="text-muted">Gérez la configuration</p>
                </div>
            </div>

            <!-- Deux cartes côte à côte -->
            <div class="row">
                <!-- Carte Logo -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3>Logo du Site</h3>
                        </div>
                        <div class="card-body">
                            @if ($parametres->count() > 0)
                                @foreach ($parametres as $param)
                                    <!-- Preview du logo existant -->
                                    <div class="logo-preview-container">
                                        @if ($param->logo)
                                            <img src="{{ asset('storage/' . $param->logo) }}" alt="Logo du site"
                                                class="logo-preview" id="current-logo-preview" style="display: block;">
                                            <div class="no-logo" style="display: none;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @else
                                            <img src="" alt="Logo du site" class="logo-preview"
                                                id="current-logo-preview" style="display: none;">
                                            <div class="no-logo">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <p class="text-muted">Aucun logo configuré</p>
                                        @endif
                                    </div>

                                    <form action="{{ route('parametres.update', $param->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="update_type" value="logo">

                                        <div class="form-group">
                                            <label for="logo">Nouveau Logo</label>
                                            <input type="file" id="logo" name="logo" accept="image/*" class="form-control file-input"
                                                onchange="previewImage(this, 'new-logo-preview')">
                                        </div>

                                        <!-- Preview du nouveau logo -->
                                        <div id="new-logo-preview-container" style="display: none;">
                                            <div class="preview-title">Aperçu du nouveau logo:</div>
                                            <div class="logo-preview-container">
                                                <img id="new-logo-preview" class="logo-preview" style="display: block;">
                                            </div>
                                        </div>

                                        <div class="action-buttons">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload mr-1"></i>Mettre à jour
                                            </button>
                                            @if ($param->logo)
                                                <button type="button" class="btn btn-danger bg-danger"
                                                    onclick="if(confirm('Supprimer le logo ?')) { document.getElementById('delete-logo-form').submit(); }">
                                                    <i class="fas fa-trash mr-1"></i>Supprimer
                                                </button>
                                            @endif
                                        </div>
                                    </form>

                                    @if ($param->logo)
                                        <form id="delete-logo-form" action="{{ route('parametres.update', $param->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="update_type" value="delete_logo">
                                        </form>
                                    @endif
                                @endforeach
                            @else
                                <!-- Formulaire initial -->
                                <form action="{{ route('parametres.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="update_type" value="logo">

                                    <div class="form-group">
                                        <label for="initial_logo">Logo du Site</label>
                                        <input type="file" id="initial_logo" name="logo" accept="image/*" 
                                            class="form-control file-input" required
                                            onchange="previewImage(this, 'initial-logo-preview')">
                                    </div>

                                    <!-- Preview du logo initial -->
                                    <div id="initial-logo-preview-container" style="display: none;">
                                        <div class="preview-title">Aperçu du logo:</div>
                                        <div class="logo-preview-container">
                                            <img id="initial-logo-preview" class="logo-preview" style="display: block;">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nom_site">Nom du Site</label>
                                        <input type="text" class="form-control" id="nom_site" name="nom_site" required
                                            placeholder="Nom de votre site">
                                    </div>

                                    <div class="form-group">
                                        <label for="telephone">Téléphone</label>
                                        <input type="text" class="form-control" id="telephone" name="telephone"
                                            placeholder="Numéro de téléphone">
                                    </div>

                                    <div class="action-buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Enregistrer
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Carte Informations -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3>Informations du Site</h3>
                        </div>
                        <div class="card-body">
                            @if ($parametres->count() > 0)
                                @foreach ($parametres as $param)
                                    <form action="{{ route('parametres.update', $param->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="update_type" value="info">

                                        <div class="form-group">
                                            <label for="edit_nom_site">Nom du Site</label>
                                            <input type="text" class="form-control" id="edit_nom_site" name="nom_site"
                                                value="{{ old('nom_site', $param->nom_site) }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_telephone">Téléphone</label>
                                            <input type="text" class="form-control" id="edit_telephone" name="telephone"
                                                value="{{ old('telephone', $param->telephone) }}"
                                                placeholder="Ex: +33 1 23 45 67 89">
                                        </div>

                                        <div class="current-value">
                                            <strong>Dernière modification :</strong>
                                            {{ $param->updated_at->format('d/m/Y à H:i') }}
                                            par {{ $param->updatedBy->name ?? 'Admin' }}
                                        </div>

                                        <div class="action-buttons mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-1"></i>Mettre à jour
                                            </button>

                                            <button type="button" class="btn btn-danger bg-danger"
                                                onclick="if(confirm('Supprimer tous les paramètres ?')) { document.getElementById('delete-param-form').submit(); }">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </form>

                                    <form id="delete-param-form" action="{{ route('parametres.destroy', $param->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endforeach
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Aucune information configurée</p>
                                    <p class="text-muted small">Veuillez d'abord ajouter un logo pour commencer</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewId + '-container');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    previewContainer.style.display = 'block';
                    
                    // Masquer le placeholder "no-logo" si présent
                    const noLogo = previewContainer.previousElementSibling;
                    if (noLogo && noLogo.classList.contains('no-logo')) {
                        noLogo.style.display = 'none';
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Désactiver les boutons pendant l'envoi
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> En cours...');
            });
        });
    </script>
@endpush