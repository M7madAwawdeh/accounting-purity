@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.projects') }}</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">{{ __('main.add_project') }}</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="search" class="sr-only">{{ __('main.search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('main.search_by_project_or_funder') }}" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-secondary mb-2">{{ __('main.search') }}</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive-md">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ __('main.name') }}</th>
                            <th>{{ __('main.funder') }}</th>
                            <th>{{ __('main.start_date') }}</th>
                            <th>{{ __('main.end_date') }}</th>
                            <th>{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->funder->name ?? '' }}</td>
                                <td>{{ $project->start_date }}</td>
                                <td>{{ $project->end_date }}</td>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">{{ __('main.show') }}</a>
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary">{{ __('main.edit') }}</a>
                                    <a href="{{ route('projects.show', ['project' => $project->id, 'print' => true]) }}" target="_blank" class="btn btn-sm btn-secondary">{{ __('main.print') }}</a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">{{ __('main.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('main.no_projects_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 