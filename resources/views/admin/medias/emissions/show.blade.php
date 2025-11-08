@extends('admin.master')

@section('title', 'Gestion des émissions')

@push('styles')
<style>
    /* === STRUCTURE GLOBALE === */
    #emissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .emission-card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        height: 100%;
    }

    .emission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    /* === MINIATURE === */
    .emission-thumbnail-container {
        overflow: hidden;
        height: 180px;
        position: relative;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .emission-thumbnail {
        cursor: pointer;
        height: 100%;
        width: 100%;
    }

    .emission-thumbnail video,
    .emission-thumbnail img,
    .emission-thumbnail iframe {
        object-fit: cover;
        height: 100%;
        width: 100%;
        transition: transform 0.3s;
    }

    .default-thumbnail {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        font-size: 3rem;
        color: white;
    }

    .emission-card:hover .emission-thumbnail video,
    .emission-card:hover .emission-thumbnail img {
        transform: scale(1.05);
    }

    /* === OVERLAY === */
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

    .emission-thumbnail:hover .thumbnail-overlay {
        opacity: 1;
    }

    /* === BADGES === */
    .media-type-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        font-size: 0.8rem;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-secondary {
        background-color: #6c757d;
    }

    /* === CONTENU === */
    .emission-card .card-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .emission-card .card-text {
        height: 40px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .btn-group .btn {
        border-radius: 5px;
        margin-left: 2px;
        padding: 0.25rem 0.5rem;
    }

    /* === ÉTAT VIDE === */
    .empty-state {
        padding: 3rem 1rem;
    }

    /* === RESPONSIVE === */
    @media (max-width: 1400px) {
        #emissions-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 1200px) {
        #emissions-grid {
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 1.25rem;
        }

        .emission-thumbnail-container {
            height: 160px;
        }
    }

    @media (max-width: 992px) {
        #emissions-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .emission-thumbnail-container {
            height: 140px;
        }

        .card-title {
            font-size: 1rem;
        }
    }

    @media (max-width: 768px) {
        #emissions-grid {
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 0.875rem;
        }

        .emission-thumbnail-container {
            height: 120px;
        }

        .btn-group .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        #emissions-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .emission-thumbnail-container {
            height: 180px;
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
        .emission-thumbnail-container {
            height: 150px;
        }

        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }

    /* === DIVERS === */
    .modal-header .close {
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
    }

    @media (hover: none) {
        .emission-card:hover {
            transform: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .thumbnail-overlay {
            opacity: 0.7;
        }
    }

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

        {{-- Messages d’erreur --}}
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

        {{-- Titre + bouton --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center section-header">
                    <h2 class="section-title">Médias disponibles</h2>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addstoreModal" data-route="{{ route('emissionsitem.store') }}" data-media-types="audio,video_link,video_file,images,pdf" data-emission-id="{{ $id }}">
                        <i class="fas fa-plus"></i> Ajouter un média
                    </button>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        {{-- <div class="row mb-4">
            <form method="GET" action="{{ route('emissions.index') }}" class="w-100">
                <div class="row g-2 d-flex flex-row justify-content-end align-items-center">
                    <div class="col-3">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Rechercher un emission...">
                    </div>

                    <div class="col-3">
                        <select name="type" class="form-control">
                            <option value="">Tous</option>
                            <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                            <option value="video_file" {{ request('type') === 'video_file' ? 'selected' : '' }}>Fichiers vidéo</option>
                            <option value="video_link" {{ request('type') === 'video_link' ? 'selected' : '' }}>Liens vidéo</option>
                            <option value="pdf" {{ request('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="images" {{ request('type') === 'images' ? 'selected' : '' }}>Images</option>
                        </select>
                    </div>

                    <div class="col-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fas fa-filter py-2"></i> Filtrer
                        </button>
                    </div>

                    <div class="col-md-2 my-1">
                        <a href="{{ route('emissions.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-sync py-2"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div> --}}

        {{-- Liste des éléments --}}
        <div class="row">
            <div class="col-12">
                <div id="emissions-grid">
                    @forelse($emissionItems as $item)
                    @php
                    $emissionid= $item->emission->id;
                    $id = $item->id;
                    $nom = $item->nom;
                    $description = $item->description;
                    $created_at = $item->created_at;
                    $media = $item->media;
                    $media_type = $media->type ?? '';
                    $media_url = $media->url_fichier ?? '';
                    $thumbnail_url = $media->thumbnail ?? '';
                    $is_published = $item->is_active ?? false;
                    @endphp

                    <div class="emission-grid-item">
                        <div class="card emission-card">
                            <div class="emission-thumbnail-container">
                                <div class="emission-thumbnail position-relative" data-emission-id="{{ $id }}" data-media-id="{{ $id }}">
                                   
                                    @if ($thumbnail_url)
                                    <img src="{{ asset('storage/' . $thumbnail_url) }}" alt="{{ $nom }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                    <div class="default-thumbnail d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        @if ($media_type === 'audio')
                                        <i class="fas fa-music text-white" style="font-size: 3rem;"></i>
                                        @elseif($media_type === 'video_link' || $media_type === 'video_file')
                                        <i class="fas fa-video text-white" style="font-size: 3rem;"></i>
                                        @elseif($media_type === 'pdf')
                                        <i class="fas fa-file-pdf text-white" style="font-size: 3rem;"></i>
                                        @elseif($media_type === 'images')
                                        <i class="fas fa-images text-white" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    @endif

                                    <div class="thumbnail-overlay">
                                        <i class="fas fa-play-circle"></i>
                                    </div>

                                    <span class="badge badge-primary media-type-badge">
                                        {{ ucfirst(str_replace('_', ' ', $media_type)) }}
                                    </span>

                                    @if (in_array($media_type, ['video_link', 'video_file']))
                                    <span class="badge {{ $is_published ? 'badge-success' : 'badge-secondary' }}" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                                        {{ $is_published ? 'Publié' : 'Non publié' }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title" title="{{ $nom }}">{{ Str::limit($nom, 25) }}</h5>
                                <p class="card-text text-muted small" title="{{ $description }}">
                                    {{ Str::limit($description, 30) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <small class="text-muted mb-1">{{ $created_at->format('d/m/Y') }}</small>

                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-info view-emission-btn rounded" title="Voir" data-media-id="{{ $id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-primary edit-emission-btn mx-1 rounded" title="Modifier" data-media-id="{{ $id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('emissionsitem.destroy', $id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet emission ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        {{-- @if (in_array($media_type, ['video_link', 'video_file']))
                                        <button class="btn btn-sm btn-outline-{{ $is_published ? 'success' : 'secondary' }} toggle-publish-btn mx-1 rounded"
                                        title="{{ $is_published ? 'Dépublier' : 'Publier' }}"
                                        data-emission-id="{{ $id }}"
                                        data-status="{{ $is_published ? 1 : 0 }}">
                                        <i class="fas fa-{{ $is_published ? 'toggle-on' : 'toggle-off' }}"></i>
                                        <span class="p-1">{{ $is_published ? 'Publié' : 'Non publié' }}</span>
                                        </button>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun emission disponible</h4>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $emissionItems->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // Toggle publication
        $(document).on('click', '.toggle-publish-btn', function() {
            const $btn = $(this);
            const id = $btn.data('emission-id');
            const isPublished = Number($btn.data('status')) === 1;
            const url = isPublished ?
                "{{ url('emissions') }}/" + id + "/unpublish" :
                "{{ url('emissions') }}/" + id + "/publish";

            $.post(url, {
                    _token: '{{ csrf_token() }}'
                })
                .done(() => window.location.reload())
                .fail(() => alert('Erreur lors du changement de statut de publication'));
        });

        // Edit
        $(document).on('click', '.edit-emission-btn', function() {
            handleEditMedia($(this)
                , "{{ route('emissionsitem.edit', ':id') }}"
                , "{{ route('emissionsitem.update', ':id') }}"
                , '#editModal');
        });

        // View
        $(document).on('click', '.view-emission-btn, .emission-thumbnail', function() {
            handleMediaView($(this), "{{ route('emissions.items.voir', ':id') }}");
        });



    });

</script>
@endpush
