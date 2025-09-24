@extends('admin.master')

@section('title', 'Créer une Programmation - À venir')

@push('styles')
    <style>
        .section-title {
            font-size: 1.5rem;
            color: #4e73df;
            margin-bottom: 0;
        }

        .wizard {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .wizard .steps {
            background: #f8f9fa;
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .wizard .steps ul {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .wizard .steps li {
            position: relative;
            margin: 0 1rem;
            text-align: center;
            min-width: 120px;
        }

        .wizard .steps li:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -1rem;
            width: 2rem;
            height: 2px;
            background: #dee2e6;
            transform: translateY(-50%);
        }

        .wizard .steps li.done::after {
            background: #28a745;
        }

        .wizard .steps li a {
            display: block;
            color: #6c757d;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .wizard .steps li.current a,
        .wizard .steps li.done a {
            color: #495057;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .wizard .steps li.current a {
            color: #4e73df;
            font-weight: bold;
        }

        .wizard .steps .number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            background: #dee2e6;
            color: white;
            border-radius: 50%;
            margin-right: 0.5rem;
            font-weight: bold;
        }

        .wizard .steps li.current .number,
        .wizard .steps li.done .number {
            background: #4e73df;
        }

        .wizard .content {
            padding: 2rem;
            min-height: 400px;
        }

        .wizard .actions {
            background: #f8f9fa;
            padding: 1rem 2rem;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
        }

        .video-selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .video-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .video-card:hover {
            border-color: #4e73df;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .video-card.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }

        .video-card .selection-order {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .selected-videos-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .selected-video-item {
            background: white;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: move;
            border: 1px solid #e9ecef;
        }

        .selected-video-item:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .drag-handle {
            color: #6c757d;
            margin-right: 1rem;
            cursor: move;
        }

        .video-info {
            flex: 1;
        }

        .video-title {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .video-duration {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .remove-video {
            color: #dc3545;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .remove-video:hover {
            color: #c82333;
        }

        .playlist-preview {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
        }

        .playlist-info {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #4e73df;
        }

        .playlist-videos {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
        }

        .preview-video-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .preview-video-item:last-child {
            border-bottom: none;
        }

        .video-order {
            background: #4e73df;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .wizard .steps ul {
                flex-direction: column;
                align-items: center;
            }

            .wizard .steps li {
                margin: 0.25rem 0;
                min-width: auto;
            }

            .wizard .steps li:not(:last-child)::after {
                display: none;
            }

            .video-selection-grid {
                grid-template-columns: 1fr;
            }

            .wizard .content {
                padding: 1rem;
            }

            .wizard .actions {
                padding: 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        .hidden {
            display: none !important;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .test-playlist-btn {
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .test-playlist-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .video-thumbnail {
            width: 60px;
            height: 40px;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            margin-right: 10px;
            transition: transform 0.2s;
        }

        .video-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="section-title">Créer une programmation "À venir"</h2>
                        <a href="{{ route('a-venir.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Assistant de création - À venir</h4>
                        </div>
                        <div class="card-body">
                            <div id="wizard_avenir" role="application" class="wizard clearfix">
                                <div class="steps clearfix">
                                    <ul role="tablist">
                                        <li role="tab" class="first current" aria-disabled="false" aria-selected="true">
                                            <a href="#step-1" aria-controls="step-1">
                                                <span class="number">1.</span> Informations
                                            </a>
                                        </li>
                                        <li role="tab" aria-disabled="true" aria-selected="false">
                                            <a href="#step-2" aria-controls="step-2">
                                                <span class="number">2.</span> Sélection vidéos (non publiées)
                                            </a>
                                        </li>
                                        <li role="tab" aria-disabled="true" aria-selected="false">
                                            <a href="#step-3" aria-controls="step-3">
                                                <span class="number">3.</span> Organisation
                                            </a>
                                        </li>
                                        <li role="tab" class="last" aria-disabled="true" aria-selected="false">
                                            <a href="#step-4" aria-controls="step-4">
                                                <span class="number">4.</span> Confirmation
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <form id="avenirForm" action="{{ route('a-venir.store') }}" method="POST">
                                    @csrf
                                    <div class="content clearfix">
                                        <section id="step-1" class="body current" aria-hidden="false">
                                            <h3 class="mb-4">Informations de la programmation</h3>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="nom">Nom *</label>
                                                        <input type="text" class="form-control" id="nom"
                                                            name="nom" placeholder="Ex: À venir - Matinée" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control" id="description" name="description" rows="4"
                                                            placeholder="Décrivez le contenu ou l'objectif de cette programmation"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="etat">Etat</label>
                                                        <select class="form-control" id="etat" name="etat">
                                                            <option value="1">Actif</option>
                                                            <option value="0">Inactif</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="date_debut">Date de début *</label>
                                                        <input type="datetime-local" class="form-control" id="date_debut"
                                                            name="date_debut" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-info">
                                                        <h6><i class="fas fa-info-circle"></i> Informations</h6>
                                                        <p class="mb-2">Définissez les paramètres de votre programmation
                                                            "À venir".</p>
                                                        <small class="text-dark">Les vidéos disponibles sont uniquement
                                                            celles non publiées.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section id="step-2" class="body hidden" aria-hidden="true">
                                            <h3 class="mb-4">Sélection des vidéos (non publiées)</h3>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <input type="text" id="searchVideos" class="form-control"
                                                        placeholder="Rechercher une vidéo...">
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <span class="badge badge-primary" id="selectedCount">0 vidéo(s)
                                                        sélectionnée(s)</span>
                                                </div>
                                            </div>
                                            <div class="video-selection-grid" id="videosGrid">
                                                @foreach ($videos as $video)
                                                    <div class="video-card" data-video-id="{{ $video->id }}"
                                                        data-video-title="{{ $video->nom }}"
                                                        data-video-duration="{{ $video->duree ?? '00:00:00' }}"
                                                        data-video-url="{{ asset('storage/' . $video->media->url_fichier ?? '') }}">
                                                        <video controls controlsList="nodownload">
                                                            <source
                                                                src="{{ asset('storage/' . $video->media->url_fichier ?? '') }}"
                                                                type="video/mp4">
                                                            Votre navigateur ne supporte pas la lecture vidéo.
                                                        </video>
                                                        <div class="selection-order hidden"></div>
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-play-circle text-primary mr-2"></i>
                                                            <h6 class="mb-0">{{ Str::limit($video->nom, 30) }}</h6>
                                                        </div>
                                                        <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                                            {{ Str::limit($video->description ?? 'Aucune description', 50) }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted"><i class="fas fa-clock"></i>
                                                                {{ $video->duree ?? 'N/A' }}</small>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input video-checkbox"
                                                                    id="video_{{ $video->id }}"
                                                                    value="{{ $video->id }}">
                                                                <label class="custom-control-label"
                                                                    for="video_{{ $video->id }}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </section>

                                        <section id="step-3" class="body hidden" aria-hidden="true">
                                            <h3 class="mb-4">Organisation des vidéos</h3>
                                            <div class="alert alert-info"><i class="fas fa-info-circle"></i>
                                                Glissez-déposez les vidéos pour définir l'ordre de lecture.</div>
                                            <div class="selected-videos-list">
                                                <h5 class="mb-3">Vidéos sélectionnées</h5>
                                                <div id="sortableVideosList" class="sortable-list"></div>
                                                <div id="noVideosSelected" class="text-center py-4 text-muted">
                                                    <i class="fas fa-video fa-3x mb-3"></i>
                                                    <p>Aucune vidéo sélectionnée. Retournez à l'étape précédente.</p>
                                                </div>
                                            </div>
                                        </section>

                                        <section id="step-4" class="body hidden" aria-hidden="true">
                                            <h3 class="mb-4">Confirmation et aperçu</h3>
                                            <div class="playlist-preview">
                                                <div class="playlist-info">
                                                    <h4 class="text-primary mb-3"><i class="fas fa-list-alt"></i>
                                                        Informations</h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Nom:</strong> <span id="preview-nom"></span></p>
                                                            <p><strong>Date de début:</strong> <span
                                                                    id="preview-date"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Nombre de vidéos:</strong> <span
                                                                    id="preview-count"></span></p>
                                                        </div>
                                                    </div>
                                                    <p><strong>Description:</strong> <span id="preview-description"></span>
                                                    </p>
                                                </div>
                                                <div class="playlist-videos">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0"><i class="fas fa-play"></i> Ordre de lecture
                                                        </h5>
                                                        <button type="button"
                                                            class="btn btn-info bg-info test-playlist-btn"
                                                            id="testPlaylistBtn">
                                                            <i class="fas fa-play"></i> Prévisualiser
                                                        </button>
                                                    </div>
                                                    <div id="previewVideosList"></div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="selected_videos" id="selectedVideosInput">
                                        </section>
                                    </div>
                                    <div class="actions clearfix">
                                        <button type="button" id="prevBtn" class="btn btn-outline-secondary hidden">
                                            <i class="fas fa-arrow-left"></i> Précédent
                                        </button>
                                        <div>
                                            <button type="button" id="nextBtn" class="btn btn-primary">
                                                Suivant <i class="fas fa-arrow-right"></i>
                                            </button>
                                            <button type="submit" id="finishBtn"
                                                class="btn btn-success bg-success hidden">
                                                <i class="fas fa-check"></i> Créer la programmation
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade video-player-modal" id="videoPlayerModal" tabindex="-1" role="dialog"
        aria-labelledby="videoPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoPlayerModalLabel"><i class="fas fa-play-circle"></i>
                        Prévisualisation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="video-player-container">
                                <video id="modalVideoPlayer" class="p-4" controls controlsList="nodownload">
                                    <source src="" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture vidéo.
                                </video>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="video-playlist-sidebar">
                                <h6 class="mb-3"><i class="fas fa-list"></i> Playlist <span
                                        class="badge badge-primary ml-2" id="modalPlaylistCount">0</span></h6>
                                <div id="modalPlaylistItems"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-secondary" data-dismiss="modal"><i
                            class="fas fa-times"></i> Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentStep = 1;
            let selectedVideos = [];
            let sortable;
            let currentVideoIndex = 0;

            function showStep(step) {
                $('.wizard .body').addClass('hidden').attr('aria-hidden', 'true');
                $(`#step-${step}`).removeClass('hidden').attr('aria-hidden', 'false');
                $('.wizard .steps li').removeClass('current done');
                for (let i = 1; i <= 4; i++) {
                    const $li = $(`.wizard .steps li:nth-child(${i})`);
                    if (i < step) {
                        $li.addClass('done');
                    } else if (i === step) {
                        $li.addClass('current');
                    }
                }
                $('#prevBtn').toggleClass('hidden', step === 1);
                if (step === 4) {
                    $('#nextBtn').addClass('hidden');
                    $('#finishBtn').removeClass('hidden');
                    updatePreview();
                } else {
                    $('#nextBtn').removeClass('hidden');
                    $('#finishBtn').addClass('hidden');
                }
                currentStep = step;
            }

            function validateStep(step) {
                if (step === 1) {
                    const nom = $('#nom').val().trim();
                    const dateDebut = $('#date_debut').val();
                    if (!nom || !dateDebut) {
                        alert('Veuillez remplir tous les champs obligatoires.');
                        return false;
                    }
                } else if (step === 2) {
                    if (selectedVideos.length === 0) {
                        alert('Veuillez sélectionner au moins une vidéo.');
                        return false;
                    }
                }
                return true;
            }

            $('#nextBtn').click(function() {
                if (validateStep(currentStep) && currentStep < 4) {
                    if (currentStep === 2) {
                        updateSortableList();
                    }
                    showStep(currentStep + 1);
                }
            });
            $('#prevBtn').click(function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });

            $(document).on('change', '.video-checkbox', function() {
                const videoId = $(this).val();
                const videoCard = $(this).closest('.video-card');
                const videoTitle = videoCard.data('video-title');
                const videoDuration = videoCard.data('video-duration');
                const videoUrl = videoCard.data('video-url');
                if ($(this).is(':checked')) {
                    selectedVideos.push({
                        id: videoId,
                        title: videoTitle,
                        duration: videoDuration,
                        url: videoUrl,
                        order: selectedVideos.length + 1
                    });
                    videoCard.addClass('selected');
                } else {
                    selectedVideos = selectedVideos.filter(v => v.id !== videoId);
                    videoCard.removeClass('selected');
                }
                updateSelectionOrder();
                updateSelectedCount();
            });

            $('#searchVideos').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.video-card').each(function() {
                    const title = $(this).data('video-title').toLowerCase();
                    $(this).toggle(title.includes(searchTerm));
                });
            });

            function updateSelectionOrder() {
                $('.video-card .selection-order').addClass('hidden');
                selectedVideos.forEach((video, index) => {
                    video.order = index + 1;
                });
                selectedVideos.forEach((video) => {
                    const $card = $(`.video-card[data-video-id="${video.id}"]`);
                    const $orderBadge = $card.find('.selection-order');
                    $orderBadge.text(video.order).removeClass('hidden');
                });
            }

            function updateSelectedCount() {
                $('#selectedCount').text(`${selectedVideos.length} vidéo(s) sélectionnée(s)`);
            }

            function updateSortableList() {
                const $list = $('#sortableVideosList');
                $list.empty();
                if (selectedVideos.length === 0) {
                    $('#noVideosSelected').show();
                    return;
                }
                $('#noVideosSelected').hide();
                selectedVideos.forEach((video, index) => {
                    const item = `
                        <div class="selected-video-item" data-video-id="${video.id}">
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <div class="video-info d-flex flex-row align-items-center">
                                <div class="video-preview-container" style="position: relative; margin-right: 15px;">
                                    <video class="video-thumbnail" style="width: 100px; height: 100px;" muted>
                                        <source src="${video.url}" type="video/mp4">
                                    </video>
                                    <div class="play-overlay" data-video-url="${video.url}" data-video-title="${video.title}" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; cursor: pointer; border-radius: 4px;">
                                        <i class="fas fa-play text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="video-title">${video.title}</div>
                                    <div class="video-duration"><i class="fas fa-clock"></i> ${video.duration}</div>
                                </div>
                            </div>
                            <div class="video-order-number"><span class="badge badge-primary">#${index + 1}</span></div>
                            <button type="button" class="remove-video" data-video-id="${video.id}"><i class="fas fa-times"></i></button>
                        </div>`;
                    $list.append(item);
                });
                if (sortable) {
                    sortable.destroy();
                }
                sortable = Sortable.create(document.getElementById('sortableVideosList'), {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        updateVideoOrder();
                    }
                });
                $('.video-thumbnail').on('mouseenter', function() {
                    this.play().catch(() => {});
                });
                $('.video-thumbnail').on('mouseleave', function() {
                    this.pause();
                    this.currentTime = 0;
                });
                $('.video-preview-container').on('mouseenter', function() {
                    $(this).find('.play-overlay').css('opacity', '1');
                });
                $('.video-preview-container').on('mouseleave', function() {
                    $(this).find('.play-overlay').css('opacity', '0');
                });
            }

            $(document).on('click', '.remove-video', function() {
                const videoId = $(this).data('video-id');
                selectedVideos = selectedVideos.filter(v => v.id != videoId);
                $(`.video-checkbox[value="${videoId}"]`).prop('checked', false);
                $(`.video-card[data-video-id="${videoId}"]`).removeClass('selected');
                updateSortableList();
                updateSelectionOrder();
                updateSelectedCount();
            });

            function updateVideoOrder() {
                const newOrder = [];
                $('#sortableVideosList .selected-video-item').each(function(index) {
                    const videoId = $(this).data('video-id');
                    const video = selectedVideos.find(v => v.id == videoId);
                    if (video) {
                        video.order = index + 1;
                        newOrder.push(video);
                        $(this).find('.video-order-number .badge').text(`#${index + 1}`);
                    }
                });
                selectedVideos = newOrder;
                updateSelectionOrder();
            }

            function updatePreview() {
                $('#preview-nom').text($('#nom').val());
                $('#preview-description').text($('#description').val() || 'Aucune description');
                $('#preview-date').text(formatDateTime($('#date_debut').val()));
                $('#preview-count').text(selectedVideos.length);
                const $previewList = $('#previewVideosList');
                $previewList.empty();
                selectedVideos.forEach((video, index) => {
                    const item =
                        `<div class="preview-video-item"><div class="video-order">${index + 1}</div><div class="video-info"><div class="video-title">${video.title}</div><div class="video-duration"><i class="fas fa-clock"></i> ${video.duration}</div></div></div>`;
                    $previewList.append(item);
                });
                $('#selectedVideosInput').val(JSON.stringify(selectedVideos));
            }

            $('#testPlaylistBtn').click(function() {
                if (selectedVideos.length === 0) {
                    alert('Aucune vidéo à prévisualiser.');
                    return;
                }
                showVideoModal();
            });

            function showVideoModal() {
                currentVideoIndex = 0;
                updateModalPlaylist();
                loadVideoInModal(currentVideoIndex);
                $('#videoPlayerModal').modal('show');
            }

            function updateModalPlaylist() {
                const $container = $('#modalPlaylistItems');
                $container.empty();
                $('#modalPlaylistCount').text(selectedVideos.length);
                selectedVideos.forEach((video, index) => {
                    const item =
                        `<div class="playlist-video-item ${index === currentVideoIndex ? 'active' : ''}" data-index="${index}"><div class="video-number">${index + 1}</div><div class="playlist-video-info"><div class="playlist-video-title">${video.title}</div><div class="playlist-video-duration"><i class="fas fa-clock"></i> ${video.duration}</div></div></div>`;
                    $container.append(item);
                });
            }

            function loadVideoInModal(index) {
                if (index < 0 || index >= selectedVideos.length) return;
                currentVideoIndex = index;
                const video = selectedVideos[index];
                const $player = $('#modalVideoPlayer');
                $player.find('source').attr('src', video.url);
                $player[0].load();
                $player[0].play();
                $('#videoPlayerModalLabel').html(
                    `<i class="fas fa-play-circle"></i> ${video.title} (${index + 1}/${selectedVideos.length})`);
                $('.playlist-video-item').removeClass('active');
                $(`.playlist-video-item[data-index="${index}"]`).addClass('active');
                const $activeItem = $(`.playlist-video-item[data-index="${index}"]`);
                if ($activeItem.length) {
                    $activeItem[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
            $('#modalVideoPlayer').on('ended', function() {
                if (currentVideoIndex < selectedVideos.length - 1) {
                    loadVideoInModal(currentVideoIndex + 1);
                } else {
                    $('#videoPlayerModal').modal('hide');
                }
            });
            $(document).on('click', '.playlist-video-item', function() {
                const index = parseInt($(this).data('index'));
                loadVideoInModal(index);
            });
            $('#videoPlayerModal').on('hidden.bs.modal', function() {
                const $player = $('#modalVideoPlayer');
                $player[0].pause();
                $player[0].currentTime = 0;
            });

            function formatDateTime(dateTimeString) {
                if (!dateTimeString) return '';
                const date = new Date(dateTimeString);
                return date.toLocaleString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            $('#avenirForm').submit(function(e) {
                if (selectedVideos.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins une vidéo.');
                    return false;
                }
                $('#selectedVideosInput').val(JSON.stringify(selectedVideos));
                return true;
            });
            showStep(1);
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            $('#date_debut').val(now.toISOString().slice(0, 16));
        });
    </script>
@endpush
