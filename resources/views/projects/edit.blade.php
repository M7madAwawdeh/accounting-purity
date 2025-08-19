@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>{{ __('main.edit_project') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">{{ __('main.name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="funder_id">{{ __('main.funder') }}</label>
                    <select name="funder_id" class="form-control @error('funder_id') is-invalid @enderror" required>
                        <option value="">{{ __('main.select_funder') }}</option>
                        @foreach($funders as $funder)
                            <option value="{{ $funder->id }}" {{ old('funder_id', $project->funder_id) == $funder->id ? 'selected' : '' }}>{{ $funder->name }}</option>
                        @endforeach
                    </select>
                    @error('funder_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="start_date">{{ __('main.start_date') }}</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $project->start_date) }}" required>
                     @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="form-group">
                    <label for="end_date">{{ __('main.end_date') }}</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $project->end_date) }}" required>
                     @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">{{ __('main.description') }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('main.update') }}</button>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection 