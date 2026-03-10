<?php
/**
 * ════════════════════════════════════════
 * VISTA: Listado de Ventas
 * ════════════════════════════════════════
 * RF003 - REGISTRAR DETALLE DE VENTA
 */

// Helper para estado de pago
function getEstadoPagoBadge($estadoPago) {
    return $estadoPago === 'Pagada' ? 'badge-success' : 'badge-warning';
}
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Ventas</h2>
</div>

<!-- Filtros por fecha -->
<div class="filter-box">
    <form method="GET" action="index.php" style="display: flex; gap: 1rem; align-items: flex-end;">
        <input type="hidden" name="page" value="ventas">
        
        <div class="form-group" style="margin: 0;">
            <label for="fecha_desde">Fecha Desde</label>
            <input 
                type="date" 
                id="fecha_desde" 
                name="fecha_desde" 
                value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>"
                class="form-control"
            >
        </div>
        
        <div class="form-group" style="margin: 0;">
            <label for="fecha_hasta">Fecha Hasta</label>
            <input 
                type="date" 
                id="fecha_hasta" 
                name="fecha_hasta" 
                value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>"
                class="form-control"
            >
        </div>
        
        <button type="submit" class="btn btn-secondary">Filtrar</button>
        
        <?php if (isset($_GET['fecha_desde']) || isset($_GET['fecha_hasta'])): ?>
            <a href="index.php?page=ventas" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de ventas -->
<div class="table-container">
    <?php if (empty($ventas)): ?>
        <div class="empty-state">
            <p>No se encontraron ventas</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Reserva #</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['id']) ?></td>
                        <td><?= htmlspecialchars($venta['cliente_nombre']) ?></td>
                        <td><?= htmlspecialchars($venta['id_reserva']) ?></td>
                        <td><?= date('d/m/Y', strtotime($venta['fecha'])) ?></td>
                        <td>Bs. <?= number_format($venta['total'], 2) ?></td>
                        <td>Bs. <?= number_format($venta['total_pagado'], 2) ?></td>
                        <td>
                            <span class="badge <?= getEstadoPagoBadge($venta['estado_pago']) ?>">
                                <?= htmlspecialchars($venta['estado_pago']) ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a href="index.php?page=ventas&action=show&id=<?= $venta['id'] ?>" 
                               class="btn-action btn-primary" 
                               title="Ver detalle">
                                👁️ Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
