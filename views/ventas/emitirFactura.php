<?php
/**
 * ════════════════════════════════════════
 * VISTA: Emitir Factura
 * ════════════════════════════════════════
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */
?>

<div class="page-header">
    <h2 class="page-title">Emitir Factura</h2>
    <a href="index.php?page=ventas&action=show&id=<?= htmlspecialchars($venta['id']) ?>" class="btn btn-secondary">← Volver a Detalle de Venta</a>
</div>

<div class="form-container">
    <!-- Información de la Venta -->
    <div class="form-section">
        <h3 class="section-title">Datos de la Venta</h3>
        
        <div class="info-grid" style="display: grid; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <strong>Venta #:</strong> <?= htmlspecialchars($venta['id']) ?>
            </div>
            <div>
                <strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?>
            </div>
            <div>
                <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($venta['fecha'])) ?>
            </div>
            <div>
                <strong>Total:</strong> Bs. <?= number_format($venta['total'], 2) ?>
            </div>
            <div style="color: #28a745;">
                <strong>Estado:</strong> ✓ Pagada Completamente
            </div>
        </div>
    </div>

    <!-- Formulario de Factura -->
    <form method="POST" action="index.php?page=ventas&action=storeFactura" class="form-vertical">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id_venta" value="<?= htmlspecialchars($venta['id']) ?>">

        <div class="form-section">
            <h3 class="section-title">Datos de Facturación</h3>

            <div class="form-group <?= isset($errors['nit']) ? 'has-error' : '' ?>">
                <label for="nit" class="required">NIT</label>
                <input 
                    type="text" 
                    id="nit" 
                    name="nit" 
                    value="<?= htmlspecialchars($old['nit'] ?? '') ?>"
                    class="form-control"
                    placeholder="Ingrese el NIT"
                    required
                >
                <?php if (isset($errors['nit'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['nit']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['razon_social']) ? 'has-error' : '' ?>">
                <label for="razon_social" class="required">Razón Social / Nombre</label>
                <input 
                    type="text" 
                    id="razon_social" 
                    name="razon_social" 
                    value="<?= htmlspecialchars($old['razon_social'] ?? $venta['cliente_nombre']) ?>"
                    class="form-control"
                    placeholder="Nombre completo o razón social"
                    required
                >
                <?php if (isset($errors['razon_social'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['razon_social']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">📄 Emitir Factura</button>
            <a href="index.php?page=ventas&action=show&id=<?= htmlspecialchars($venta['id']) ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
