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
@endsection

@extends('admin.master')

@section('title', 'Détail Programmation - À venir')

@php
    /* Reuse the playlist show UI for now */
@endphp
@include('admin.playlists.show')
