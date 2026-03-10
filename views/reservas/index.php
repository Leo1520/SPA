<?php
/**
 * ════════════════════════════════════════
 * VISTA: Listado de Reservas
 * ════════════════════════════════════════
 */

// Helper para badge de estado
function getEstadoBadge($estado) {
    $badges = [
        'Pendiente' => 'badge-warning',
        'Confirmada' => 'badge-info',
        'Completada' => 'badge-success',
        'Cancelada' => 'badge-danger'
    ];
    return $badges[$estado] ?? 'badge-secondary';
}
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Reservas</h2>
    <a href="index.php?page=reservas&action=create" class="btn btn-primary">+ Nueva Reserva</a>
</div>

<!-- Filtros por estado -->
<div class="filter-box">
    <a href="index.php?page=reservas" class="filter-btn <?= !isset($_GET['estado']) ? 'active' : '' ?>">
        Todas
    </a>
    <a href="index.php?page=reservas&estado=Pendiente" class="filter-btn <?= ($_GET['estado'] ?? '') === 'Pendiente' ? 'active' : '' ?>">
        Pendientes
    </a>
    <a href="index.php?page=reservas&estado=Confirmada" class="filter-btn <?= ($_GET['estado'] ?? '') === 'Confirmada' ? 'active' : '' ?>">
        Confirmadas
    </a>
    <a href="index.php?page=reservas&estado=Completada" class="filter-btn <?= ($_GET['estado'] ?? '') === 'Completada' ? 'active' : '' ?>">
        Completadas
    </a>
    <a href="index.php?page=reservas&estado=Cancelada" class="filter-btn <?= ($_GET['estado'] ?? '') === 'Cancelada' ? 'active' : '' ?>">
        Canceladas
    </a>
</div>

<!-- Tabla de reservas -->
<div class="table-container">
    <?php if (empty($reservas)): ?>
        <div class="empty-state">
            <p>No se encontraron reservas</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Servicios</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['id']) ?></td>
                        <td><?= htmlspecialchars($reserva['cliente_nombre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></td>
                        <td>
                            <span class="badge <?= getEstadoBadge($reserva['estado']) ?>">
                                <?= htmlspecialchars($reserva['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($reserva['cantidad_servicios']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reserva['fecha_registro'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
