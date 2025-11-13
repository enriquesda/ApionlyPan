<!-- resources/views/auth/reset-password.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f7f7f7;
        }
        
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        input[type="password"] {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }
        
        button {
            padding: 0.75rem;
            font-size: 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .error {
            color: red;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Restablecer Contraseña</h1>
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <label for="password">Nueva Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Ingrese su nueva contraseña" required>

            <label for="password_confirmation">Confirmar Contraseña:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme su nueva contraseña" required>

            <button type="submit">Restablecer Contraseña</button>
        </form>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</body>
</html>
