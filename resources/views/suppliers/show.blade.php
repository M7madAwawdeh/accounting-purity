@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.supplier_details') }}</h1>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="fas fa-truck fa-7x text-primary mb-3"></i>
                    <h4 class="font-weight-bold">{{ $supplier->name }}</h4>
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">{{ __('main.contact_person') }}</th>
                                <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                            </tr>
                             <tr>
                                <th>{{ __('main.phone') }}</th>
                                <td>{{ $supplier->phone }}</td>
                            </tr>
                             <tr>
                                <th>{{ __('main.address') }}</th>
                                <td>{{ $supplier->address ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                     <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-primary">{{ __('main.edit') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 