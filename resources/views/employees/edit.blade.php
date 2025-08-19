@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>{{ __('main.edit_employee') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">{{ __('main.name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="position">{{ __('main.position') }}</label>
                    <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $employee->position) }}" required>
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="salary">{{ __('main.salary') }}</label>
                    <input type="number" name="salary" step="0.01" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $employee->salary) }}" required>
                     @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="joining_date">{{ __('main.joining_date') }}</label>
                    <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $employee->joining_date) }}" required>
                     @error('joining_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="form-group">
                    <label for="phone">{{ __('main.phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="form-group">
                    <label for="address">{{ __('main.address') }}</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $employee->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('main.update') }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection 