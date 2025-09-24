@extends('admin.master')

@section('title', 'Détails - À venir')

@push('styles')
    <style>
        .playlist-details-container {
            padding: 20px;
        }

        .playlist-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .playlist-info-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .playlist-info-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #4e73df;
        }

        .playlist-info-body {
            padding: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 150px;
        }

        .info-value {
            color: #6c757d;
            flex: 1;
        }

        .videos-table {
            width: 100%;
            border-collapse: collapse;
        }

        .videos-table th {
            background: #4e73df;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        .videos-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .videos-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .videos-table tr:hover {
            background: #e9ecef;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }

            .info-label {
                margin-bottom: 5px;
                min-width: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid playlist-details-container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title"><i class="fas fa-clock mr-2"></i>Détails de la programmation</h2>
                    <a href="{{ route('a-venir.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle
                        programmation</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="playlist-info-card">
                    <div class="playlist-info-header"><i class="fas fa-info-circle"></i> Informations</div>
                    <div class="playlist-info-body">
                        <div class="info-row">
                            <div class="info-label">Nom:</div>
                            <div class="info-value">{{ $avenir->nom }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Description:</div>
                            <div class="info-value">{{ $avenir->description ?: 'Aucune description' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date de début:</div>
                            <div class="info-value">{{ $avenir->date_debut->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nombre de vidéos:</div>
                            <div class="info-value">{{ $avenir->items->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="playlist-info-card">
                    <div class="playlist-info-header d-flex justify-content-between align-items-center">
                        <div><i class="fas fa-film"></i> Vidéos</div>
                        @if ($avenir->items->count() > 0)
                            <button type="button" class="btn btn-sm btn-success bg-success" data-toggle="modal"
                                data-target="#playAvenirModal">
                                <i class="fas fa-play"></i> Lire la programmation
                            </button>
                        @endif
                    </div>
                    <div class="playlist-info-body">
                        @if ($avenir->items->count() > 0)
                            <div class="table-responsive">
                                <table class="videos-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Titre de la vidéo</th>
                                            <th>Durée</th>
                                            <th>Ordre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($avenir->items->sortBy('position') as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->video->nom }}</td>
                                                <td>{{ $item->duree_video ?? 'N/A' }}</td>
                                                <td>{{ $item->position }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-video-slash fa-2x mb-3"></i>
                                <div>Aucune vidéo dans cette programmation</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('a-venir.edit', $avenir->id) }}" class="btn btn-primary mr-2"><i
                            class="fas fa-edit"></i> Modifier</a>
                    <a href="{{ route('a-venir.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                        Retour</a>
                </div>
            </div>
        </div>
    </div>
    @if ($avenir->items->count() > 0)
        <div class="modal fade playlist-modal" id="playAvenirModal" tabindex="-1" role="dialog"
            aria-labelledby="playAvenirModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="playAvenirModalLabel">
                            <i class="fas fa-play-circle"></i> Lecture: {{ $avenir->nom }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="current-playing" id="currentVideoTitle">Chargement...</div>
                        <div class="video-player-container">
                            <video class="video-player" id="videoPlayer" controls>
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        </div>
                        <div class="playlist-controls d-flex justify-content-between align-items-center my-2">
                            <button class="btn bg-secondary" id="prevVideo"><i class="fas fa-step-backward"></i>
                                Précédent</button>
                            <div class="playlist-progress flex-grow-1 mx-3">
                                <div class="progress">
                                    <div class="progress-bar" id="playlistProgress" role="progressbar" style="width: 0%"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small id="progressText">Vidéo 1 sur {{ $avenir->items->count() }}</small>
                            </div>
                            <button class="btn bg-secondary" id="nextVideo">Suivant <i
                                    class="fas fa-step-forward"></i></button>
                        </div>
                        <div class="playlist-items d-none">
                            @foreach ($avenir->items->sortBy('position') as $index => $item)
                                <div class="playlist-item" data-video-id="{{ $item->video->id }}"
                                    data-video-src="{{ asset('storage/' . $item->video->media->url_fichier) }}"
                                    data-order="{{ $item->position }}">
                                    <div class="playlist-item-thumb"><i class="fas fa-play-circle"></i></div>
                                    <div class="playlist-item-info">
                                        <div class="playlist-item-title">{{ $item->video->nom }}</div>
                                        <div class="playlist-item-duration">{{ $item->duree_video ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let currentVideoIndex = 0;
                    const videoPlayer = document.getElementById('videoPlayer');
                    const playlistItems = document.querySelectorAll('#playAvenirModal .playlist-item');
                    const currentVideoTitle = document.getElementById('currentVideoTitle');
                    const prevButton = document.getElementById('prevVideo');
                    const nextButton = document.getElementById('nextVideo');
                    const progressBar = document.getElementById('playlistProgress');
                    const progressText = document.getElementById('progressText');

                    const videos = Array.from(playlistItems).map((item) => ({
                        id: item.dataset.videoId,
                        title: item.querySelector('.playlist-item-title').textContent,
                        url: item.dataset.videoSrc,
                        order: parseInt(item.dataset.order, 10) || 0,
                        duration: (item.querySelector('.playlist-item-duration') || {}).textContent || 'N/A'
                    })).sort((a, b) => a.order - b.order);

                    function loadVideo(index) {
                        if (index < 0 || index >= videos.length) return;
                        currentVideoIndex = index;
                        const video = videos[index];
                        currentVideoTitle.innerHTML = `<i class="fas fa-play-circle"></i> En cours: ${video.title}`;
                        videoPlayer.innerHTML = `<source src="${video.url}" type="video/mp4">`;
                        videoPlayer.load();
                        const pct = ((index + 1) / videos.length) * 100;
                        progressBar.style.width = pct + '%';
                        progressText.textContent = `Vidéo ${index + 1} sur ${videos.length}`;
                        prevButton.disabled = index === 0;
                        nextButton.disabled = index === videos.length - 1;
                        const playPromise = videoPlayer.play();
                        if (playPromise) {
                            playPromise.catch(() => {});
                        }
                    }

                    prevButton.addEventListener('click', function() {
                        if (currentVideoIndex > 0) loadVideo(currentVideoIndex - 1);
                    });
                    nextButton.addEventListener('click', function() {
                        if (currentVideoIndex < videos.length - 1) loadVideo(currentVideoIndex + 1);
                    });
                    videoPlayer.addEventListener('ended', function() {
                        if (currentVideoIndex < videos.length - 1) loadVideo(currentVideoIndex + 1);
                    });
                    $('#playAvenirModal').on('show.bs.modal', function() {
                        loadVideo(0);
                    });
                    $('#playAvenirModal').on('hidden.bs.modal', function() {
                        try {
                            videoPlayer.pause();
                            videoPlayer.currentTime = 0;
                            videoPlayer.src = '';
                            videoPlayer.load();
                        } catch (e) {}
                    });
                });
            </script>
        @endpush
    @endif
@endsection
