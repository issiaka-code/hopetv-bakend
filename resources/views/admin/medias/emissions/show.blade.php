@extends('admin.master')

@section('title', 'Détail de l\'émission')

@push('styles')
    <style>
        .emission-cover {
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .emission-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .emission-info {
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .emission-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .emission-description {
            font-size: 1.1rem;
            color: #5a6c7d;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .video-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .video-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .video-thumbnail-container {
            overflow: hidden;
            height: 200px;
            position: relative;
            background: #f8f9fa;
        }

        .video-thumbnail {
            cursor: pointer;
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .video-card:hover .video-thumbnail {
            transform: scale(1.08);
        }

        .thumbnail-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
            padding: 20px;
        }

        .video-card:hover .thumbnail-overlay {
            opacity: 1;
        }

        .play-icon {
            background: rgba(255, 255, 255, 0.9);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .play-icon i {
            font-size: 1.2rem;
            color: #e74c3c;
            margin-left: 3px;
        }

        .video-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .video-description {
            font-size: 0.9rem;
            color: #7f8c8d;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .video-actions {
            padding: 15px 20px;
            border-top: 1px solid #ecf0f1;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }

        .add-video-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .add-video-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .video-count-badge {
            background: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 font-weight-bold text-primary">Détails de l'émission</h1>
                    </div>
                    <div>
                        <button class="btn btn-primary add-video-btn" data-toggle="modal" data-target="#addEmissionVideoModal">
                            <i class="fa fa-plus-circle mr-2"></i> Ajouter une vidéo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section principale avec image de couverture et contenu -->
        <div class="row">
            <!-- Colonne de gauche - Image de couverture -->
            <div class="col-md-4 mb-4">
                <div class="emission-cover">
                    @if($emission->media && $emission->media->thumbnail)
                        <img src="{{ asset('storage/' . $emission->media->thumbnail) }}" 
                             alt="Couverture de l'émission {{ $emission->nom }}"
                             onerror="this.style.display='none'; this.parentNode.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                            <div class="text-center">
                                <i class="fas fa-film fa-4x mb-3"></i>
                                <p class="mb-0">Aucune image de couverture</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Informations de l'émission -->
                <div class="emission-info mt-4">
                    <h2 class="emission-title">{{ $emission->nom }}</h2>
                    <p class="emission-description">{{ $emission->description }}</p>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">
                            <i class="fas fa-video mr-2"></i>
                            {{ $emission->items->count() }} vidéo(s)
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-calendar mr-2"></i>
                            Créée le {{ $emission->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite - Liste des vidéos -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="section-title">
                        Vidéos de l'émission
                        <span class="video-count-badge ml-2">{{ $emission->items->count() }}</span>
                    </h3>
                </div>

                <div class="row">
                    @forelse($emission->items as $item)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card video-card">
                                <div class="video-thumbnail-container">
                                    @if (!empty($item->thumbnail))
                                        <img class="video-thumbnail" 
                                             src="{{ $item->thumbnail_url }}" 
                                             alt="Couverture vidéo {{ $item->titre_video }}">
                                    @elseif ($item->type_video === 'video' && $item->video_url)
                                        <video class="video-thumbnail" muted>
                                            <source src="{{ asset('storage/emissions/videos/' . $item->video_url) }}" type="video/mp4">
                                        </video>
                                    @elseif($item->type_video === 'link' && $item->video_url)
                                        @php
                                            $ytId = null;
                                            if (strpos($item->video_url, 'youtube.com') !== false || strpos($item->video_url, 'youtu.be') !== false) {
                                                $pattern = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/';
                                                if (preg_match($pattern, $item->video_url, $matches) && strlen($matches[7]) === 11) {
                                                    $ytId = $matches[7];
                                                }
                                            }
                                        @endphp
                                        @if($ytId)
                                            <img class="video-thumbnail" 
                                                 src="https://img.youtube.com/vi/{{ $ytId }}/hqdefault.jpg" 
                                                 alt="Couverture YouTube">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="fas fa-link fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-video-slash fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="thumbnail-overlay open-video" 
                                         data-video-url="{{ $item->type_video === 'video' ? asset('storage/emissions/videos/' . $item->video_url) : $item->video_url }}" 
                                         data-video-type="{{ $item->type_video }}" 
                                         data-description="{{ $item->description_video }}" 
                                         data-title="{{ $item->titre_video }}">
                                        <div>
                                            <div class="play-icon">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            <h6 class="text-white font-weight-bold mb-1">{{ $item->titre_video }}</h6>
                                            <p class="text-light mb-0 small">{{ Str::limit($item->description_video, 60) }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="video-actions">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-outline-primary btn-sm view-video-btn rounded-pill"
                                                data-video-url="{{ $item->type_video === 'video' ? asset('storage/emissions/videos/' . $item->video_url) : $item->video_url }}"
                                                data-video-type="{{ $item->type_video }}"
                                                data-description="{{ $item->description_video }}"
                                                data-title="{{ $item->titre_video }}">
                                            <i class="fa fa-eye mr-1"></i> Voir
                                        </button>
                                        
                                        <form action="{{ route('items.destroy', $item->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                                <i class="fa fa-trash mr-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-video-slash"></i>
                                <h4 class="text-muted">Aucune vidéo disponible</h4>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @include('admin.medias.emissions.modals.add_video')
    @include('admin.medias.emissions.modals.view_video')
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
                        $('input[name="type_video"]').change(function() {
                if ($(this).val() === 'upload') {
                    $('.video-upload-section').show();
                    $('.video-link-section').hide();
                    $('#video_file').prop('required', true);
                    $('#video_url').prop('required', false);
                    $('.thumbnail-section').show();
                } else {
                    $('.video-upload-section').hide();
                    $('.video-link-section').show();
                    $('#video_file').prop('required', false);
                    $('#video_url').prop('required', true);
                    $('.thumbnail-section').hide();
                }
            });

            // Gestion du changement de type de vidéo pour l'édition
            $('input[name="type_video"]').on('change', function() {
                if ($(this).val() === 'upload') {
                    $('.edit-video-upload-section').show();
                    $('.edit-video-link-section').hide();
                } else {
                    $('.edit-video-upload-section').hide();
                    $('.edit-video-link-section').show();
                }
            });

            // Gestion des labels de fichiers
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            

            // Bouton de visionnage de vidéo (via bouton et via overlay)
            $('.view-video-btn').click(function() {
                const videoUrl = $(this).data('video-url');
                const videoType = $(this).data('video-type');

                let videoHtml = '';

                if (videoType === 'video') {
                    videoHtml = `
                <video controls class="w-100" style="max-height: 70vh;">
                    <source src="${videoUrl}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            `;
                } else {
                    // Pour les liens YouTube
                    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                        const videoId = getYouTubeId(videoUrl);
                        videoHtml = `
                    <iframe width="100%" height="400" 
                        src="https://www.youtube.com/embed/${videoId}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                `;
                    } else {
                        // Fallback iframe générique (peut ne pas fonctionner pour toutes les plateformes)
                        videoHtml = `
                    <iframe width="100%" height="400" src="${videoUrl}" frameborder="0" allowfullscreen></iframe>
                `;
                    }
                }

                // Aligner avec le modal du module vidéos
                if (videoType === 'video') {
                    $('#iframePlayerContainer').addClass('d-none');
                    $('#modalIframePlayer').attr('src', '');
                    $('#videoPlayerContainer').removeClass('d-none');
                    // Activer l'autoplay (muted requis par les navigateurs)
                    $('#modalVideoPlayer')
                        .attr('src', videoUrl)
                        .prop('muted', true)
                        .attr('autoplay', true)[0].play().catch(() => {});
                    $('#mediaTypeBadge').text('Fichier vidéo');
                } else {
                    $('#videoPlayerContainer').addClass('d-none');
                    $('#modalVideoPlayer').attr('src', '');
                    $('#iframePlayerContainer').removeClass('d-none');
                    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                        const videoId = getYouTubeId(videoUrl);
                        if (videoId) {
                            // Autoplay + mute + modestbranding
                            $('#modalIframePlayer').attr('src', `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&rel=0&modestbranding=1`);
                        } else {
                            // Fallback à l'URL brute si extraction échoue
                            const sep = videoUrl.includes('?') ? '&' : '?';
                            $('#modalIframePlayer').attr('src', `${videoUrl}${sep}autoplay=1&mute=1`);
                        }
                        $('#mediaTypeBadge').text('Vidéo en ligne');
                    } else {
                        // Tentative d'autoplay pour iframe générique (peut être limité par la plateforme)
                        const sep = videoUrl.includes('?') ? '&' : '?';
                        $('#modalIframePlayer').attr('src', `${videoUrl}${sep}autoplay=1`);
                        $('#mediaTypeBadge').text('Vidéo en ligne');
                    }
                }
                $('#videoTitle').text($(this).data('title') || $(this).closest('.card').find('.card-title').text());
                $('#videoDescription').text($(this).data('description') || $(this).closest('.card').find('.card-text').text());
                $('#videoViewModal').modal('show');
            });

            $('.open-video').click(function() {
                const videoUrl = $(this).data('video-url');
                const videoType = $(this).data('video-type');

                if (videoType === 'video') {
                    $('#iframePlayerContainer').addClass('d-none');
                    $('#modalIframePlayer').attr('src', '');
                    $('#videoPlayerContainer').removeClass('d-none');
                    $('#modalVideoPlayer')
                        .attr('src', videoUrl)
                        .prop('muted', true)
                        .attr('autoplay', true)[0].play().catch(() => {});
                    $('#mediaTypeBadge').text('Fichier vidéo');
                } else {
                    $('#videoPlayerContainer').addClass('d-none');
                    $('#modalVideoPlayer').attr('src', '');
                    $('#iframePlayerContainer').removeClass('d-none');
                    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                        const videoId = getYouTubeId(videoUrl);
                        if (videoId) {
                            $('#modalIframePlayer').attr('src', `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&rel=0&modestbranding=1`);
                        } else {
                            const sep = videoUrl.includes('?') ? '&' : '?';
                            $('#modalIframePlayer').attr('src', `${videoUrl}${sep}autoplay=1&mute=1`);
                        }
                        $('#mediaTypeBadge').text('Vidéo en ligne');
                    } else {
                        const sep = videoUrl.includes('?') ? '&' : '?';
                        $('#modalIframePlayer').attr('src', `${videoUrl}${sep}autoplay=1`);
                        $('#mediaTypeBadge').text('Vidéo en ligne');
                    }
                }
                const $card = $(this).closest('.card');
                $('#videoTitle').text($(this).data('title') || $card.find('.card-title').text());
                $('#videoDescription').text($(this).data('description') || $card.find('.card-text').text());
                $('#videoViewModal').modal('show');
            });

            // Nettoyage/pause à la fermeture du modal
            $('#videoViewModal').on('hidden.bs.modal', function() {
                const $video = $('#modalVideoPlayer');
                try { $video[0].pause(); } catch(e) {}
                $video.attr('src', '');
                $('#modalIframePlayer').attr('src', '');
            });

            // Fonction robuste pour extraire l'ID d'une vidéo YouTube (watch, youtu.be, embed, shorts)
            function getYouTubeId(url) {
                try {
                    const u = new URL(url);
                    const host = u.hostname.replace('www.', '');
                    // Cas standard: ?v=ID
                    if ((host === 'youtube.com' || host === 'm.youtube.com' || host === 'youtu.be' || host === 'youtube-nocookie.com')) {
                        if (u.searchParams.has('v') && u.searchParams.get('v').length === 11) {
                            return u.searchParams.get('v');
                        }
                        // youtu.be/ID
                        const parts = u.pathname.split('/').filter(Boolean);
                        if (host === 'youtu.be' && parts.length >= 1 && parts[0].length === 11) {
                            return parts[0];
                        }
                        // /embed/ID
                        const embedIdx = parts.indexOf('embed');
                        if (embedIdx !== -1 && parts[embedIdx + 1] && parts[embedIdx + 1].length === 11) {
                            return parts[embedIdx + 1];
                        }
                        // /shorts/ID
                        const shortsIdx = parts.indexOf('shorts');
                        if (shortsIdx !== -1 && parts[shortsIdx + 1] && parts[shortsIdx + 1].length === 11) {
                            return parts[shortsIdx + 1];
                        }
                    }
                } catch (e) {
                    // ignore
                }
                // Regex fallback
                const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(shorts\/)|(watch\?))\??v?=?([^#&?]*).*/;
                const match = (url || '').match(regExp);
                return (match && match[8] && match[8].length === 11) ? match[8] : false;
            }

            // Gestion de la soumission du formulaire d'ajout
            $('#addVideoForm').submit(function(e) {
                const typeVideo = $('input[name="type_video"]:checked').val();

                if (typeVideo === 'upload' && !$('#video_file').val()) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un fichier vidéo');
                    return false;
                }

                if (typeVideo === 'link' && !$('#video_url').val()) {
                    e.preventDefault();
                    alert('Veuillez saisir un lien vidéo');
                    return false;
                }
            });

            // Nettoyer le modal de visionnage quand il se ferme
            $('#viewVideoModal').on('hidden.bs.modal', function() {
                $('#videoPlayer').html('');
            });

            // Réinitialiser le formulaire d'ajout quand le modal se ferme
            $('#addEmissionVideoModal').on('hidden.bs.modal', function() {
                $('#addVideoForm')[0].reset();
                $('.custom-file-label').html('Choisir un fichier...');
                $('.video-upload-section').show();
                $('.video-link-section').hide();
                $('#video_file').prop('required', true);
                $('#video_url').prop('required', false);
                $('.thumbnail-section').show();
            });
        });
    </script>
@endpush
