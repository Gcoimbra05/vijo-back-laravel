@extends('layouts.guest')
@section('title', 'Admin Login')

@section('content')
    <h4 class="text-center mb-2">Welcome to VIJO! 👋</h4>
    <p class="text-center mb-4">Please sign-in to your account</p>

    <form id="signinForm" name="signinForm" class="mb-3" method="POST" action="{{ url('/admin/login') }}">
            @csrf
            <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text"
                                 class="form-control @error('email') is-invalid @enderror"
                                 id="email"
                                 name="email"
                                 placeholder="Enter your email or username"
                                 value="{{ old('email') }}"
                                 autofocus>
                    @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
            </div>
            <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="input-group input-group-merge">
                            <input type="password"
                                         id="password"
                                         class="form-control @error('password') is-invalid @enderror"
                                         name="password"
                                         placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                         aria-describedby="password">
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                    </div>
            </div>
            <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
            </div>
    </form>
@endsection
