@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.donations') }}</h1>
        <a href="{{ route('donations.create') }}" class="btn btn-primary">{{ __('main.add_donation') }}</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('main.filter') }}</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('donations.index') }}" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="search" class="sr-only">{{ __('main.search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('main.search_by_donor_or_desc') }}" value="{{ request('search') }}">
                </div>
                 <div class="form-group mb-2 mr-sm-2">
                    <label for="project_id" class="sr-only">{{ __('main.project') }}</label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">{{ __('main.all_projects') }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary mb-2">{{ __('main.search') }}</button>
                 <a href="{{ route('donations.index') }}" class="btn btn-light mb-2 ml-2">{{ __('main.reset') }}</a>
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
                            <th>{{ __('main.date') }}</th>
                            <th>{{ __('main.donor_name') }}</th>
                            <th>{{ __('main.amount') }}</th>
                            <th>{{ __('main.payment_method') }}</th>
                            <th>{{ __('main.project') }}</th>
                            <th>{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donations as $donation)
                            <tr>
                                <td>{{ $donation->id }}</td>
                                <td>{{ $donation->date }}</td>
                                <td>{{ $donation->donor_name }}</td>
                                <td>{{ number_format($donation->amount, 2) }} {{ getCurrencyName($donation->currency, true) }}</td>
                                <td>{{ __('main.payment_method_' . $donation->payment_method) }}</td>
                                <td>
                                    @if($donation->project)
                                        <a href="{{ route('projects.show', $donation->project_id) }}" class="btn btn-sm btn-outline-info" target="_blank">{{ $donation->project->name }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('donations.show', $donation->id) }}" class="btn btn-sm btn-info">{{ __('main.show') }}</a>
                                    <a href="{{ route('donations.edit', $donation->id) }}" class="btn btn-sm btn-primary">{{ __('main.edit') }}</a>
                                    <a href="{{ route('donations.show', ['donation' => $donation->id, 'print' => true]) }}" target="_blank" class="btn btn-sm btn-secondary">{{ __('main.print') }}</a>
                                    <form action="{{ route('donations.destroy', $donation->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">{{ __('main.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('main.no_donations_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center">
                {{ $donations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 