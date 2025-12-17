@extends('layouts.guest')
@section('title', 'Admin Login')

<style>
    .otp-container {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .otp-input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 1.6rem;
        border: 1px solid #bbb;
        border-radius: 8px;
        color: #00046a96;
    }

    .otp-input:focus {
        border-color: #7367f0;
        outline: none;
        box-shadow: 0 0 4px #7367f0;
    }

    #alert-success{
        position: fixed; 
        top: 20px; 
        right: 20px; 
        background-color: #28a745; 
        color: #fff; 
        padding: 12px 20px; 
        border-radius: 8px; 
        font-size: 0.9rem; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.6s ease;
    }

    .resend-active {
    color: #7367f0 !important;
    cursor: pointer !important;
    text-decoration: underline;
    pointer-events: auto !important;
    }

</style>

@section('content')
    @if (session('success'))
        <div id="alert-success">
            {{ session('success') }}
        </div>
            
        <script>
            // Alert for 4 seconds and fade out
            setTimeout(() => {
                const alertBox = document.getElementById('alert-success');
                if (alertBox) {
                    alertBox.style.opacity = "0"; // fade out
                    setTimeout(() => alertBox.remove(), 600);
                }
            }, 4000);
        </script>
    @endif

    <h4 class="text-center mb-2">Enter the token that was sent to your email.📩</h4>
    <p class="text-center mb-4" style="font-size:0.8rem; margin-bottom: 2.5rem !important;">
        An email containing a token has been sent to your email. Please enter this token to reset your password.
    </p>

    <form method="POST" action="{{ route('admin.password.validatetoken') }}">
        @csrf
        <div style="text-align: center;">
            <label for="token" class="form-label">Token</label>
        </div>

        <div class="otp-container">
            <input type="text" maxlength="1" class="otp-input" name="code[]" />
            <input type="text" maxlength="1" class="otp-input" name="code[]" />
            <input type="text" maxlength="1" class="otp-input" name="code[]" />
            <input type="text" maxlength="1" class="otp-input" name="code[]" />
            <input type="text" maxlength="1" class="otp-input" name="code[]" />
        </div>

        <input type="hidden" name="email" value="{{ old('email', session('email')) }}">


        <div>
            <label class="text-left mt-5" style="font-size:0.7rem;"> Didn't receive the email?</label><br>
            <label id="resend" class="text-left mb-3" style="font-size:0.7rem; color: #999; pointer-events: none; cursor: default;">Resend token in <span id="countdown">30</span> seconds.</label>
        </div>

        <div class="mb-3 mt-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Send</button>
            <a href="{{ route('admin.forgot.view') }}" class="btn btn-secondary d-grid w-100 mt-2">Back </a>
        </div>
    </form>

        <!-- <-- RESEND FORM  -->
    <form id="resendForm" method="POST" action="{{ route('admin.password.forgot') }}" style="display:none;">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
    </form>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll(".otp-input");

        inputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && index > 0 && input.value === "") {
                    inputs[index - 1].focus();
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        let timeLeft = 30;
        const countdown = document.getElementById('countdown');
        const resendLabel = document.getElementById('resend');
        const resendForm = document.getElementById('resendForm');

        const timer = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft > 0 ? timeLeft : 0;

            if (timeLeft <= 0) {
                clearInterval(timer);
                resendLabel.textContent = 'Resend token';
                resendLabel.classList.add('resend-active');
                resendLabel.style.pointerEvents = 'auto';
                resendLabel.style.cursor = 'pointer';
                

                console.log('Resend label is now active.');

                // handler only one time
                resendLabel.addEventListener('click', function onResendClick(e) {
                    resendForm.submit();
                    resendLabel.removeEventListener('click', onResendClick);
                });
            }
        }, 1000);
    });

</script>
@endsection
