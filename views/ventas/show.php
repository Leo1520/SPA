<?php
/**
 * ════════════════════════════════════════
 * VISTA: Detalle de Venta con Pagos
 * ════════════════════════════════════════
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */
?>

<div class="page-header">
    <h2 class="page-title">Detalle de Venta #<?= htmlspecialchars($venta['id']) ?></h2>
    <a href="index.php?page=ventas" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <!-- Información de la Venta -->
    <div class="form-section">
        <h3 class="section-title">Información General</h3>
        
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?>
            </div>
            <div>
                <strong>Email:</strong> <?= htmlspecialchars($venta['cliente_email'] ?? 'N/A') ?>
            </div>
            <div>
                <strong>Teléfono:</strong> <?= htmlspecialchars($venta['cliente_telefono'] ?? 'N/A') ?>
            </div>
            <div>
                <strong>Fecha de Venta:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?>
            </div>
            <div>
                <strong>Reserva #:</strong> <?= htmlspecialchars($venta['reserva_id']) ?>
            </div>
            <div>
                <strong>Fecha de Servicio:</strong> <?= date('d/m/Y', strtotime($venta['fecha_reserva'])) ?>
            </div>
        </div>
    </div>

    <!-- Servicios Vendidos -->
    <div class="form-section">
        <h3 class="section-title">Servicios</h3>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Terapeuta</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?= htmlspecialchars($detalle['servicio_nombre']) ?></td>
                        <td><?= htmlspecialchars($detalle['empleado_nombre'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($detalle['cantidad']) ?></td>
                        <td>Bs. <?= number_format($detalle['precio_unitario'], 2) ?></td>
                        <td>Bs. <?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>TOTAL:</strong></td>
                    <td><strong>Bs. <?= number_format($venta['total'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Resumen Financiero -->
    <div class="form-section">
        <h3 class="section-title">Resumen Financiero</h3>
        
        <div class="info-box" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
            <div style="display: grid; gap: 0.5rem; font-size: 1.1em;">
                <div style="display: flex; justify-content: space-between;">
                    <strong>Total Venta:</strong>
                    <span>Bs. <?= number_format($venta['total'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #28a745;">
                    <strong>Total Pagado:</strong>
                    <span>Bs. <?= number_format($totalPagado, 2) ?></span>
                </div>
                <hr style="margin: 0.5rem 0;">
                <div style="display: flex; justify-content: space-between; font-size: 1.2em; <?= $saldoPendiente > 0 ? 'color: #dc3545;' : 'color: #28a745;' ?>">
                    <strong>Saldo Pendiente:</strong>
                    <span><strong>Bs. <?= number_format($saldoPendiente, 2) ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Pagos -->
    <div class="form-section">
        <h3 class="section-title">Historial de Pagos</h3>
        
        <?php if (empty($pagos)): ?>
            <p style="color: #666; font-style: italic;">No se han registrado pagos para esta venta</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                            <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                            <td>Bs. <?= number_format($pago['monto'], 2) ?></td>
                            <td><?= htmlspecialchars($pago['referencia'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Formulario para Registrar Nuevo Pago (solo si hay saldo pendiente y es Cajero/Admin) -->
    <?php if ($saldoPendiente > 0 && in_array($_SESSION['id_rol'], [1, 3])): ?>
        <div class="form-section" style="border: 2px solid #28a745; padding: 1.5rem; border-radius: 8px;">
            <h3 class="section-title" style="color: #28a745;">Registrar Nuevo Pago</h3>
            
            <form method="POST" action="index.php?page=ventas&action=storePago" class="form-vertical">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="id_venta" value="<?= htmlspecialchars($venta['id']) ?>">

                <div class="form-group">
                    <label for="id_metodo_pago" class="required">Método de Pago</label>
                    <select id="id_metodo_pago" name="id_metodo_pago" class="form-control" required>
                        <option value="">Seleccione un método</option>
                        <?php foreach ($metodosPago as $metodo): ?>
                            <option value="<?= $metodo['id'] ?>"><?= htmlspecialchars($metodo['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="monto" class="required">Monto (Bs.)</label>
                    <input 
                        type="number" 
                        id="monto" 
                        name="monto" 
                        step="0.01"
                        min="0.01"
                        max="<?= $saldoPendiente ?>"
                        value="<?= $saldoPendiente ?>"
                        class="form-control"
                        required
                    >
                    <small style="color: #666;">Máximo: Bs. <?= number_format($saldoPendiente, 2) ?></small>
                </div>

                <div class="form-group">
                    <label for="referencia">Referencia / Comprobante (Opcional)</label>
                    <input 
                        type="text" 
                        id="referencia" 
                        name="referencia" 
                        class="form-control"
                        placeholder="Número de transacción, cheque, etc."
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💰 Registrar Pago</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Botón para Emitir Factura (solo si está totalmente pagada y no tiene factura) -->
    <?php if ($saldoPendiente == 0 && !$tieneFactura && in_array($_SESSION['id_rol'], [1, 3])): ?>
        <div class="form-section" style="background: #d1ecf1; padding: 1.5rem; border-radius: 8px; border: 1px solid #bee5eb;">
            <p style="margin: 0 0 1rem 0; color: #0c5460;">
                <strong>✓ Venta pagada completamente.</strong> Puede emitir la factura ahora.
            </p>
            <a href="index.php?page=ventas&action=emitirFactura&id_venta=<?= $venta['id'] ?>" class="btn btn-primary">
                📄 Emitir Factura
            </a>
        </div>
    <?php endif; ?>

    <!-- Mostrar enlace a factura si ya existe -->
    <?php if ($tieneFactura): ?>
        <div class="form-section" style="background: #d4edda; padding: 1.5rem; border-radius: 8px; border: 1px solid #c3e6cb;">
            <p style="margin: 0 0 1rem 0; color: #155724;">
                <strong>✓ Factura emitida.</strong>
            </p>
            <a href="index.php?page=ventas&action=factura&id=<?= $factura['id'] ?>" class="btn btn-success" target="_blank">
                📄 Ver/Imprimir Factura
            </a>
        </div>
    <?php endif; ?>
</div>
