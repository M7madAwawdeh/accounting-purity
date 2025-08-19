@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.suppliers') }}</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">{{ __('main.add_supplier') }}</a>
    </div>

     <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.index') }}" class="form-inline">
                <div class="form-group mb-2 mr-sm-2">
                    <label for="search" class="sr-only">{{ __('main.search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('main.search_by_name_or_phone') }}" value="{{ request('search') }}">
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
                            <th>{{ __('main.contact_person') }}</th>
                            <th>{{ __('main.phone') }}</th>
                            <th>{{ __('main.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->id }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>
                                    <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-sm btn-info">{{ __('main.show') }}</a>
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-primary">{{ __('main.edit') }}</a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">{{ __('main.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('main.no_suppliers_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 