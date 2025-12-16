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

    .resend-active {
    color: #7367f0 !important;
    cursor: pointer !important;
    text-decoration: underline;
    }

</style>

@section('content')
    <h4 class="text-center mb-2">Enter the token that was sent to your email.📩</h4>
    <p class="text-center mb-4" style="font-size:0.8rem; margin-bottom: 2.5rem !important;">
        An email containing a token has been sent to your email. Please enter this token to reset your password.
    </p>

    <form id="signinForm" name="signinForm" class="mb-3" method="POST" action="#">
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

        <div>
        <label class="text-left mt-5" style="font-size:0.7rem;"> Didn't receive the email?</label><br>
        <label id="resend" class="text-left mb-3" style="font-size:0.7rem; color: #999; pointer-events: none; cursor: default;">Resend token in <span id="countdown">30</span> seconds.</label>
        </div>

        <div class="mb-3 mt-3">
            <a href="" class="btn btn-primary d-grid w-100" type="submit">Send</a>
            <a href="{{ route('admin.password.forgot') }}" class="btn btn-secondary d-grid w-100 mt-2">Back </a>
        </div>
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
        let timeLeft = 15; // segundos
        const countdown = document.getElementById("countdown");

        const timer = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(timer);
                countdown.textContent = "0";
                // aqui você pode habilitar botão de reenviar o token
                console.log("Tempo acabou!");
            }
        }, 1000);
    });

    document.addEventListener("DOMContentLoaded", function () {
        let timeLeft = 30;
        const countdown = document.getElementById("countdown");
        const resend = document.getElementById("resend");

        const timer = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(timer);

                // transformando em link clicável
                resend.textContent = "Resend token";
                resend.classList.add("resend-active");
                resend.style.pointerEvents = "auto";

                resend.addEventListener("click", function () {
                    console.log("Token reenviado!");
                    // aqui você chama a rota para reenviar o email
                });
            }
        }, 1000);
    });

</script>
@endsection
