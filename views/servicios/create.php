<?php
/**
 * ════════════════════════════════════════
 * VISTA: Formulario de Creación de Servicio
 * ════════════════════════════════════════
 * RF007 - GESTIÓN DEL CATÁLOGO DE SERVICIOS
 */
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Servicio</h2>
    <a href="index.php?page=servicios" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formServicio" action="index.php?page=servicios&action=store" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- Nombre del servicio -->
        <div class="form-group">
            <label for="nombre" class="form-label required">Nombre del Servicio</label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                required
                maxlength="100"
                placeholder="Ej: Masaje Relajante, Facial Anti-edad">
            <?php if (isset($errors['nombre'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['nombre']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Descripción -->
        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea 
                class="form-control" 
                id="descripcion" 
                name="descripcion" 
                rows="4" 
                maxlength="500"
                placeholder="Breve descripción del servicio (opcional)"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
            <?php if (isset($errors['descripcion'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['descripcion']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Duración en minutos -->
        <div class="form-group">
            <label for="duracion_min" class="form-label required">Duración (minutos)</label>
            <input 
                type="number" 
                class="form-control" 
                id="duracion_min" 
                name="duracion_min" 
                value="<?= htmlspecialchars($old['duracion_min'] ?? '') ?>" 
                required
                min="1"
                max="999"
                step="1"
                placeholder="Ej: 60, 90, 120">
            <small class="form-help">Duración estándar del servicio en minutos</small>
            <?php if (isset($errors['duracion_min'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['duracion_min']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Precio -->
        <div class="form-group">
            <label for="precio" class="form-label required">Precio (Bs.)</label>
            <input 
                type="number" 
                class="form-control" 
                id="precio" 
                name="precio" 
                value="<?= htmlspecialchars($old['precio'] ?? '') ?>" 
                required
                min="0.01"
                max="99999.99"
                step="0.01"
                placeholder="Ej: 150.00">
            <small class="form-help">Precio en bolivianos (debe ser mayor a 0)</small>
            <?php if (isset($errors['precio'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['precio']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label class="form-label">Estado Inicial</label>
            <div class="form-check">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="activo" 
                    name="activo" 
                    value="1"
                    <?= isset($old['activo']) ? ($old['activo'] ? 'checked' : '') : 'checked' ?>>
                <label class="form-check-label" for="activo">
                    <strong>Activo</strong> (el servicio estará disponible inmediatamente)
                </label>
            </div>
            <small class="form-help">Servicios inactivos no aparecerán en reservas</small>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Servicio</button>
            <a href="index.php?page=servicios" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
