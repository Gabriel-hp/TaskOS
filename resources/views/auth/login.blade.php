<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                            <h3>TaskOS</h3>
                            <p class="text-muted">Sistema de Agendamento com O.S.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div>
                                <label for="username">Usuário</label>
                                <input type="text" name="username" id="username" value="{{ old('username') }}" required>
                            </div>

                            <div>
                                <label for="password">Senha</label>
                                <input type="password" name="password" id="password" required>
                            </div>

                            <button type="submit">Entrar</button>

                            @error('username')
                                <div>{{ $message }}</div>
                            @enderror
                        </form>


                        <div class="mt-4 text-sm text-muted">
                            <p><strong>Usuários de teste:</strong></p>
                            <p>admin.silva / senha123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
