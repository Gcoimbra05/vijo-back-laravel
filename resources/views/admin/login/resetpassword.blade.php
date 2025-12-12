@extends('layouts.guest')
@section('title', 'Admin Login')

<style>
.input-group .form-control:focus {
box-shadow: none !important;
border-color: #ccc !important; /* mantém limpo */
}

.input-group-text {
    background-color: transparent !important;
    border-left: none !important;
    box-shadow: none !important;
}

.input-group-text:focus {
    outline: none !important;
    box-shadow: none !important;
}

.x-list {
    list-style: none;
    padding-left: 0;
}

.x-list li::before {
    content: "✖ ";
    font-weight: bold;
}

.x-list li.check::before {
    content: "✔ ";
    font-weight: bold;
}

#savePasswordBtn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

</style>

@section('content')
    <h4 class="text-center mb-2">Set your new password. 🔒</h4>
    <p class="text-center mb-4">Enter the new password and repeat it again.</p>

    <form method="POST" action="{{ route('admin.password.resetpassword') }}">
            @csrf
            <div class="mb-3">
                <label for="newpassword" class="form-label">New Password</label>
                    <div class="input-group">
                        <input class="form-control wide-input password-field @error('newpassword') is-invalid @enderror" type="password" id="newpassword" name="password" placeholder="Ex: New Password" autocomplete="off"  />
                            <span class="input-group-text bg-transparent border-0 p-0.3rem toggle-password">
                                <i class="bx bx-hide"  style="font-size: 1.35rem; cursor: pointer;"></i>
                            </span>
                    </div>
            </div>
            <div class="mb-3">
                <label for="repeatpassword" class="form-label">Repeat the new password</label>
                    <div class="input-group">
                        <input class="form-control wide-input password-field @error('repeatpassword') is-invalid @enderror" type="password" id="repeatpassword" name="password_confirmation" placeholder="Ex: Repeat the new password" autocomplete="off" />
                            <span class="input-group-text bg-transparent border-0 p-0.3rem toggle-password">
                                <i class="bx bx-hide"  style="font-size: 1.35rem; cursor: pointer;"></i>
                            </span>
                        <div class="invalid-feedback">
                            Passwords do not match.
                        </div>
                        <div class="valid-feedback">
                            Passwords match!
                        </div>
            </div>
            <div class="mt-3" style="font-size:0.85rem;">
                <p class="mb-1">Your password must meet the following requirements:</p>
                <ul class="x-list">
                    <li id="li1" class="text-danger">Minimum of 6 digits</li>
                    <li id="li2" class="text-danger">Must have at least one uppercase letter</li>
                    <li id="li3" class="text-danger">Must have at least one number</li>
                    <li id="li4" class="text-danger">Contain a special character (!@#$%^&* etc.)</li>
                </ul>
            </div>
            <div class="mb-3">
                    <button id="savePasswordBtn" class="btn btn-primary d-grid w-100" type="submit" style="margin-top:1.5rem;" disabled>Enter</button>
                    <a href="{{ route('admin.validatetoken.show') }}" class="btn btn-secondary d-grid w-100 mt-2">Back </a>
            </div>
    </form>
@endsection

@section('scripts')
<script>

document.querySelector("form").addEventListener("submit", () => {
    console.log("📤 FORM ENVIADO");
})

const newPassword = document.getElementById('newpassword');
const repeatPassword = document.getElementById('repeatpassword');
const saveBtn       = document.getElementById('savePasswordBtn');

document.querySelectorAll('.toggle-password').forEach(span => {
    span.addEventListener('click', () => {
        const input = span.closest('.input-group').querySelector('.password-field');
        const icon = span.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    });
}); 

repeatPassword.addEventListener('input', function () {
    if (repeatPassword.value.length > 0) {
        if (newPassword.value !== repeatPassword.value) {
            repeatPassword.classList.remove('is-valid');
            repeatPassword.classList.add('is-invalid');
        } else {
            repeatPassword.classList.remove('is-invalid');
            repeatPassword.classList.add('is-valid');
        }
    } else {
        // Se apagou tudo, remove o erro
        repeatPassword.classList.remove('is-invalid');
        repeatPassword.classList.remove('is-valid');
    }
});

newPassword.addEventListener('input', function () {
    const value = newPassword.value;

    // Verifica os critérios
    const hasMinLength = value.length >= 6;
    const hasUppercase = /[A-Z]/.test(value);
    const hasNumber = /[0-9]/.test(value);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value);

    // Atualiza a lista de verificação
    document.getElementById('li1').className = hasMinLength ? 'text-success check' : 'text-danger';
    document.getElementById('li2').className = hasUppercase ? 'text-success check' : 'text-danger';
    document.getElementById('li3').className = hasNumber ? 'text-success check' : 'text-danger';
    document.getElementById('li4').className = hasSpecialChar ? 'text-success check' : 'text-danger';

    if (hasMinLength && hasUppercase && hasNumber && hasSpecialChar) {
        newPassword.classList.remove('is-invalid');
        newPassword.classList.add('is-valid');
    } else {
        newPassword.classList.remove('is-valid');
        newPassword.classList.add('is-invalid');
    }
});

function validateAll() {
    const value = newPassword.value;

    // Critérios
    const hasMinLength   = value.length >= 6;
    const hasUppercase   = /[A-Z]/.test(value);
    const hasNumber      = /[0-9]/.test(value);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value);
    const passwordsMatch = newPassword.value === repeatPassword.value;

    // Habilita o botão só quando tudo é válido
    if (hasMinLength && hasUppercase && hasNumber && hasSpecialChar && passwordsMatch) {
        saveBtn.disabled = false; // Habilitar o botão
    } else {
        saveBtn.disabled = true;  // Desabilitar o botão
    }
}


// Monitora os campos
newPassword.addEventListener('input', validateAll);
repeatPassword.addEventListener('input', validateAll);

</script>
@endsection
