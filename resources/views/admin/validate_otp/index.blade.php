@extends('layouts.guest')
@section('title', 'Two-Factor Authentication')

@section('content')
<form id="verifyAccountForm" name="verifyAccountForm" class="mb-3" method="post" action="{{ url()->current() }}">
    @csrf
    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
            <label class="form-label" for="otp">Verification Code</label>
        </div>
        <div class="input-group input-group-merge">
            <input type="password" id="otp" class="form-control @error('otp') is-invalid @enderror" name="otp" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="otp" value="{{ old('otp') }}" maxlength="6" autofocus />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            @error('otp')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
            <label class="fform-label cursor-pointer" data-bs-toggle="modal" data-bs-target="#checkJunkEmailModal">Did not receive code?</label>
            <a href="{{ url('admin/resend_2fa') }}">
                <small>Resend</small>
            </a>
        </div>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit">Verify</button>
    </div>
</form>
<div class="text-center">
    <a href="{{ url('admin') }}" class="d-flex align-items-center justify-content-center">
        <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
        Back to Sign in
    </a>
</div>
@endsection
