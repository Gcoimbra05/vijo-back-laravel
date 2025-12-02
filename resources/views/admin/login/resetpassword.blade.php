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

</style>

@section('content')
    <h4 class="text-center mb-2">Set your new password. 🔒</h4>
    <p class="text-center mb-4">Enter the new password and repeat it again.</p>

    <form id="signinForm" name="signinForm" class="mb-3" method="POST" action="{{ url('/admin/login') }}">
            @csrf
            <div class="mb-3">
                <label for="newpassoword" class="form-label">New Password</label>
                    <div class="input-group">
                        <input class="form-control wide-input password-field @error('newpassoword') is-invalid @enderror" type="password" id="newpassoword" name="newpassoword" placeholder="Ex: New Password" autocomplete="off"  />
                        
                        <span class="input-group-text bg-transparent border-0 p-0.3rem toggle-password">
                            <i class="bx bx-hide"  style="font-size: 1.35rem; cursor: pointer;"></i>
                        </span>
                    </div>
            </div>
            <div class="mb-3">
                <label for="repeatpassword" class="form-label">Repeat the new password</label>
                    <div class="input-group">
                        <input class="form-control wide-input password-field @error('repeatpassword') is-invalid @enderror" type="password" id="repeatpassword" name="repeatpassword" placeholder="Ex: Repeat the new password" autocomplete="off" />
            
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
            <div class="mb-3">
                    <button id="savePasswordBtn" class="btn btn-primary d-grid w-100" type="submit" style="margin-top:1.5rem;">Enter</button>
                    <a href="{{ route('password.validatetoken') }}" class="btn btn-secondary d-grid w-100 mt-2">Back </a>
            </div>
    </form>
@endsection

@section('scripts')
<script>

const newPassword = document.getElementById('newpassoword');
const repeatPassword = document.getElementById('repeatpassword');

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
    }
});

document.getElementById('savePasswordBtn').addEventListener('click', function () {
    // Validação final
    if (newPassword.value !== repeatPassword.value) {
        alert("Enter the same password in both fields!");
        return;
    }
    // Fecha o modal se estiver correto
    const modalElement = document.getElementById('myModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    repeatPassword.classList.remove('is-valid');
    repeatPassword.classList.remove('is-invalid');
    modal.hide();
});

document.querySelectorAll('.toggle-password').forEach(span => {
    span.addEventListener('click', () => {
        // pega o input correspondente ao ícone clicado
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
</script>
@endsection
