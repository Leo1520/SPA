<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Formulario de Creación de Sala
 * ════════════════════════════════════════════════
 * RF008 - GESTIÓN DE SALAS
 */
?>

<div class="page-header">
    <h2 class="page-title">Nueva Sala</h2>
    <a href="index.php?page=salas" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formSala" action="index.php?page=salas&action=store" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" class="form-label required">Nombre de la Sala</label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                required
                maxlength="50"
                placeholder="Ej: Sala VIP 1, Sala Masajes, Sala de Relajación">
            <?php if (isset($errors['nombre'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['nombre']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Capacidad -->
        <div class="form-group">
            <label for="capacidad" class="form-label">Capacidad</label>
            <input 
                type="number" 
                class="form-control" 
                id="capacidad" 
                name="capacidad" 
                value="<?= htmlspecialchars($old['capacidad'] ?? '') ?>"
                min="1"
                max="999"
                placeholder="Número de personas">
            <small class="form-help">Capacidad máxima de la sala (opcional)</small>
            <?php if (isset($errors['capacidad'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['capacidad']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Ubicación -->
        <div class="form-group">
            <label for="ubicacion" class="form-label">Ubicación</label>
            <input 
                type="text" 
                class="form-control" 
                id="ubicacion" 
                name="ubicacion" 
                value="<?= htmlspecialchars($old['ubicacion'] ?? '') ?>"
                maxlength="100"
                placeholder="Ej: Primer piso, Ala este, Planta baja">
            <small class="form-help">Ubicación física de la sala (opcional)</small>
            <?php if (isset($errors['ubicacion'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['ubicacion']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Sala</button>
            <a href="index.php?page=salas" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
