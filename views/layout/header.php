<?php
/**
 * ════════════════════════════════════════
 * LAYOUT: Header
 * ════════════════════════════════════════
 * Topbar + Sidebar con navegación
 */

// Verificar sesión activa
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$currentPage = $_GET['page'] ?? '';
$userName = htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']);
$userRole = htmlspecialchars($_SESSION['rol']);
$idRol = $_SESSION['id_rol'];

// Determinar qué elementos del menú mostrar según el rol
// 1 = Administrador, 2 = Recepcionista, 3 = Cajero, 4 = Terapeuta
$canAccessClientes = in_array($idRol, [1, 2]); // Admin y Recepcionista
$canAccessReservas = in_array($idRol, [1, 2]); // Admin y Recepcionista
$canAccessVentas = in_array($idRol, [1, 2, 3]); // Admin, Recepcionista y Cajero
$canAccessEmpleados = $idRol == 1; // Solo Admin
$canAccessServicios = $idRol == 1; // Solo Admin
$canAccessSalas = $idRol == 1; // Solo Admin

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPA Las América - <?= ucfirst($currentPage) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ════════════════════════════════════════ -->
    <!-- TOPBAR -->
    <!-- ════════════════════════════════════════ -->
    <div class="topbar">
        <div class="topbar-left">
            <h1 class="logo">🌿 SPA LAS AMÉRICA</h1>
        </div>
        <div class="topbar-right">
            <div class="user-info">
                <span class="user-name"><?= $userName ?></span>
                <span class="user-role"><?= $userRole ?></span>
            </div>
            <a href="index.php?page=logout" class="btn-logout" title="Cerrar Sesión">
                Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- ════════════════════════════════════════ -->
    <!-- SIDEBAR -->
    <!-- ════════════════════════════════════════ -->
    <div class="sidebar">
        <nav class="sidebar-nav">
            <?php if ($canAccessClientes): ?>
            <a href="index.php?page=clientes" class="sidebar-item <?= $currentPage === 'clientes' ? 'active' : '' ?>">
                <span class="sidebar-icon">👥</span>
                <span class="sidebar-text">Clientes</span>
            </a>
            <?php endif; ?>

            <?php if ($canAccessReservas): ?>
            <a href="index.php?page=reservas" class="sidebar-item <?= $currentPage === 'reservas' ? 'active' : '' ?>">
                <span class="sidebar-icon">📅</span>
                <span class="sidebar-text">Reservas</span>
            </a>
            <?php endif; ?>

            <?php if ($canAccessVentas): ?>
            <a href="index.php?page=ventas" class="sidebar-item <?= $currentPage === 'ventas' ? 'active' : '' ?>">
                <span class="sidebar-icon">💰</span>
                <span class="sidebar-text">Ventas</span>
            </a>
            <?php endif; ?>

            <?php if ($canAccessEmpleados): ?>
            <a href="#" class="sidebar-item disabled" title="Próximamente">
                <span class="sidebar-icon">👔</span>
                <span class="sidebar-text">Empleados</span>
            </a>
            <?php endif; ?>

            <?php if ($canAccessServicios): ?>
            <a href="index.php?page=servicios" class="sidebar-item <?= $currentPage === 'servicios' ? 'active' : '' ?>">
                <span class="sidebar-icon">💆</span>
                <span class="sidebar-text">Servicios</span>
            </a>
            <?php endif; ?>

            <?php if ($canAccessSalas): ?>
            <a href="#" class="sidebar-item disabled" title="Próximamente">
                <span class="sidebar-icon">🏠</span>
                <span class="sidebar-text">Salas</span>
            </a>
            <?php endif; ?>

            <!-- Separador -->
            <?php if (in_array($idRol, [1, 2])): ?>
            <div style="margin: 20px 0; border-top: 1px solid rgba(255,255,255,0.1);"></div>
            <?php endif; ?>

            <!-- REPORTES -->
            <?php if (in_array($idRol, [1, 2])): ?>
            <a href="index.php?page=reportes&action=reservasDia" 
               class="sidebar-item <?= ($currentPage === 'reportes' && ($_GET['action'] ?? '') === 'reservasDia') ? 'active' : '' ?>">
                <span class="sidebar-icon">📋</span>
                <span class="sidebar-text">Reporte Diario</span>
            </a>
            <?php endif; ?>

            <?php if ($idRol == 1): ?>
            <a href="index.php?page=reportes&action=ventasEmpleado" 
               class="sidebar-item <?= ($currentPage === 'reportes' && ($_GET['action'] ?? '') === 'ventasEmpleado') ? 'active' : '' ?>">
                <span class="sidebar-icon">📊</span>
                <span class="sidebar-text">Ventas por Empleado</span>
            </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- ════════════════════════════════════════ -->
    <!-- MAIN CONTENT -->
    <!-- ════════════════════════════════════════ -->
    <div class="main-content">
        
        <!-- Mensajes Flash -->
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span><?= htmlspecialchars($_SESSION['flash']['success']) ?></span>
            </div>
            <?php unset($_SESSION['flash']['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-error">
                <span class="alert-icon">✗</span>
                <span><?= htmlspecialchars($_SESSION['flash']['error']) ?></span>
            </div>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash']['info'])): ?>
            <div class="alert alert-info">
                <span class="alert-icon">i</span>
                <span><?= htmlspecialchars($_SESSION['flash']['info']) ?></span>
            </div>
            <?php unset($_SESSION['flash']['info']); ?>
        <?php endif; ?>
