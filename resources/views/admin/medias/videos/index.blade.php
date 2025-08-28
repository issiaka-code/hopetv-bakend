@extends('admin.master')

@section('title', 'Gestion des Vidéos')

@push('styles')
    <style>
        .btn-group-toggle .btn {
            border-radius: 5px;
        }

        .btn-group-toggle .btn.active {
            background-color: #4e73df;
            color: white;
            border-color: #4e73df;
        }

        #videoFileSection,
        #videoLinkSection {
            transition: opacity 0.3s ease;
        }

        .section-title {
            font-size: 1.5rem;
            color: #4e73df;
            margin-bottom: 0;
        }

        .video-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            height: 100%;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .video-thumbnail-container {
            overflow: hidden;
            height: 180px;
            position: relative;
        }

        .video-thumbnail {
            cursor: pointer;
            height: 100%;
            width: 100%;
        }

        .video-thumbnail video,
        .video-thumbnail img {
            object-fit: cover;
            height: 100%;
            width: 100%;
            transition: transform 0.3s;
        }

        .video-card:hover .video-thumbnail video,
        .video-card:hover .video-thumbnail img {
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

        .video-thumbnail:hover .thumbnail-overlay {
            opacity: 1;
        }

        .video-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .video-card .card-title {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .video-card .card-text {
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
        #videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .video-grid-item {
            width: 100%;
        }

        /* Personnalisation du file input */
        .custom-file-label::after {
            content: "Parcourir";
        }

        /* Responsive improvements */
        @media (max-width: 1400px) {
            #videos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            #videos-grid {
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                gap: 1.25rem;
            }

            .video-thumbnail-container {
                height: 160px;
            }
        }

        @media (max-width: 992px) {
            #videos-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .video-thumbnail-container {
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
            #videos-grid {
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
                gap: 0.875rem;
            }

            .video-thumbnail-container {
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
            #videos-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                max-width: 400px;
                margin: 0 auto;
            }

            .video-thumbnail-container {
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
            .video-thumbnail-container {
                height: 150px;
            }

            .modal-dialog {
                margin: 0.25rem;
            }

            .modal-content {
                border-radius: 0.5rem;
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
            .video-card:hover {
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
    <section class="section">
        <div class="section-body">
            <!-- Afficher les messages de succès/erreur -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

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
                        <h2 class="section-title">Vidéos disponibles</h2>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVideoModal">
                            <i class="fas fa-plus"></i> Ajouter une vidéo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grille de vidéos -->
            <div class="row">
                <div class="col-12">
                    <div id="videos-grid">
                        @forelse($videos as $video)
                            @php
                                $videoUrl =
                                    $video->media && $video->media->url_fichier
                                        ? ($video->media->type === 'link'
                                            ? $video->media->url_fichier
                                            : asset('storage/' . $video->media->url_fichier))
                                        : '#';
                                $isLink = $video->media && $video->media->type === 'link';
                            @endphp

                            <div class="video-grid-item">
                                <div class="card video-card">
                                    <div class="video-thumbnail-container">
                                        <div class="video-thumbnail position-relative" data-video-url="{{ $videoUrl }}"
                                            data-video-name="{{ $video->nom }}">
                                            @if ($isLink)
                                                <img src="https://via.placeholder.com/300x200/007bff/ffffff?text=Video+Link"
                                                    alt="Miniature lien vidéo">
                                            @else
                                                <video preload="metadata" muted>
                                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                                    Votre navigateur ne supporte pas la lecture de vidéos.
                                                </video>
                                            @endif
                                            <div class="thumbnail-overlay">
                                                <i class="fas fa-play-circle"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $video->nom }}</h5>
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($video->description, 80) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <small class="text-muted mb-1">
                                                {{ $video->created_at->format('d/m/Y') }}
                                            </small>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info view-video-btn rounded"
                                                    data-video-url="{{ $videoUrl }}"
                                                    data-video-name="{{ $video->nom }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary edit-video-btn mx-1 rounded"
                                                    data-toggle="modal" data-target="#editVideoModal"
                                                    data-video-id="{{ $video->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('videos.destroy', $video->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-video-slash fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">Aucune vidéo disponible</h4>
                                    <p class="text-muted">Commencez par ajouter votre première vidéo</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pagination si nécessaire -->
            @if ($videos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $videos->links() }}
                </div>
            @endif
        </div>
    </section>

    <div class="modal fade" id="addVideoModal" tabindex="-1" role="dialog" aria-labelledby="addVideoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-static" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addVideoModalLabel">Ajouter une vidéo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addVideoForm" method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="addVideoNom"
                                class="form-control @error('nom') is-invalid @enderror" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="addVideoDescription" class="form-control @error('description') is-invalid @enderror"
                                rows="3" required></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Type de vidéo <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="video_type" id="addVideoTypeFile" value="file"
                                        autocomplete="off" checked> Fichier
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="video_type" id="addVideoTypeLink" value="link"
                                        autocomplete="off"> Lien
                                </label>
                            </div>
                        </div>

                        <!-- Section Fichier Vidéo pour AJOUT -->
                        <div id="addVideoFileSection">
                            <div class="form-group">
                                <label class="font-weight-bold">Fichier Vidéo <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="fichier_video" id="addVideoFichier"
                                        class="custom-file-input @error('fichier_video') is-invalid @enderror"
                                        accept="video/*" required>
                                    <label class="custom-file-label" for="addVideoFichier">Choisir un fichier</label>
                                    @error('fichier_video')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Formats acceptés: MP4, AVI, MOV, etc. (max
                                    100MB)</small>
                            </div>
                        </div>

                        <!-- Section Lien Vidéo pour AJOUT -->
                        <div id="addVideoLinkSection" class="d-none">
                            <div class="form-group">
                                <label class="font-weight-bold">Lien de la vidéo <span
                                        class="text-danger">*</span></label>
                                <input type="url" name="lien_video" id="addVideoLink"
                                    class="form-control @error('lien_video') is-invalid @enderror"
                                    placeholder="https://example.com/video.mp4">
                                @error('lien_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Entrez l'URL complète de la vidéo (YouTube, Vimeo, ou
                                    lien direct)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour ÉDITER une vidéo -->
    <div class="modal fade" id="editVideoModal" tabindex="-1" role="dialog" aria-labelledby="editVideoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-static" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title" id="editVideoModalLabel">Modifier la vidéo</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editVideoForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="editVideoNom"
                                class="form-control @error('nom') is-invalid @enderror" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="editVideoDescription"
                                class="form-control @error('description') is-invalid @enderror" rows="3" required></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Type de vidéo <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-info active" id="editVideoTypeFileLabel">
                                    <input type="radio" name="video_type" id="editVideoTypeFile" value="file"
                                        autocomplete="off" checked> Fichier
                                </label>
                                <label class="btn btn-outline-info" id="editVideoTypeLinkLabel">
                                    <input type="radio" name="video_type" id="editVideoTypeLink" value="link"
                                        autocomplete="off"> Lien
                                </label>
                            </div>
                        </div>

                        <!-- Section Fichier Vidéo pour ÉDITION -->
                        <div id="editVideoFileSection">
                            <div class="form-group">
                                <label class="font-weight-bold">Fichier Vidéo</label>
                                <div class="custom-file">
                                    <input type="file" name="fichier_video" id="editVideoFichier"
                                        class="custom-file-input @error('fichier_video') is-invalid @enderror"
                                        accept="video/*">
                                    <label class="custom-file-label" for="editVideoFichier">Choisir un nouveau
                                        fichier</label>
                                    @error('fichier_video')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Formats acceptés: MP4, AVI, MOV, etc. (max
                                    100MB)</small>

                                <div id="editCurrentVideo" class="mt-2">
                                    <small>Fichier actuel: <span id="editCurrentVideoName"></span></small>
                                    <button type="button" class="btn btn-sm btn-info ml-2" id="editViewCurrentVideo">
                                        <i class="fa fa-eye"></i> Voir
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Section Lien Vidéo pour ÉDITION -->
                        <div id="editVideoLinkSection" class="d-none">
                            <div class="form-group">
                                <label class="font-weight-bold">Lien de la vidéo <span
                                        class="text-danger">*</span></label>
                                <input type="url" name="lien_video" id="editVideoLink"
                                    class="form-control @error('lien_video') is-invalid @enderror"
                                    placeholder="https://example.com/video.mp4">
                                @error('lien_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Entrez l'URL complète de la vidéo</small>

                                <div id="editCurrentLink" class="mt-2">
                                    <small>Lien actuel: <span id="editCurrentLinkValue"></span></small>
                                    <a href="#" target="_blank" class="btn btn-sm btn-info ml-2"
                                        id="editViewCurrentLink">
                                        <i class="fa fa-external-link-alt"></i> Voir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour visualiser la vidéo (inchangé) -->
    <div class="modal fade" id="videoViewModal" tabindex="-1" role="dialog" aria-labelledby="videoViewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-static" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="videoViewModalLabel">Visualisation de la vidéo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="videoPlayerContainer" class="text-center">
                        <video id="modalVideoPlayer" controls class="w-100 d-none" style="max-height: 70vh;"></video>
                        <iframe id="modalIframePlayer" class="w-100 d-none" style="max-height: 70vh;"
                            allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ===== GESTION DU FORMULAIRE D'AJOUT =====

            // Basculer entre fichier et lien pour l'ajout
            $('input[name="video_type"]', '#addVideoForm').change(function() {
                if ($(this).val() === 'file') {
                    $('#addVideoFileSection').removeClass('d-none');
                    $('#addVideoLinkSection').addClass('d-none');
                    $('#addVideoFichier').attr('required', 'required');
                    $('#addVideoLink').removeAttr('required');
                } else {
                    $('#addVideoFileSection').addClass('d-none');
                    $('#addVideoLinkSection').removeClass('d-none');
                    $('#addVideoFichier').removeAttr('required');
                    $('#addVideoLink').attr('required', 'required');
                }
            });

            // Gestion du fichier sélectionné pour l'ajout
            $('#addVideoFichier').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Réinitialiser le formulaire d'ajout à la fermeture
            $('#addVideoModal').on('hidden.bs.modal', function() {
                $('#addVideoForm')[0].reset();
                $('#addVideoFichier').next('.custom-file-label').html('Choisir un fichier');
                $('#addVideoTypeFile').prop('checked', true).trigger('change');
            });

            // ===== GESTION DU FORMULAIRE D'ÉDITION =====

            // Basculer entre fichier et lien pour l'édition
            $('input[name="video_type"]', '#editVideoForm').change(function() {
                if ($(this).val() === 'file') {
                    $('#editVideoFileSection').removeClass('d-none');
                    $('#editVideoLinkSection').addClass('d-none');
                    $('#editVideoLink').removeAttr('required');
                } else {
                    $('#editVideoFileSection').addClass('d-none');
                    $('#editVideoLinkSection').removeClass('d-none');
                    $('#editVideoLink').attr('required', 'required');
                }
            });

            // Gestion du fichier sélectionné pour l'édition
            $('#editVideoFichier').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                    'Choisir un nouveau fichier');
            });

            // Ouvrir le modal d'édition et charger les données
            $(document).on('click', '.edit-video-btn', function() {
                const videoId = $(this).data('video-id');

                // Charger les données de la vidéo via AJAX
                $.ajax({
                    url: "{{ route('videos.edit', ':id') }}".replace(':id', videoId),
                    method: 'GET',
                    success: function(data) {
                        // Remplir les champs
                        $('#editVideoNom').val(data.nom);
                        $('#editVideoDescription').val(data.description);

                        // Configurer l'action du formulaire
                        $('#editVideoForm').attr('action',
                            "{{ route('videos.update', ':id') }}".replace(':id', videoId));

                        // Déterminer le type et configurer les sections
                        if (data.media) {
                            if (data.media.type === 'link') {
                                // C'est un lien
                                $('#editVideoTypeLink').prop('checked', true);
                                $('#editVideoTypeLinkLabel').addClass('active');
                                $('#editVideoTypeFileLabel').removeClass('active');
                                $('#editVideoTypeLink').trigger('change');

                                $('#editVideoLink').val(data.media.url_fichier);
                                $('#editCurrentLinkValue').text(data.media.url_fichier);
                                $('#editViewCurrentLink').attr('href', data.media.url_fichier);
                                $('#editCurrentLink').show();
                                $('#editCurrentVideo').hide();
                            } else {
                                // C'est un fichier
                                $('#editVideoTypeFile').prop('checked', true);
                                $('#editVideoTypeFileLabel').addClass('active');
                                $('#editVideoTypeLinkLabel').removeClass('active');
                                $('#editVideoTypeFile').trigger('change');

                                const fileName = data.media.url_fichier.split('/').pop();
                                $('#editCurrentVideoName').text(fileName);
                                $('#editViewCurrentVideo').data('video-url', '/storage/' + data
                                    .media.url_fichier);
                                $('#editCurrentVideo').show();
                                $('#editCurrentLink').hide();
                            }
                        }

                        // Ouvrir le modal
                        $('#editVideoModal').modal('show');
                    },
                    error: function() {
                        alert('Erreur lors du chargement des données de la vidéo');
                    }
                });
            });

            // Visualisation de la vidéo actuelle dans le formulaire d'édition
            $(document).on('click', '#editViewCurrentVideo', function() {
                const videoUrl = $(this).data('video-url');
                $('#modalVideoPlayer').attr('src', videoUrl);
                $('#videoViewModal').modal('show');
            });

            // ===== GESTION COMMUNE =====

            // Mettre à jour le bouton d'ajout pour ouvrir le bon modal
            $('button[data-target="#videoFormModal"]').attr('data-target', '#addVideoModal');

            // Visualisation des vidéos dans la grille
            $(document).on('click', '.view-video-btn, .video-thumbnail', function() {
                const videoUrl = $(this).data('video-url');
                const videoName = $(this).data('video-name');

                // Vérifier si c’est un lien externe (YouTube/Vimeo)
                if (videoUrl.includes("youtube.com") || videoUrl.includes("vimeo.com")) {
                    $('#modalVideoPlayer').addClass('d-none').attr('src', '');
                    $('#modalIframePlayer').removeClass('d-none').attr('src', videoUrl);
                } else {
                    $('#modalIframePlayer').addClass('d-none').attr('src', '');
                    $('#modalVideoPlayer').removeClass('d-none').attr('src', videoUrl);
                }

                $('#videoViewModal .modal-title').text('Visualisation: ' + videoName);
                $('#videoViewModal').modal('show');
            });

            // Nettoyer la vidéo/iframe quand on ferme
            $('#videoViewModal').on('hidden.bs.modal', function() {
                $('#modalVideoPlayer').attr('src', '').addClass('d-none');
                $('#modalIframePlayer').attr('src', '').addClass('d-none');
            });


            // Lecture automatique quand la modal de visualisation s'ouvre
            $('#videoViewModal').on('shown.bs.modal', function() {
                $('#modalVideoPlayer')[0].play().catch(function(error) {
                    console.log('Lecture automatique bloquée:', error);
                });
            });

            // Arrêter la lecture quand la modal se ferme
            $('#videoViewModal').on('hidden.bs.modal', function() {
                const videoPlayer = $('#modalVideoPlayer')[0];
                videoPlayer.pause();
                videoPlayer.currentTime = 0;
            });

            // Amélioration de l'expérience mobile
            if ('ontouchstart' in document.documentElement) {
                $('.btn').addClass('touch-optimized');
                $('.modal').addClass('touch-modal');
            }
        });
    </script>
@endpush
