<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Formulario de Creación de Insumo
 * ════════════════════════════════════════════════
 * RF009 - GESTIÓN DE INSUMOS
 */
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Insumo</h2>
    <a href="index.php?page=insumos" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formInsumo" action="index.php?page=insumos&action=store" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" class="form-label required">Nombre del Insumo</label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                required
                maxlength="100"
                placeholder="Ej: Aceite de masaje, Toallas, Crema facial">
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
                maxlength="500"
                placeholder="Descripción opcional del insumo"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
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
                value="<?= htmlspecialchars($old['stock'] ?? '0') ?>" 
                required
                min="0"
                step="0.01"
                placeholder="0.00">
            <small class="form-help">Cantidad actual en inventario</small>
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
                value="<?= htmlspecialchars($old['stock_minimo'] ?? '0') ?>" 
                required
                min="0"
                step="0.01"
                placeholder="0.00">
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
                value="<?= htmlspecialchars($old['unidad_medida'] ?? '') ?>" 
                required
                maxlength="20"
                placeholder="Ej: ml, g, unidad, litros, kg">
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
                value="<?= htmlspecialchars($old['costo_unitario'] ?? '0') ?>"
                min="0"
                step="0.01"
                placeholder="0.00">
            <small class="form-help">Costo por unidad de medida (opcional)</small>
            <?php if (isset($errors['costo_unitario'])): ?>
                <div class="form-error"><?= htmlspecialchars($errors['costo_unitario']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Insumo</button>
            <a href="index.php?page=insumos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
