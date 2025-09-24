@extends('admin.master')

@section('title', 'Modifier Programmation - À venir')

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

        .hidden {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <section class="section" style="margin-top: -25px;">
        <div class="section-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
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
                        <h2 class="section-title">Modifier la programmation "À venir"</h2>
                        <a href="{{ route('a-venir.index') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left"></i> Retour</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Assistant de modification - À venir</h4>
                        </div>
                        <div class="card-body">
                            <div id="wizard_avenir" role="application" class="wizard clearfix">
                                <div class="steps clearfix">
                                    <ul role="tablist">
                                        <li role="tab" class="first current" aria-disabled="false" aria-selected="true">
                                            <a href="#step-1" aria-controls="step-1"><span class="number">1.</span>
                                                Informations</a></li>
                                        <li role="tab" aria-disabled="true" aria-selected="false"><a href="#step-2"
                                                aria-controls="step-2"><span class="number">2.</span> Sélection vidéos (non
                                                publiées)</a></li>
                                        <li role="tab" aria-disabled="true" aria-selected="false"><a href="#step-3"
                                                aria-controls="step-3"><span class="number">3.</span> Organisation</a></li>
                                        <li role="tab" class="last" aria-disabled="true" aria-selected="false"><a
                                                href="#step-4" aria-controls="step-4"><span class="number">4.</span>
                                                Confirmation</a></li>
                                    </ul>
                                </div>

                                <form id="avenirForm" action="{{ route('a-venir.update', $avenir->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="content clearfix">
                                        <section id="step-1" class="body current" aria-hidden="false">
                                            <h3 class="mb-4">Informations</h3>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group"><label for="nom">Nom *</label><input
                                                            type="text" class="form-control" id="nom"
                                                            name="nom" value="{{ old('nom', $avenir->nom) }}" required>
                                                    </div>
                                                    <div class="form-group"><label for="description">Description</label>
                                                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $avenir->description) }}</textarea>
                                                    </div>
                                                    <div class="form-group"><label for="etat">Etat</label><select
                                                            class="form-control" id="etat" name="etat">
                                                            <option value="1" {{ $avenir->etat ? 'selected' : '' }}>
                                                                Actif</option>
                                                            <option value="0" {{ !$avenir->etat ? 'selected' : '' }}>
                                                                Inactif</option>
                                                        </select></div>
                                                    <div class="form-group"><label for="date_debut">Date de début
                                                            *</label><input type="datetime-local" class="form-control"
                                                            id="date_debut" name="date_debut"
                                                            value="{{ old('date_debut', $avenir->date_debut->format('Y-m-d\TH:i')) }}"
                                                            required></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-info">
                                                        <h6><i class="fas fa-info-circle"></i> Astuce</h6><small>Les vidéos
                                                            listées sont uniquement celles non publiées.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section id="step-2" class="body hidden" aria-hidden="true">
                                            <h3 class="mb-4">Sélection des vidéos (non publiées)</h3>
                                            <div class="row mb-3">
                                                <div class="col-md-6"><input type="text" id="searchVideos"
                                                        class="form-control" placeholder="Rechercher une vidéo..."></div>
                                                <div class="col-md-6 text-right"><span class="badge badge-primary"
                                                        id="selectedCount">0 vidéo(s) sélectionnée(s)</span></div>
                                            </div>
                                            <div class="video-selection-grid" id="videosGrid">
                                                @foreach ($videos as $video)
                                                    @php $isSelected = $avenir->items->contains('id_video', $video->id); @endphp
                                                    <div class="video-card {{ $isSelected ? 'selected' : '' }}"
                                                        data-video-id="{{ $video->id }}"
                                                        data-video-title="{{ $video->nom }}"
                                                        data-video-duration="{{ $video->duree ?? '00:00:00' }}"
                                                        data-video-url="{{ asset('storage/' . $video->media->url_fichier ?? '') }}">
                                                        <div class="video-thumbnail"
                                                            style="position: relative; height: 150px; overflow: hidden; border-radius: 8px; background: #000; margin-bottom: 10px;">
                                                            <video style="width: 100%; height: 100%; object-fit: cover;"
                                                                preload="metadata" onloadeddata="this.currentTime = 10">
                                                                <source
                                                                    src="{{ asset('storage/' . $video->media->url_fichier ?? '') }}#t=10"
                                                                    type="video/mp4">
                                                            </video>
                                                            <div class="play-overlay"
                                                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 2rem; opacity: 0.8;">
                                                                <i class="fas fa-play-circle"></i></div>
                                                        </div>
                                                        <div class="selection-order {{ $isSelected ? '' : 'hidden' }}">
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2"><i
                                                                class="fas fa-play-circle text-primary mr-2"></i>
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
                                                                    value="{{ $video->id }}"
                                                                    {{ $isSelected ? 'checked' : '' }}>
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
                                                Glissez-déposez pour réorganiser.</div>
                                            <div class="selected-videos-list">
                                                <h5 class="mb-3">Vidéos sélectionnées</h5>
                                                <div id="sortableVideosList" class="sortable-list"></div>
                                                <div id="noVideosSelected" class="text-center py-4 text-muted"><i
                                                        class="fas fa-video fa-3x mb-3"></i>
                                                    <p>Aucune vidéo sélectionnée.</p>
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
                                                        </h5><button type="button"
                                                            class="btn btn-info bg-info test-playlist-btn"
                                                            id="testPlaylistBtn"><i class="fas fa-play"></i>
                                                            Prévisualiser</button>
                                                    </div>
                                                    <div id="previewVideosList"></div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="selected_videos" id="selectedVideosInput">
                                        </section>
                                    </div>
                                    <div class="actions clearfix">
                                        <button type="button" id="prevBtn" class="btn btn-outline-secondary hidden"><i
                                                class="fas fa-arrow-left"></i> Précédent</button>
                                        <div>
                                            <button type="button" id="nextBtn" class="btn btn-primary">Suivant <i
                                                    class="fas fa-arrow-right"></i></button>
                                            <button type="submit" id="finishBtn"
                                                class="btn btn-success bg-success hidden"><i class="fas fa-save"></i>
                                                Mettre à jour</button>
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
                        Prévisualisation</h5><button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="video-player-container"><video id="modalVideoPlayer" class="p-4" controls
                                    controlsList="nodownload">
                                    <source src="" type="video/mp4">Votre navigateur ne supporte pas la lecture
                                    vidéo.
                                </video></div>
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
                <div class="modal-footer"><button type="button" class="btn btn-secondary bg-secondary"
                        data-dismiss="modal"><i class="fas fa-times"></i> Fermer</button></div>
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

            @foreach ($avenir->items as $item)
                selectedVideos.push({
                    id: '{{ $item->video->id }}',
                    title: '{{ addslashes($item->video->nom) }}',
                    duration: '{{ $item->duree_video ?? '00:00:00' }}',
                    url: '{{ asset('storage/' . $item->video->media->url_fichier ?? '') }}',
                    order: {{ $item->position }}
                });
            @endforeach

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
                const s = $(this).val().toLowerCase();
                $('.video-card').each(function() {
                    const title = $(this).data('video-title').toLowerCase();
                    $(this).toggle(title.includes(s));
                });
            });

            function updateSelectionOrder() {
                $('.video-card .selection-order').addClass('hidden');
                selectedVideos.forEach((v, i) => {
                    v.order = i + 1;
                });
                selectedVideos.forEach(v => {
                    const $card = $(`.video-card[data-video-id="${v.id}"]`);
                    $card.find('.selection-order').text(v.order).removeClass('hidden');
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
                selectedVideos.forEach((v, i) => {
                    const item =
                        `<div class="selected-video-item" data-video-id="${v.id}"><div class="drag-handle"><i class="fas fa-grip-vertical"></i></div><div class="video-info"><div class="video-title">${v.title}</div><div class="video-duration"><i class="fas fa-clock"></i> ${v.duration}</div></div><div class="video-order-number"><span class="badge badge-primary">#${v.order}</span></div><button type="button" class="remove-video" data-video-id="${v.id}"><i class="fas fa-times"></i></button></div>`;
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
            }

            function updateVideoOrder() {
                const newOrder = [];
                $('#sortableVideosList .selected-video-item').each(function(index) {
                    const id = $(this).data('video-id');
                    const v = selectedVideos.find(x => x.id == id);
                    if (v) {
                        v.order = index + 1;
                        newOrder.push(v);
                        $(this).find('.video-order-number .badge').text(`#${index+1}`);
                    }
                });
                selectedVideos = newOrder;
                updateSelectionOrder();
            }
            $(document).on('click', '.remove-video', function() {
                const id = $(this).data('video-id');
                selectedVideos = selectedVideos.filter(v => v.id != id);
                $(`.video-checkbox[value="${id}"]`).prop('checked', false);
                $(`.video-card[data-video-id="${id}"]`).removeClass('selected');
                updateSortableList();
                updateSelectionOrder();
                updateSelectedCount();
            });

            function updatePreview() {
                $('#preview-nom').text($('#nom').val());
                $('#preview-description').text($('#description').val() || 'Aucune description');
                $('#preview-date').text(formatDateTime($('#date_debut').val()));
                $('#preview-count').text(selectedVideos.length);
                const $list = $('#previewVideosList');
                $list.empty();
                selectedVideos.forEach((v, i) => {
                    const item =
                        `<div class="preview-video-item"><div class="video-order">${i+1}</div><div class="video-info"><div class="video-title">${v.title}</div><div class="video-duration"><i class="fas fa-clock"></i> ${v.duration}</div></div></div>`;
                    $list.append(item);
                });
                $('#selectedVideosInput').val(JSON.stringify(selectedVideos));
            }

            function formatDateTime(s) {
                if (!s) return '';
                const d = new Date(s);
                return d.toLocaleString('fr-FR', {
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
            updateSelectedCount();
            updateSelectionOrder();
        });
    </script>
@endpush

@extends('admin.master')

@section('title', 'Modifier Programmation - À venir')

@php
    /* Reuse the playlist create as edit UI for now */
@endphp
@include('admin.playlists.edit')
