@extends('layouts.guest')
@section('title', 'Admin Login')

@section('content')
    <h4 class="text-center mb-2">Confirm your email to receive the token.📩</h4>

    <form method="POST" action="{{ route('admin.password.forgot') }}">
        @csrf
        <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                placeholder="Enter your email"
                value="{{ old('email') }}"
                autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
        </div>

        <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Send</button>
                <a href="{{ route('admin.login') }}" class="btn btn-secondary d-grid w-100 mt-2">Back</a>
        </div>
        </form>
@endsection
