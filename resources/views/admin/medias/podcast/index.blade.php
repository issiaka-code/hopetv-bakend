@extends('admin.master')

@section('title', isset($video) ? 'Modifier Vidéo' : 'Gestion des Vidéos')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="text-white">
                                {{ isset($video) ? 'Modifier Vidéo' : 'Ajouter des Vidéos' }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (isset($video))
                                <!-- Formulaire d'édition -->
                                <form method="POST" action="{{ route('videos.update', $video->id) }}" enctype="multipart/form-data" id="edit-form">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                               value="{{ old('nom', $video->nom) }}" required>
                                        @error('nom')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                                  rows="3" required>{{ old('description', $video->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Fichier Vidéo</label>
                                        @if($video->media && $video->media->url_fichier)
                                            <div class="mb-2">
                                                <small>Fichier actuel: {{ basename($video->media->url_fichier) }}</small>
                                                <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#videoModal" data-video-url="{{ asset('storage/' . $video->media->url_fichier) }}">
                                                    <i class="fa fa-eye"></i> Voir
                                                </button>
                                            </div>
                                        @endif
                                        <input type="file" name="fichier_video" class="form-control @error('fichier_video') is-invalid @enderror" accept="video/*">
                                        @error('fichier_video')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <small class="text-muted">Laissez vide pour conserver le fichier actuel</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Média associé <span class="text-danger">*</span></label>
                                        <select name="id_media" class="form-control @error('id_media') is-invalid @enderror" required>
                                            <option value="">Sélectionner un média</option>
                                            @foreach($medias as $media)
                                                <option value="{{ $media->id }}" 
                                                        {{ (old('id_media', $video->id_media) == $media->id) ? 'selected' : '' }}>
                                                    {{ $media->nom }} ({{ $media->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_media')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block btn-submit">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                </form>
                            @else
                                <!-- Formulaire de création multiple -->
                                <form method="POST" action="{{ route('videos.store') }}" id="video-form" enctype="multipart/form-data">
                                    @csrf
                                    <div id="videos-container" class="scrollable-content">
                                        <!-- Première vidéo -->
                                        <div class="video-group" data-index="0">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Nom <span class="text-danger">*</span></label>
                                                <input type="text" name="videos[0][nom]" 
                                                       class="form-control @error('videos.0.nom') is-invalid @enderror" 
                                                       placeholder="Nom de la vidéo" 
                                                       value="{{ old('videos.0.nom') }}" required>
                                                @error('videos.0.nom')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                                <textarea name="videos[0][description]" 
                                                          class="form-control @error('videos.0.description') is-invalid @enderror" 
                                                          rows="2" placeholder="Description de la vidéo" required>{{ old('videos.0.description') }}</textarea>
                                                @error('videos.0.description')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Fichier Vidéo <span class="text-danger">*</span></label>
                                                <input type="file" name="videos[0][fichier_video]" 
                                                       class="form-control @error('videos.0.fichier_video') is-invalid @enderror" 
                                                       accept="video/*" required>
                                                @error('videos.0.fichier_video')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <button type="button" class="btn btn-danger btn-sm mb-1 remove-video">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                        <!-- Fin première vidéo -->
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="add-video" class="btn btn-light btn-sm m-1">
                                            <i class="fas fa-plus-circle"></i> Ajouter plus
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg m-1">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                            <h4 class="text-white">Liste des Vidéos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="videos-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Créée le</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($videos as $video)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $video->nom }}</td>
                                                <td class="scrollable-{{ $video->id }}" data-full-text="{{ $video->description }}">
                                                    {{ Str::limit($video->description, 30) }}
                                                </td>
                                                <td>{{ $video->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-primary mx-1">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('videos.destroy', $video->id) }}" method="POST" class="delete-form">
                                                            @csrf
                                                            @method('DELETE') 
                                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')" class="btn btn-danger mx-1">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @if($video->media && $video->media->url_fichier)
                                                            <button type="button" class="btn btn-info mx-1 view-video-btn" 
                                                                    data-video-url="{{ asset('storage/' . $video->media->url_fichier) }}"
                                                                    data-video-name="{{ $video->nom }}">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucune vidéo disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal pour visualiser la vidéo -->
    <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-static" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="videoModalLabel">Visualisation de la vidéo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <video id="modalVideoPlayer" controls class="w-100" style="max-height: 70vh;">
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .scrollable-content {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 15px;
        }

        .video-group {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .badge {
            font-size: 0.8em;
        }

        /* Styles pour les messages d'erreur */
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .invalid-feedback {
            font-size: 0.875em;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        /* Animation pour les alertes */
        .alert {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Style pour le spinner de loading */
        .btn-loading {
            position: relative;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: button-loading-spinner 1s ease infinite;
        }

        @keyframes button-loading-spinner {
            from {
                transform: rotate(0turn);
            }
            to {
                transform: rotate(1turn);
            }
        }

        /* Style pour la modal */
        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        video {
            border-radius: 8px;
            background-color: #000;
        }
    </style>
@endpush

@push('scripts')
    <!-- SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Gestion du hover sur les descriptions
            $('td[class^="scrollable-"]').hover(
                function() {
                    let fullText = $(this).data('full-text');
                    $(this).data('short-text', $(this).text());
                    $(this).text(fullText);
                    $(this).parent('tr').css('height', 'auto');
                },
                function() {
                    let shortText = $(this).data('short-text');
                    $(this).text(shortText);
                    $(this).parent('tr').css('height', '');
                }
            );
        });
    </script>

    <script>
        // Gestion de la modal vidéo
        $('#videoModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const videoUrl = button.data('video-url');
            const videoName = button.data('video-name') || 'Vidéo';
            
            const modal = $(this);
            modal.find('.modal-title').text('Visualisation');
            
            const videoPlayer = modal.find('#modalVideoPlayer');
            videoPlayer.attr('src', videoUrl);
            
            // Lecture automatique quand la modal s'ouvre
            videoPlayer[0].play().catch(error => {
                console.log('Lecture automatique bloquée:', error);
            });
        });

        // Arrêter la lecture quand la modal se ferme
        $('#videoModal').on('hidden.bs.modal', function () {
            const videoPlayer = $('#modalVideoPlayer');
            videoPlayer[0].pause();
            videoPlayer[0].currentTime = 0;
            videoPlayer.attr('src', '');
        });

        // Gestion des boutons de visualisation dans le tableau
        document.querySelectorAll('.view-video-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const videoUrl = this.getAttribute('data-video-url');
                const videoName = this.getAttribute('data-video-name');
                
                // Mettre à jour la modal avec les données de la vidéo
                document.getElementById('videoModalLabel').textContent = 'Visualisation: ' + videoName;
                const videoPlayer = document.getElementById('modalVideoPlayer');
                videoPlayer.src = videoUrl;
                
                // Ouvrir la modal
                $('#videoModal').modal('show');
                
                // Lecture automatique après un petit délai pour laisser la modal s'ouvrir
                setTimeout(() => {
                    videoPlayer.play().catch(error => {
                        console.log('Lecture automatique bloquée:', error);
                        // Afficher un message si la lecture automatique est bloquée
                        if (error.name === 'NotAllowedError') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Lecture manuelle requise',
                                text: 'Veuillez cliquer sur le bouton play pour démarrer la vidéo.',
                                timer: 3000
                            });
                        }
                    });
                }, 500);
            });
        });

        // Gestion création multiple
        let videoCount = 1;

        document.getElementById('add-video')?.addEventListener('click', function() {
            const container = document.getElementById('videos-container');
            const template = document.querySelector('.video-group').cloneNode(true);
            const newIndex = videoCount++;

            template.setAttribute('data-index', newIndex);
            
            // Mise à jour des noms et nettoyage des valeurs
            template.querySelectorAll('[name]').forEach(el => {
                const name = el.getAttribute('name').replace(/\[0\]/g, `[${newIndex}]`);
                el.setAttribute('name', name);
                
                // Nettoyer les erreurs de validation
                el.classList.remove('is-invalid');
                
                // Nettoyer les valeurs
                if (el.type !== 'hidden' && el.type !== 'file') {
                    el.value = '';
                }
                if (el.type === 'file') {
                    el.value = null;
                }
            });

            // Supprimer les messages d'erreur existants
            template.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Ajouter l'event listener pour le bouton supprimer
            template.querySelector('.remove-video').addEventListener('click', function() {
                if (document.querySelectorAll('.video-group').length > 1) {
                    this.closest('.video-group').remove();
                }
            });

            container.appendChild(template);
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners pour les boutons supprimer
            document.querySelectorAll('.remove-video').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (document.querySelectorAll('.video-group').length > 1) {
                        this.closest('.video-group').remove();
                    }
                });
            });

            // Validation des fichiers avant soumission
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const fileInputs = this.querySelectorAll('input[type="file"]');
                    let hasError = false;

                    fileInputs.forEach(input => {
                        if (input.files.length > 0) {
                            const file = input.files[0];
                            const maxSize = 100 * 1024 * 1024; // 100MB
                            
                            // Vérifier la taille du fichier
                            if (file.size > maxSize) {
                                showError('Le fichier ' + file.name + ' est trop volumineux (max 100MB)');
                                hasError = true;
                            }
                            
                            // Vérifier le type de fichier
                            if (!file.type.startsWith('video/')) {
                                showError('Le fichier ' + file.name + ' n\'est pas une vidéo valide');
                                hasError = true;
                            }
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }

                    // Ajouter un indicateur de chargement sur le bouton de soumission
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('btn-loading');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement...';
                        
                        // Restaurer le bouton après 30 secondes (timeout de sécurité)
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-loading');
                            submitBtn.innerHTML = originalText;
                        }, 30000);
                    }
                });
            });

            // Validation en temps réel
            document.querySelectorAll('input[required], textarea[required], select[required]').forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Ce champ est obligatoire');
                    } else {
                        this.classList.remove('is-invalid');
                        removeFieldError(this);
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                        removeFieldError(this);
                    }
                });
            });
        });

        // Fonction pour afficher les erreurs
        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Erreur!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            `;
            
            const container = document.querySelector('.section-body');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-hide après 5 secondes
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Fonction pour afficher les erreurs de champ
        function showFieldError(field, message) {
            removeFieldError(field);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        // Fonction pour supprimer les erreurs de champ
        function removeFieldError(field) {
            const existingError = field.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
        }
    </script>
@endpush