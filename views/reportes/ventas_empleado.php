<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Reporte de Ventas por Empleado
 * ════════════════════════════════════════════════
 * RF016 - Servicios prestados por empleado en rango de fechas
 * 
 * Variables disponibles:
 * @var array $empleados - Lista de empleados activos para select
 * @var array $resultados - Servicios prestados por el empleado
 * @var float $totalPrecio - Total de servicios completados
 * @var string $empleadoSeleccionado - Nombre del empleado consultado
 * @var string $fechaDesde - Fecha inicial del rango
 * @var string $fechaHasta - Fecha final del rango
 * @var string $idEmpleado - ID del empleado seleccionado
 */
?>

<style>
    /* Formulario en una sola fila */
    .form-filters {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    .form-filters .form-group {
        margin-bottom: 0;
        flex: 1;
        min-width: 200px;
    }
    .form-filters .form-actions {
        margin-top: 0;
    }

    /* Fila de total destacada */
    .row-total {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .row-total td {
        padding: 12px !important;
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
        .row-total {
            background-color: #e9ecef !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .reporte-info {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #333;
        }
    }
</style>

<div class="page-header">
    <h2 class="page-title">📊 Reporte de Servicios por Empleado</h2>
    <button onclick="window.print()" class="btn btn-primary btn-print">🖨️ Imprimir</button>
</div>

<!-- Formulario de filtros -->
<div class="form-container">
    <form method="GET" action="index.php" class="form">
        <input type="hidden" name="page" value="reportes">
        <input type="hidden" name="action" value="ventasEmpleado">
        
        <div class="form-filters">
            <!-- Select de empleado -->
            <div class="form-group">
                <label for="id_empleado" class="form-label">Empleado</label>
                <select 
                    class="form-control" 
                    id="id_empleado" 
                    name="id_empleado" 
                    required>
                    <option value="">Seleccione un empleado</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option 
                            value="<?= htmlspecialchars($empleado['id']) ?>"
                            <?= ($idEmpleado == $empleado['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($empleado['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Fecha desde -->
            <div class="form-group">
                <label for="desde" class="form-label">Desde</label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="desde" 
                    name="desde" 
                    value="<?= htmlspecialchars($fechaDesde) ?>" 
                    required>
            </div>
            
            <!-- Fecha hasta -->
            <div class="form-group">
                <label for="hasta" class="form-label">Hasta</label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="hasta" 
                    name="hasta" 
                    value="<?= htmlspecialchars($fechaHasta) ?>" 
                    required>
            </div>
            
            <!-- Botón consultar -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Consultar</button>
            </div>
        </div>
    </form>
</div>

<!-- Información del reporte (visible en impresión) -->
<?php if (!empty($empleadoSeleccionado)): ?>
<div class="reporte-info" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 4px;">
    <div><strong>Empleado:</strong> <?= htmlspecialchars($empleadoSeleccionado) ?></div>
    <div><strong>Período:</strong> <?= date('d/m/Y', strtotime($fechaDesde)) ?> al <?= date('d/m/Y', strtotime($fechaHasta)) ?></div>
</div>
<?php endif; ?>

<!-- Tabla de resultados -->
<div class="table-container">
    <?php if (empty($resultados)): ?>
        <?php if (!empty($idEmpleado)): ?>
            <div class="empty-state">
                <p>No se encontraron servicios para los filtros seleccionados</p>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Seleccione un empleado y rango de fechas para generar el reporte</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Servicio Prestado</th>
                    <th>Precio (Bs.)</th>
                    <th>Estado Reserva</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($resultado['fecha'])) ?></td>
                        <td><?= htmlspecialchars($resultado['cliente']) ?></td>
                        <td><?= htmlspecialchars($resultado['servicio']) ?></td>
                        <td>Bs. <?= number_format($resultado['precio'], 2) ?></td>
                        <td>
                            <span class="badge <?= $resultado['estado'] === 'Completada' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= htmlspecialchars($resultado['estado']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Fila de TOTAL -->
                <tr class="row-total">
                    <td colspan="3" style="text-align: right;">
                        TOTAL SERVICIOS PRESTADOS:
                    </td>
                    <td colspan="2">
                        Bs. <?= number_format($totalPrecio, 2) ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Resumen adicional -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
            <div><strong>Total de servicios:</strong> <?= count($resultados) ?></div>
            <div><strong>Servicios completados:</strong> <?= count(array_filter($resultados, fn($r) => $r['estado'] === 'Completada')) ?></div>
        </div>
    <?php endif; ?>
</div>
