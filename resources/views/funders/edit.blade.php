@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>{{ __('main.edit_funder') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('funders.update', $funder->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">{{ __('main.name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $funder->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="contact_person">{{ __('main.contact_person') }}</label>
                    <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $funder->contact_person) }}">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">{{ __('main.phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $funder->phone) }}" required>
                     @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="form-group">
                    <label for="address">{{ __('main.address') }}</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $funder->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('main.update') }}</button>
                <a href="{{ route('funders.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection 