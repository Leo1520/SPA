<?php
/**
 * ════════════════════════════════════════
 * VISTA: Listado de Servicios
 * ════════════════════════════════════════
 * RF007 - GESTIÓN DEL CATÁLOGO DE SERVICIOS
 */

// Helper para badge de estado
function getEstadoServicioBadge($activo) {
    return $activo ? 'badge-success' : 'badge-secondary';
}
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Servicios</h2>
    <a href="index.php?page=servicios&action=create" class="btn btn-primary">+ Nuevo Servicio</a>
</div>

<!-- Tabla de servicios -->
<div class="table-container">
    <?php if (empty($servicios)): ?>
        <div class="empty-state">
            <p>No se encontraron servicios</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Duración (min)</th>
                    <th>Precio (Bs.)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $servicio): ?>
                    <tr>
                        <td><?= htmlspecialchars($servicio['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($servicio['nombre']) ?></strong>
                            <?php if ($servicio['descripcion']): ?>
                                <br>
                                <small style="color: #666;"><?= htmlspecialchars(substr($servicio['descripcion'], 0, 60)) ?><?= strlen($servicio['descripcion']) > 60 ? '...' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($servicio['duracion_minutos']) ?></td>
                        <td>Bs. <?= number_format($servicio['precio'], 2) ?></td>
                        <td>
                            <span class="badge <?= getEstadoServicioBadge($servicio['activo']) ?>">
                                <?= $servicio['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <!-- Botón Editar -->
                            <a href="index.php?page=servicios&action=edit&id=<?= $servicio['id'] ?>" 
                               class="btn-action btn-edit"
                               title="Editar">
                                ✏️ Editar
                            </a>

                            <!-- Botón Activar/Desactivar -->
                            <a href="index.php?page=servicios&action=toggle&id=<?= $servicio['id'] ?>" 
                               class="btn-action <?= $servicio['activo'] ? 'btn-warning' : 'btn-success' ?>"
                               title="<?= $servicio['activo'] ? 'Desactivar' : 'Activar' ?>"
                               onclick="return confirm('¿Está seguro de <?= $servicio['activo'] ? 'desactivar' : 'activar' ?> este servicio?')">
                                <?= $servicio['activo'] ? '⊗ Desactivar' : '✓ Activar' ?>
                            </a>

                            <!-- Botón Eliminar -->
                            <a href="index.php?page=servicios&action=delete&id=<?= $servicio['id'] ?>" 
                               class="btn-action btn-delete"
                               title="Eliminar"
                               onclick="return confirm('¿Está seguro de eliminar este servicio? Esta acción eliminará también sus relaciones con insumos.')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
