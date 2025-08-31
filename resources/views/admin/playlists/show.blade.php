@extends('admin.master')

@section('title', 'Détails de la Playlist')

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

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-edit {
            background: #4e73df;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-back {
            background: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }

            .info-label {
                margin-bottom: 5px;
                min-width: auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .videos-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid playlist-details-container">
            <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title">
                    <i class="fas fa-comment-dots mr-2"></i>Details de la playlist
                </h2>
                <a href="{{ route('playlists.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Playlist
                </a>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-12">

                <!-- Carte d'informations -->
                <div class="playlist-info-card">
                    <div class="playlist-info-header">
                        <i class="fas fa-info-circle"></i> Informations de la playlist
                    </div>
                    <div class="playlist-info-body">
                        <div class="info-row">
                            <div class="info-label">Nom:</div>
                            <div class="info-value">{{ $playlist->nom }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Description:</div>
                            <div class="info-value">{{ $playlist->description ?: 'Aucune description' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date de début:</div>
                            <div class="info-value">{{ $playlist->date_debut->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">État:</div>
                            <div class="info-value">
                                <span class="status-badge {{ $playlist->etat ? 'status-active' : 'status-inactive' }}">
                                    {{ $playlist->etat ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nombre de vidéos:</div>
                            <div class="info-value">{{ $playlist->items->count() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des vidéos -->
                <div class="playlist-info-card">
                    <div class="playlist-info-header">
                        <i class="fas fa-film"></i> Vidéos dans la playlist
                    </div>
                    <div class="playlist-info-body">
                        @if ($playlist->items->count() > 0)
                            <div class="table-responsive">
                                <table class="videos-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Titre de la vidéo</th>
                                            <th>Durée</th>
                                            <th>Ordre de lecture</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($playlist->items as $item)
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
                            <div class="empty-state">
                                <i class="fas fa-video-slash"></i>
                                <h4>Aucune vidéo dans cette playlist</h4>
                                <p>Ajoutez des vidéos pour commencer à utiliser cette playlist</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="action-buttons">
                    <a href="{{ route('playlists.edit', $playlist->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Modifier la playlist
                    </a>
                    <a href="{{ route('playlists.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
