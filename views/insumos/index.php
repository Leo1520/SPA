<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Listado de Insumos
 * ════════════════════════════════════════════════
 * RF009 - GESTIÓN DE INSUMOS
 */
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Insumos</h2>
    <a href="index.php?page=insumos&action=create" class="btn btn-primary">+ Nuevo Insumo</a>
</div>

<!-- Tabla de insumos -->
<div class="table-container">
    <?php if (empty($insumos)): ?>
        <div class="empty-state">
            <p>No se encontraron insumos</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Unidad</th>
                    <th>Costo Unitario (Bs.)</th>
                    <th>Alerta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($insumos as $insumo): ?>
                    <?php 
                    $stockBajo = floatval($insumo['stock']) <= floatval($insumo['stock_minimo']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($insumo['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($insumo['nombre']) ?></strong>
                            <?php if ($insumo['descripcion']): ?>
                                <br><small style="color: #666;"><?= htmlspecialchars(substr($insumo['descripcion'], 0, 50)) ?><?= strlen($insumo['descripcion']) > 50 ? '...' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($insumo['stock'], 2) ?></td>
                        <td><?= number_format($insumo['stock_minimo'], 2) ?></td>
                        <td><?= htmlspecialchars($insumo['unidad_medida']) ?></td>
                        <td>Bs. <?= number_format($insumo['costo_unitario'], 2) ?></td>
                        <td>
                            <?php if ($stockBajo): ?>
                                <span class="badge badge-danger">⚠ Stock bajo</span>
                            <?php else: ?>
                                <span class="badge badge-success">OK</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <!-- Botón Editar -->
                            <a href="index.php?page=insumos&action=edit&id=<?= $insumo['id'] ?>" 
                               class="btn-action btn-edit"
                               title="Editar">
                                ✏️ Editar
                            </a>

                            <!-- Botón Eliminar -->
                            <a href="index.php?page=insumos&action=delete&id=<?= $insumo['id'] ?>" 
                               class="btn-action btn-delete"
                               title="Eliminar"
                               onclick="return confirm('¿Está seguro de eliminar este insumo? Esta acción eliminará también sus movimientos de inventario.')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
