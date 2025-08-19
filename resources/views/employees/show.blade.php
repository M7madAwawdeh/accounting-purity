@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.employee_details') }}</h1>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="fas fa-user-tie fa-7x text-primary mb-3"></i>
                    <h4 class="font-weight-bold">{{ $employee->name }}</h4>
                    <p class="text-muted">{{ $employee->position }}</p>
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">{{ __('main.salary') }}</th>
                                <td>{{ number_format($employee->salary, 2) }}</td>
                            </tr>
                             <tr>
                                <th>{{ __('main.joining_date') }}</th>
                                <td>{{ $employee->joining_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('main.phone') }}</th>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('main.address') }}</th>
                                <td>{{ $employee->address ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                     <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">{{ __('main.edit') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 