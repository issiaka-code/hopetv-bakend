@extends('admin.master')

@section('title', 'Gestion des Informations Importantes')

@push('styles')
    <style>
        /* Styles CSS identiques à ceux des podcasts mais adaptés pour info importantes */
        .btn-group-toggle .btn {
            border-radius: 5px;
        }

        .btn-group-toggle .btn.active {
            background-color: #4e73df;
            color: white;
            border-color: #4e73df;
        }

        #audioFileSection,
        #videoFileSection {
            transition: opacity 0.3s ease;
        }

        .section-title {
            font-size: 1.5rem;
            color: #4e73df;
            margin-bottom: 0;
        }

        .info-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .info-thumbnail-container {
            overflow: hidden;
            height: 180px;
            position: relative;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-thumbnail {
            cursor: pointer;
            height: 100%;
            width: 100%;
        }

        .info-thumbnail video,
        .info-thumbnail img {
            object-fit: cover;
            height: 100%;
            width: 100%;
            transition: transform 0.3s;
        }

        .audio-thumbnail {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-size: 4rem;
            color: #4e73df;
        }

        .info-card:hover .info-thumbnail video,
        .info-card:hover .info-thumbnail img {
            transform: scale(1.05);
        }

        .thumbnail-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
        }

        .thumbnail-overlay i {
            font-size: 3rem;
            color: white;
        }

        .info-thumbnail:hover .thumbnail-overlay {
            opacity: 1;
        }

        .info-card .card-title {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-card .card-text {
            height: 40px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .btn-group .btn {
            border-radius: 5px;
            margin-left: 2px;
            padding: 0.25rem 0.5rem;
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        /* Styles pour la grille responsive */
        #info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .info-grid-item {
            width: 100%;
        }

        /* Personnalisation du file input */
        .custom-file-label::after {
            content: "Parcourir";
        }

        /* Media type badge */
        .media-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        /* Status badge */
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }

        /* Responsive improvements */
        @media (max-width: 1400px) {
            #info-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            #info-grid {
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                gap: 1.25rem;
            }

            .info-thumbnail-container {
                height: 160px;
            }
        }

        @media (max-width: 992px) {
            #info-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .info-thumbnail-container {
                height: 140px;
            }

            .section-title {
                font-size: 1.35rem;
            }

            .card-title {
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            #info-grid {
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
                gap: 0.875rem;
            }

            .info-thumbnail-container {
                height: 120px;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .btn-group .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            #info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                max-width: 400px;
                margin: 0 auto;
            }

            .info-thumbnail-container {
                height: 180px;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .section-title {
                font-size: 1.4rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .btn-group {
                width: 100%;
                justify-content: center;
                margin-top: 0.5rem;
            }

            .btn-group .btn {
                margin: 0 2px;
                flex: 1;
                max-width: 45px;
            }
        }

        @media (max-width: 400px) {
            .info-thumbnail-container {
                height: 150px;
            }

            .modal-dialog {
                margin: 0.25rem;
            }

            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Mobile first improvements */
        .modal-header .close {
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }

        /* Touch device improvements */
        @media (hover: none) {
            .info-card:hover {
                transform: none;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            }

            .thumbnail-overlay {
                opacity: 0.7;
            }

            .btn-group .btn {
                padding: 0.4rem 0.6rem;
            }
        }

        /* High density screens */
        @media (min-resolution: 2dppx) {
            .thumbnail-overlay i {
                font-size: 2.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <section class="section" style="margin-top: -25px;">
        <div class="section-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center section-header">
                        <h2 class="section-title">Informations Importantes</h2>
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#addstoreModal" data-route="{{ route('info_importantes.store') }}"
                            data-media-types="audio,video_file" data-section-name="information importante">
                            <i class="fas fa-plus"></i> Ajouter une information
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <form method="GET" action="{{ route('info_importantes.index') }}" class="w-100">
                    <div class="row g-2 d-flex flex-row justify-content-end align-items-center">
                        <!-- Champ recherche -->
                        <div class="col-3">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Rechercher une information...">
                        </div>

                        <!-- Filtre par type -->
                        <div class="col-2">
                            <select name="type" class="form-control">
                                <option value="">Tous</option>
                                <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                                <option value="video_file" {{ request('type') === 'video_file' ? 'selected' : '' }}>Fichiers vidéo</option>
                            </select>
                        </div>

                        <!-- Filtre par statut -->
                        <div class="col-2">
                            <select name="status" class="form-control">
                                <option value="">Tous</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>

                        <!-- Bouton recherche -->
                        <div class="col-2">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-filter py-2"></i> Filtrer
                            </button>
                        </div>
                        <div class="col-2">
                            <a href="{{ route('info_importantes.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-sync py-2"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Grille d'informations importantes -->
            <div class="row">
                <div class="col-12">
                    <div id="info-grid">
                        @forelse($infoData as $info)
                            <div class="info-grid-item">
                                <div class="card info-card">
                                    <div class="info-thumbnail-container">
                                        <div class="info-thumbnail position-relative" data-info-id="{{ $info->id }}" data-section-name="information importante">
                                            <!-- Afficher l'image de couverture ou icône par défaut -->
                                            @if ($info->has_thumbnail && $info->thumbnail_url)
                                                <img src="{{ $info->thumbnail_url }}" alt="{{ $info->nom }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div class="default-thumbnail d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                    @if ($info->media_type === 'audio')
                                                        <i class="fas fa-music text-white" style="font-size: 3rem;"></i>
                                                    @elseif($info->media_type === 'video_file')
                                                        <i class="fas fa-video text-white" style="font-size: 3rem;"></i>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="thumbnail-overlay">
                                                <i class="fas fa-play-circle"></i>
                                            </div>

                                            <span class="badge badge-primary media-type-badge">
                                                {{ $info->media_type === 'audio' ? 'Audio' : 'Fichier vidéo' }}
                                            </span>

                                            <span class="badge {{ $info->is_active ? 'badge-success' : 'badge-danger' }} status-badge">
                                                {{ $info->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title" title="{{ $info->nom }}">
                                            {{ Str::limit($info->nom, 25) }}
                                        </h5>
                                        <p class="card-text text-muted small" title="{{ $info->description }}">
                                            {{ Str::limit($info->description, 30) }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <small class="text-muted mb-1">
                                                {{ $info->created_at->format('d/m/Y') }}
                                            </small>

                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info view-info-btn rounded"
                                                    data-info-id="{{ $info->id }}" data-section-name="information importante" title="Voir l'information">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary edit-info-btn mx-1 rounded"
                                                    data-info-id="{{ $info->id }}" data-media-types="audio,video_file" data-section-name="information importante" title="Modifier l'information">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form action="{{ route('info_importantes.destroy', $info->id) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette information ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-outline-{{ $info->is_active ? 'success' : 'danger' }} toggle-status-btn mx-1 rounded"
                                                    data-info-id="{{ $info->id }}" 
                                                    data-status="{{ $info->is_active }}"
                                                    title="{{ $info->is_active ? 'Actif' : 'Inactif' }}">
                                                    <i class="fas fa-{{ $info->is_active ? 'toggle-on' : 'toggle-off' }}"><span class="p-1">{{ $info->is_active ? 'Actif' : 'Inactif' }}</span></i>  
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                    </div>
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucune information importante disponible</h4>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination si nécessaire -->
            @if ($infoImportantes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $infoImportantes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ===== TOGGLE STATUS =====
            $(document).on('click', '.toggle-status-btn', function() {
                const infoId = $(this).data('info-id');
                const currentStatus = $(this).data('status');
                const newStatus = currentStatus ? 0 : 1;

                $.ajax({
                    url: "{{ route('info_importantes.toggle_status', ':id') }}".replace(':id', infoId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: newStatus
                    },
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function() {
                        alert('Erreur lors de la mise à jour du statut');
                    }
                });
            });

            // ===== ÉDITION DES INFORMATIONS IMPORTANTES =====
            $(document).on('click', '.edit-info-btn', function() {
                handleEditMedia(
                    $(this)
                    , "{{ route('info_importantes.edit', ':id') }}"
                    , "{{ route('info_importantes.update', ':id') }}"
                    , '#editModal'
                );
            });

            // ===== VISUALISATION DES INFORMATIONS IMPORTANTES =====
            $(document).on('click', '.view-info-btn, .info-thumbnail', function() {
                handleMediaView($(this), "{{ route('info_importantes.voir', ':id') }}");
            });

        });
    </script>
@endpush