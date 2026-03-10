<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Reporte Diario de Reservas
 * ════════════════════════════════════════════════
 * RF015 - Reporte de reservas por fecha
 * 
 * Variables disponibles:
 * @var string $fecha - Fecha consultada
 * @var array $reservas - Lista de reservas del día
 */

// Helper para obtener clase CSS del badge según estado
function getEstadoBadgeClass($estado) {
    $badges = [
        'Pendiente'   => 'badge-pendiente',
        'Confirmada'  => 'badge-confirmada',
        'Completada'  => 'badge-completada',
        'Cancelada'   => 'badge-cancelada',
    ];
    return $badges[$estado] ?? 'badge-secondary';
}
?>

<style>
    /* Badges personalizados por estado */
    .badge-pendiente {
        background-color: #FFF3CD;
        color: #856404;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .badge-confirmada {
        background-color: #CCE5FF;
        color: #004085;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .badge-completada {
        background-color: #D4EDDA;
        color: #155724;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .badge-cancelada {
        background-color: #F8D7DA;
        color: #721C24;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Estilos de impresión */
    @media print {
        .sidebar, 
        .topbar, 
        .btn, 
        .nav, 
        form, 
        .btn-print,
        .form-container { 
            display: none !important; 
        }
        body { 
            background: white; 
        }
        .main-content {
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 20px;
        }
        .page-header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .data-table {
            width: 100%;
            font-size: 11pt;
        }
        .data-table th {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="page-header">
    <h2 class="page-title">📋 Reporte Diario de Reservas</h2>
    <button onclick="window.print()" class="btn btn-primary btn-print">🖨️ Imprimir</button>
</div>

<!-- Formulario de filtro -->
<div class="form-container">
    <form method="GET" action="index.php" class="form">
        <input type="hidden" name="page" value="reportes">
        <input type="hidden" name="action" value="reservasDia">
        
        <div class="form-group">
            <label for="fecha" class="form-label">Fecha</label>
            <input 
                type="date" 
                class="form-control" 
                id="fecha" 
                name="fecha" 
                value="<?= htmlspecialchars($fecha) ?>" 
                required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Consultar</button>
        </div>
    </form>
</div>

<!-- Fecha consultada (visible en impresión) -->
<div style="margin: 20px 0; font-size: 1rem;">
    <strong>Fecha consultada:</strong> <?= date('d/m/Y', strtotime($fecha)) ?>
</div>

<!-- Tabla de resultados -->
<div class="table-container">
    <?php if (empty($reservas)): ?>
        <div class="empty-state">
            <p>No hay reservas para esta fecha</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Terapeuta</th>
                    <th>Sala</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                        <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                        <td><?= htmlspecialchars($reserva['terapeuta']) ?></td>
                        <td><?= htmlspecialchars($reserva['sala']) ?></td>
                        <td><?= date('H:i', strtotime($reserva['hora_inicio'])) ?></td>
                        <td><?= date('H:i', strtotime($reserva['hora_fin'])) ?></td>
                        <td>
                            <span class="<?= getEstadoBadgeClass($reserva['estado']) ?>">
                                <?= htmlspecialchars($reserva['estado']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Resumen -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
            <strong>Total de reservas:</strong> <?= count($reservas) ?>
        </div>
    <?php endif; ?>
</div>
