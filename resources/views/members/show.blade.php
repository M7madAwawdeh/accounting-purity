@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('main.member_details') }}</h1>
        <a href="{{ route('members.index') }}" class="btn btn-secondary">{{ __('main.back_to_list') }}</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="fas fa-user-friends fa-7x text-primary mb-3"></i>
                    <h4 class="font-weight-bold">{{ $member->name }}</h4>
                    <p class="text-muted">{{ $member->membership_type }}</p>
                    <span class="badge badge-{{ $member->status == 'active' ? 'success' : 'danger' }} p-2">{{ __('main.' . $member->status) }}</span>
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">{{ __('main.phone') }}</th>
                                <td>{{ $member->phone }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('main.address') }}</th>
                                <td>{{ $member->address ?? 'N/A' }}</td>
                            </tr>
                             <tr>
                                <th>{{ __('main.joining_date') }}</th>
                                <td>{{ $member->joining_date }}</td>
                            </tr>
                        </tbody>
                    </table>
                     <a href="{{ route('members.edit', $member->id) }}" class="btn btn-primary">{{ __('main.edit') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 