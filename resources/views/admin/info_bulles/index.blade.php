@extends('admin.master')

@section('title', 'Gestion des Info-Bulles')

@push('styles')
    <style>
        .settings-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
            margin-bottom: 25px;
            background: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 20px;
            border-bottom: none;
            color: white;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.4rem;
        }

        .card-body {
            padding: 25px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #4e73df;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .section-subtitle {
            color: #6e707e;
            font-size: 1.1rem;
        }

        /* Switch Button Modern */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e74a3b;
            transition: .4s;
            border-radius: 34px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
        }

        input:checked+.slider {
            background-color: #1cc88a;
        }

        input:checked+.slider:before {
            transform: translateX(30px);
        }

        .slider:after {
            content: "OFF";
            color: white;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: 600;
        }

        input:checked+.slider:after {
            content: "ON";
            left: 8px;
            right: auto;
        }

        .switch-label {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .switch-text {
            font-weight: 600;
            color: #5a5c69;
        }

        .switch-status {
            font-size: 0.9rem;
            color: #858796;
            margin-top: 5px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #d1d3e2;
            width: 100%;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-secondary {
            background: #858796;
            color: white;
        }

        .btn-secondary:hover {
            background: #6e707e;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-outline-primary {
            border: 2px solid #4e73df;
            color: #4e73df;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: #4e73df;
            color: white;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Style spécifique pour les boutons d'action dans le tableau */
        .table .action-buttons {
            justify-content: center;
        }

        .table .btn {
            min-width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table .btn i {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 10px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table th {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 700;
            color: #4e73df;
            padding: 15px;
            text-align: center;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        .table tr:hover {
            background-color: #f8f9fc;
        }

        .status-active {
            color: #1cc88a;
            font-weight: 600;
        }

        .status-inactive {
            color: #e74a3b;
            font-weight: 600;
        }

        .text-truncate {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 20px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 20px;
            border-radius: 0 0 12px 12px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .settings-container {
                padding: 15px;
            }

            .card-body {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .text-truncate {
                max-width: 150px;
            }

            .table .action-buttons {
                flex-direction: row;
                justify-content: center;
            }
        }

        /* Loading state for buttons */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading:after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Badge styles */
        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: #e8f5f0;
            color: #1cc88a;
        }

        .badge-inactive {
            background: #fde8e8;
            color: #e74a3b;
        }

        /* Search and filter section */
        .search-section {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
    </style>
@endpush

@section('content')
    <section class="section" style="margin-top: -25px;">
        <div class="settings-container">
            <!-- Messages d'alerte -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
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

            <!-- En-tête -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="section-title">
                                <i class="fas fa-comment-dots mr-2"></i>Gestion des Info-Bulles
                            </h2>
                            <p class="section-subtitle">Configurez les messages contextuels de votre site</p>
                        </div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addInfoBulleModal">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Info-Bulle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section de recherche -->
            <div class="search-section">
                <form method="GET" action="{{ route('info-bulles.index') }}">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Rechercher par titre ou contenu...">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="statut" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actives
                                </option>
                                <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactives
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-search mr-2"></i>Filtrer
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('info-bulles.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-sync mr-2"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tableau des info-bulles existantes -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list mr-2"></i>Liste des Info-Bulles</h3>
                </div>
                <div class="card-body">
                    @if ($infoBulles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Titre</th>
                                        <th>Contenu</th>
                                        <th>Statut</th>
                                        <th>Création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($infoBulles as $infoBulle)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $infoBulle->titre }}</strong>
                                            </td>
                                            <td>
                                                <div class="text-truncate" title="{{ $infoBulle->texte }}">
                                                    {{ $infoBulle->texte }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($infoBulle->is_active)
                                                    <span class="badge badge-active">
                                                        <i class="fas fa-check-circle mr-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-inactive">
                                                        <i class="fas fa-times-circle mr-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $infoBulle->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <!-- Switch de statut -->
                                                    <form action="{{ route('info-bulles.toggle-status', $infoBulle->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <label class="switch">
                                                            <input type="checkbox"
                                                                {{ $infoBulle->is_active ? 'checked' : '' }}
                                                                onchange="this.form.submit()">
                                                            <span class="slider"></span>
                                                        </label>
                                                    </form>

                                                    <!-- Bouton Modifier -->
                                                    <button class="btn btn-sm btn-primary edit-btn" data-toggle="modal"
                                                        data-target="#editInfoBulleModal" data-id="{{ $infoBulle->id }}"
                                                        data-titre="{{ $infoBulle->titre }}"
                                                        data-texte="{{ $infoBulle->texte }}"
                                                        data-active="{{ $infoBulle->is_active }}" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Bouton Supprimer -->
                                                    <form action="{{ route('info-bulles.destroy', $infoBulle->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette info-bulle ?')"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($infoBulles->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $infoBulles->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune info-bulle configurée</h5>
                            <p class="text-muted">Commencez par ajouter votre première info-bulle.</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addInfoBulleModal">
                                <i class="fas fa-plus mr-2"></i>Ajouter une info-bulle
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Modal d'ajout -->
    <div class="modal fade" id="addInfoBulleModal" tabindex="-1" role="dialog"
        aria-labelledby="addInfoBulleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInfoBulleModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Ajouter une Info-Bulle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('info-bulles.store') }}" method="POST" id="addInfoBulleForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="add_titre">Titre de l'info-bulle *</label>
                                    <input type="text" class="form-control" id="add_titre" name="titre"
                                        value="{{ old('titre') }}" required placeholder="Ex: Guide d'utilisation">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">Statut d'activation</label>
                                    <div class="switch-label">
                                        <label class="switch">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" id="add_is_active" name="is_active" value="1"
                                                {{ old('is_active', true) ? 'checked' : '' }}>

                                            <span class="slider"></span>
                                        </label>
                                        <span class="switch-text" id="add_switch_text">
                                            {{ old('is_active', true) ? 'Activée' : 'Désactivée' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="add_texte">Contenu de l'info-bulle *</label>
                            <textarea class="form-control" id="add_texte" name="texte" rows="4" required
                                placeholder="Ex: Cette fonctionnalité vous permet de...">{{ old('texte') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editInfoBulleModal" tabindex="-1" role="dialog"
        aria-labelledby="editInfoBulleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInfoBulleModalLabel">
                        <i class="fas fa-edit mr-2"></i>Modifier l'Info-Bulle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editInfoBulleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_titre">Titre de l'info-bulle *</label>
                                    <input type="text" class="form-control" id="edit_titre" name="titre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">Statut d'activation</label>
                                    <div class="switch-label">
                                        <label class="switch">
                                            <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                                            <span class="slider"></span>
                                        </label>
                                        <span class="switch-text" id="edit_switch_text">Activée</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_texte">Contenu de l'info-bulle *</label>
                            <textarea class="form-control" id="edit_texte" name="texte" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Gestion du switch de statut pour l'ajout
            $('#add_is_active').change(function() {
                $('#add_switch_text').text(this.checked ? 'Activée' : 'Désactivée');
            });

            // Gestion du switch de statut pour l'édition
            $('#edit_is_active').change(function() {
                $('#edit_switch_text').text(this.checked ? 'Activée' : 'Désactivée');
            });

            // Gestion de l'édition - Remplir le modal avec les données
            $('#editInfoBulleModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var titre = button.data('titre');
                var texte = button.data('texte');
                var active = button.data('active');

                var modal = $(this);
                modal.find('#edit_titre').val(titre);
                modal.find('#edit_texte').val(texte);
                modal.find('#edit_is_active').prop('checked', active);
                modal.find('#edit_switch_text').text(active ? 'Activée' : 'Désactivée');

                // Mettre à jour l'action du formulaire
                modal.find('#editInfoBulleForm').attr('action', "{{ route('info-bulles.update', ':id') }}"
                    .replace(':id', id));
            });

            // Réinitialiser le modal d'ajout quand il est fermé
            $('#addInfoBulleModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('#add_is_active').prop('checked', true).trigger('change');
            });

            // Désactiver les boutons pendant l'envoi
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]')
                    .addClass('btn-loading')
                    .prop('disabled', true);
            });

            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
@endpush
