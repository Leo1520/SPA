<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Formulario de Edición de Sala
 * ════════════════════════════════════════════════
 * RF008 - GESTIÓN DE SALAS
 */
?>

<div class="page-header">
    <h2 class="page-title">Editar Sala</h2>
    <a href="index.php?page=salas" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formSala" action="index.php?page=salas&action=update" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        
        <!-- ID de la sala -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($sala['id']) ?>">

        <!-- ID (solo lectura) -->
        <div class="form-group">
            <label class="form-label">ID</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($sala['id']) ?>" disabled>
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" class="form-label required">Nombre de la Sala</label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($old['nombre'] ?? $sala['nombre']) ?>" 
                required
                maxlength="50">
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
                value="<?= htmlspecialchars($old['capacidad'] ?? $sala['capacidad'] ?? '') ?>"
                min="1"
                max="999">
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
                value="<?= htmlspecialchars($old['ubicacion'] ?? $sala['ubicacion'] ?? '') ?>"
                maxlength="100">
            <small class="form-help">Ubicación física de la sala (opcional)</small>
            <?php if (isset($errors['ubicacion'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['ubicacion']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Actualizar Sala</button>
            <a href="index.php?page=salas" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
