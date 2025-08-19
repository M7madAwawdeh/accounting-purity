@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>{{ __('main.edit_member') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('members.update', $member->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">{{ __('main.name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $member->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">{{ __('main.phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $member->phone) }}" required>
                     @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="form-group">
                    <label for="address">{{ __('main.address') }}</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $member->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="membership_type">{{ __('main.membership_type') }}</label>
                    <input type="text" name="membership_type" class="form-control @error('membership_type') is-invalid @enderror" value="{{ old('membership_type', $member->membership_type) }}" required>
                    @error('membership_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="joining_date">{{ __('main.joining_date') }}</label>
                    <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $member->joining_date) }}" required>
                     @error('joining_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">{{ __('main.status') }}</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>{{ __('main.active') }}</option>
                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>{{ __('main.inactive') }}</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('main.update') }}</button>
                <a href="{{ route('members.index') }}" class="btn btn-secondary">{{ __('main.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection 