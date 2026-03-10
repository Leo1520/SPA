<?php
/**
 * ════════════════════════════════════════
 * ROUTER PRINCIPAL - ENTRY POINT
 * ════════════════════════════════════════
 * Sistema de gestión para Spa Las América
 * Arquitectura MVC básica con PHP puro
 */

// Iniciar sesión
session_start();

// Tiempo de inactividad de sesión (30 minutos)
$session_timeout = 30 * 60; // 30 minutos en segundos

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Sesión expirada por inactividad
    session_unset();
    session_destroy();
    header('Location: index.php?page=login&error=session_expired');
    exit();
}

// Actualizar el tiempo de última actividad
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// Autoload de clases
spl_autoload_register(function ($class) {
    $directories = ['controllers', 'models'];
    foreach ($directories as $dir) {
        $file = __DIR__ . '/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir configuración de base de datos
require_once __DIR__ . '/config/db.php';

// Obtener parámetros de ruta
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// Rutas públicas (no requieren autenticación)
$public_routes = ['login'];

// Verificar autenticación para rutas protegidas
if (!in_array($page, $public_routes) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// ════════════════════════════════════════
// ROUTER - Enrutamiento de solicitudes
// ════════════════════════════════════════

try {
    switch ($page) {
        // ──────────────────────────────────
        // AUTENTICACIÓN
        // ──────────────────────────────────
        case 'login':
            $controller = new AuthController();
            if ($action === 'post') {
                $controller->processLogin();
            } else {
                $controller->showLogin();
            }
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;

        // ──────────────────────────────────
        // GESTIÓN DE CLIENTES
        // ──────────────────────────────────
        case 'clientes':
            $controller = new ClienteController();
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'edit':
                    $controller->edit();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        // ──────────────────────────────────
        // GESTIÓN DE RESERVAS
        // ──────────────────────────────────
        case 'reservas':
            $controller = new ReservaController();
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'updateEstado':
                    $controller->updateEstado();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        // ──────────────────────────────────
        // GESTIÓN DE VENTAS
        // ──────────────────────────────────
        case 'ventas':
            $controller = new VentaController();
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'show':
                    $controller->show();
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        // ──────────────────────────────────
        // DASHBOARD (redirección por defecto)
        // ──────────────────────────────────
        default:
            // Por defecto redirigir a clientes
            header('Location: index.php?page=clientes');
            exit();
            break;
    }
} catch (Exception $e) {
    // Manejo básico de errores
    die("Error en la aplicación: " . htmlspecialchars($e->getMessage()));
}
