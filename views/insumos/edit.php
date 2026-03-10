<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Formulario de Edición de Insumo
 * ════════════════════════════════════════════════
 * RF009 - GESTIÓN DE INSUMOS
 */
?>

<div class="page-header">
    <h2 class="page-title">Editar Insumo</h2>
    <a href="index.php?page=insumos" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formInsumo" action="index.php?page=insumos&action=update" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        
        <!-- ID del insumo -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($insumo['id']) ?>">

        <!-- ID (solo lectura) -->
        <div class="form-group">
            <label class="form-label">ID</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($insumo['id']) ?>" disabled>
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" class="form-label required">Nombre del Insumo</label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($old['nombre'] ?? $insumo['nombre']) ?>" 
                required
                maxlength="100">
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
                rows="3" 
                maxlength="500"><?= htmlspecialchars($old['descripcion'] ?? $insumo['descripcion'] ?? '') ?></textarea>
            <?php if (isset($errors['descripcion'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['descripcion']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Stock actual -->
        <div class="form-group">
            <label for="stock" class="form-label required">Stock Actual</label>
            <input 
                type="number" 
                class="form-control" 
                id="stock" 
                name="stock" 
                value="<?= htmlspecialchars($old['stock'] ?? $insumo['stock']) ?>" 
                required
                min="0"
                step="0.01">
            <small class="form-help">Cantidad actual en inventario. Si cambia, se registrará un movimiento automático.</small>
            <?php if (isset($errors['stock'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['stock']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Stock mínimo -->
        <div class="form-group">
            <label for="stock_minimo" class="form-label required">Stock Mínimo</label>
            <input 
                type="number" 
                class="form-control" 
                id="stock_minimo" 
                name="stock_minimo" 
                value="<?= htmlspecialchars($old['stock_minimo'] ?? $insumo['stock_minimo']) ?>" 
                required
                min="0"
                step="0.01">
            <small class="form-help">Nivel mínimo de alerta de stock</small>
            <?php if (isset($errors['stock_minimo'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['stock_minimo']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Unidad de medida -->
        <div class="form-group">
            <label for="unidad_medida" class="form-label required">Unidad de Medida</label>
            <input 
                type="text" 
                class="form-control" 
                id="unidad_medida" 
                name="unidad_medida" 
                value="<?= htmlspecialchars($old['unidad_medida'] ?? $insumo['unidad_medida']) ?>" 
                required
                maxlength="20">
            <?php if (isset($errors['unidad_medida'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['unidad_medida']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Costo unitario -->
        <div class="form-group">
            <label for="costo_unitario" class="form-label">Costo Unitario (Bs.)</label>
            <input 
                type="number" 
                class="form-control" 
                id="costo_unitario" 
                name="costo_unitario" 
                value="<?= htmlspecialchars($old['costo_unitario'] ?? $insumo['costo_unitario']) ?>"
                min="0"
                step="0.01">
            <small class="form-help">Costo por unidad de medida (opcional)</small>
            <?php if (isset($errors['costo_unitario'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['costo_unitario']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Actualizar Insumo</button>
            <a href="index.php?page=insumos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
