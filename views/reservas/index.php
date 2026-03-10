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
                    <th>Acciones</th>
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
                        <td class="table-actions">
                            <?php
                            $estado = $reserva['estado'];
                            $idReserva = $reserva['id'];
                            $idRol = $_SESSION['id_rol'];
                            
                            // Botón Confirmar (solo si está Pendiente)
                            if ($estado === 'Pendiente' && in_array($idRol, [1, 2])): ?>
                                <a href="index.php?page=reservas&action=updateEstado&id=<?= $idReserva ?>&estado=Confirmada" 
                                   class="btn-action btn-primary" 
                                   title="Confirmar reserva"
                                   onclick="return confirm('¿Confirmar esta reserva?')">
                                    ✓ Confirmar
                                </a>
                            <?php endif;
                            
                            // Botón Completar (solo si está Confirmada)
                            if ($estado === 'Confirmada' && in_array($idRol, [1, 2, 4])): ?>
                                <a href="index.php?page=reservas&action=updateEstado&id=<?= $idReserva ?>&estado=Completada" 
                                   class="btn-action btn-success" 
                                   title="Marcar como completada"
                                   onclick="return confirm('¿Marcar esta reserva como completada?')">
                                    ✓ Completar
                                </a>
                            <?php endif;
                            
                            // Botón Generar Venta (solo si está Completada y no tiene venta)
                            if ($estado === 'Completada' && !$reserva['tiene_venta'] && in_array($idRol, [1, 2, 3])): ?>
                                <a href="index.php?page=ventas&action=create&id_reserva=<?= $idReserva ?>" 
                                   class="btn-action btn-primary" 
                                   title="Generar venta"
                                   style="background: #28a745;">
                                    💰 Generar Venta
                                </a>
                            <?php endif;
                            
                            // Botón Cancelar (solo si está Pendiente o Confirmada)
                            if (in_array($estado, ['Pendiente', 'Confirmada']) && in_array($idRol, [1, 2])): ?>
                                <a href="javascript:void(0)" 
                                   class="btn-action btn-warning" 
                                   title="Cancelar reserva"
                                   onclick="cancelarReserva(<?= $idReserva ?>)">
                                    ✗ Cancelar
                                </a>
                            <?php endif;
                            
                            // Botón Eliminar (solo Admin y solo si no está Completada)
                            if ($idRol === 1 && $estado !== 'Completada'): ?>
                                <a href="index.php?page=reservas&action=delete&id=<?= $idReserva ?>" 
                                   class="btn-action btn-delete" 
                                   title="Eliminar reserva"
                                   onclick="return confirm('¿Está seguro de eliminar esta reserva? Esta acción no se puede deshacer.')">
                                    🗑️
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
// Función para cancelar reserva con motivo
function cancelarReserva(idReserva) {
    const motivo = prompt('Ingrese el motivo de cancelación:');
    
    if (motivo === null) {
        // Usuario canceló el prompt
        return;
    }
    
    if (motivo.trim() === '') {
        alert('Debe ingresar un motivo para cancelar la reserva');
        return;
    }
    
    // Redirigir con el motivo
    window.location.href = `index.php?page=reservas&action=updateEstado&id=${idReserva}&estado=Cancelada&motivo=${encodeURIComponent(motivo)}`;
}
</script>
