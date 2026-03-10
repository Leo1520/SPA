<?php
/**
 * ════════════════════════════════════════
 * VISTA: Login
 * ════════════════════════════════════════
 * Formulario de autenticación
 */

// Generar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPA Las América</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2C3E35 0%, #3D5A4C 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h1 {
            color: #2C3E35;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #7F8C8D;
            font-size: 14px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #FDECEA;
            color: #E74C3C;
            border: 1px solid #E74C3C;
        }

        .alert-info {
            background-color: #EBF5FB;
            color: #3498DB;
            border: 1px solid #3498DB;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2C3E35;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #D5D8DC;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4A7C5F;
        }

        .btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4A7C5F;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background-color: #3D6B50;
        }

        .btn-primary:disabled {
            background-color: #A8C5A0;
            cursor: not-allowed;
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #7F8C8D;
            font-size: 13px;
        }

        .blocked-message {
            text-align: center;
            padding: 15px;
            background-color: #FDECEA;
            border: 2px solid #E74C3C;
            border-radius: 6px;
            color: #C0392B;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🌿 SPA LAS AMÉRICA</h1>
            <p>Sistema de Gestión</p>
        </div>

        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['flash']['error']) ?>
            </div>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>

        <?php if (isset($error) && $error === 'session_expired'): ?>
            <div class="alert alert-info">
                Su sesión ha expirado por inactividad. Por favor ingrese nuevamente.
            </div>
        <?php endif; ?>

        <?php if (isset($blocked) && $blocked): ?>
            <div class="blocked-message">
                ⚠ Cuenta bloqueada temporalmente por múltiples intentos fallidos
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login&action=post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label for="username">Usuario</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required
                    <?= (isset($blocked) && $blocked) ? 'disabled' : '' ?>
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    <?= (isset($blocked) && $blocked) ? 'disabled' : '' ?>
                >
            </div>

            <button 
                type="submit" 
                class="btn btn-primary"
                <?= (isset($blocked) && $blocked) ? 'disabled' : '' ?>
            >
                Iniciar Sesión
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; 2026 SPA Las América - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
