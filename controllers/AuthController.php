<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: AuthController
 * ════════════════════════════════════════
 * Gestión de autenticación y sesiones
 * RF005 - LOGIN Y ROLES
 */

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        // Si ya está autenticado, redirigir al sistema
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=clientes');
            exit();
        }

        // Verificar si está bloqueado temporalmente
        $blocked = $this->isBlocked();
        $error = $_GET['error'] ?? null;
        $attemptCount = $_SESSION['login_attempts'] ?? 0;

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesar login
     */
    public function processLogin() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=login');
            exit();
        }

        // Verificar si está bloqueado
        if ($this->isBlocked()) {
            $remainingTime = $this->getRemainingBlockTime();
            $_SESSION['flash']['error'] = "Cuenta bloqueada temporalmente. Intente nuevamente en {$remainingTime} minutos.";
            header('Location: index.php?page=login');
            exit();
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validar campos vacíos
        if (empty($username) || empty($password)) {
            $_SESSION['flash']['error'] = 'Por favor complete todos los campos';
            header('Location: index.php?page=login');
            exit();
        }

        // Buscar usuario
        $user = $this->usuarioModel->findByUsername($username);

        if (!$user) {
            $this->handleFailedLogin();
            header('Location: index.php?page=login');
            exit();
        }

        // Verificar si la cuenta está activa
        if ($user['activo'] != 1) {
            $_SESSION['flash']['error'] = 'Su cuenta ha sido desactivada. Contacte al administrador.';
            header('Location: index.php?page=login');
            exit();
        }

        // Verificar contraseña
        // Para el usuario de prueba con SHA2, verificar directamente
        $passwordValid = false;
        if (strlen($user['password']) === 64) {
            // Es SHA256 (usuario de prueba)
            $passwordValid = hash('sha256', $password) === $user['password'];
        } else {
            // Es password_hash normal
            $passwordValid = password_verify($password, $user['password']);
        }

        if (!$passwordValid) {
            $this->handleFailedLogin();
            header('Location: index.php?page=login');
            exit();
        }

        // Login exitoso - resetear intentos y crear sesión
        $this->resetLoginAttempts();
        $this->createSession($user);
        
        header('Location: index.php?page=clientes');
        exit();
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?page=login');
        exit();
    }

    /**
     * Manejar intento de login fallido
     */
    private function handleFailedLogin() {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        
        $_SESSION['login_attempts']++;
        $attempts = $_SESSION['login_attempts'];

        if ($attempts >= 3) {
            // Bloquear temporalmente (15 minutos)
            $_SESSION['blocked_until'] = time() + (15 * 60);
            $_SESSION['flash']['error'] = 'Ha superado el número máximo de intentos. Cuenta bloqueada temporalmente por 15 minutos.';
        } else {
            $_SESSION['flash']['error'] = "Credenciales incorrectas. Intento {$attempts} de 3.";
        }
    }

    /**
     * Verificar si el usuario está bloqueado
     * @return bool
     */
    private function isBlocked() {
        if (isset($_SESSION['blocked_until'])) {
            if (time() < $_SESSION['blocked_until']) {
                return true;
            } else {
                // El tiempo de bloqueo ha expirado
                $this->resetLoginAttempts();
            }
        }
        return false;
    }

    /**
     * Obtener tiempo restante de bloqueo en minutos
     * @return int
     */
    private function getRemainingBlockTime() {
        if (isset($_SESSION['blocked_until'])) {
            $remaining = $_SESSION['blocked_until'] - time();
            return ceil($remaining / 60);
        }
        return 0;
    }

    /**
     * Resetear intentos de login
     */
    private function resetLoginAttempts() {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['blocked_until']);
    }

    /**
     * Crear sesión de usuario
     * @param array $user
     */
    private function createSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre'] = $user['empleado_nombre'] ?? 'Usuario';
        $_SESSION['apellido'] = $user['empleado_apellido'] ?? 'Sistema';
        $_SESSION['rol'] = $user['rol_nombre'];
        $_SESSION['id_rol'] = $user['id_rol'];
        $_SESSION['last_activity'] = time();
    }
}
