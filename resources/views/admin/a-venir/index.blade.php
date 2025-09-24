@extends('admin.master')

@section('title', 'Gestion des Programmations - À venir')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title">
                    <i class="fas fa-clock mr-2"></i>Programmations - À venir
                </h2>
                <a href="{{ route('a-venir.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle programmation
                </a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Liste des programmations</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('a-venir.index') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Rechercher..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('a-venir.index') }}" class="btn btn-primary"> <i class="fas fa-sync m-2"></i> Réinitialiser</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover border-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Date de début</th>
                                        <th>Nombre de vidéos</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($avenirs as $avenir)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $avenir->nom }}</td>
                                            <td>{{ Str::limit($avenir->description, 50) }}</td>
                                            <td>{{ $avenir->date_debut->format('d/m/Y H:i') }}</td>
                                            <td>{{ $avenir->items->count() }}</td>
                                            <td>
                                                <a href="{{ route('a-venir.show', $avenir->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('a-venir.edit', $avenir->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('a-venir.destroy', $avenir->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm bg-danger"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette programmation ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune programmation trouvée</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $avenirs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.master')

@section('title', 'Programmations - À venir')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title">
                    <i class="fas fa-clock mr-2"></i>Gestion des programmations "À venir"
                </h2>
                <a href="{{ route('a-venir.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle programmation
                </a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Liste des programmations</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('a-venir.index') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Rechercher..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('a-venir.index') }}" class="btn btn-primary"> <i class="fas fa-sync m-2"></i> Réinitialiser</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover border-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Date de début</th>
                                        <th>Nombre de vidéos</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($avenirs as $avenir)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $avenir->nom }}</td>
                                            <td>{{ Str::limit($avenir->description, 50) }}</td>
                                            <td>{{ $avenir->date_debut->format('d/m/Y H:i') }}</td>
                                            <td>{{ $avenir->items->count() }}</td>
                                            <td>
                                                <a href="{{ route('a-venir.show', $avenir->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('a-venir.edit', $avenir->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('a-venir.destroy', $avenir->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm bg-danger"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette programmation ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune programmation trouvée</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $avenirs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



