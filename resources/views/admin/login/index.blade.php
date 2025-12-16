@extends('layouts.guest')
@section('title', 'Admin Login')

@section('content')
<h4 class="text-center mb-2">Welcome to VIJO! 👋</h4>
<p class="text-center mb-4">Please sign-in to your account</p>

<form id="signinForm" name="signinForm" class="mb-3" method="POST" action="{{ url('/admin/login') }}">
    @csrf
    <div class="form-floating form-floating-outline mb-5">
        <input type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            placeholder="Enter your email"
            value="{{ old('email') }}"
            autofocus />
        <label for="email">Email</label>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-5 form-password-toggle">
        <div class="form-password-toggle form-control-validation">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        aria-describedby="password" />
                    <label for="password">Password</label>
                </div>
                <span class="input-group-text cursor-pointer">
                    <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="mb-4 d-flex justify-content-end">
        <a href="" class="modal-subtitle" style="font-size:0.8rem;">I forgot my password.</a>
    </div>

    <div class="mb-5">
        <button class="btn btn-primary d-grid w-100" type="submit">login</button>
    </div>
</form>
@endsection
