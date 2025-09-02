<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST" action="{{ url('/admin/login') }}">
        @csrf
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="password" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
    </form>
    @if($errors->any())
        <div style="color:red;">
            {{ $errors->first() }}
        </div>
    @endif
</body>
</html>