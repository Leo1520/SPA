<?php
/**
 * ════════════════════════════════════════
 * VISTA: Generar Venta desde Reserva
 * ════════════════════════════════════════
 * RF003 - REGISTRAR DETALLE DE VENTA
 */
?>

<div class="page-header">
    <h2 class="page-title">Generar Venta</h2>
    <a href="index.php?page=reservas" class="btn btn-secondary">← Volver a Reservas</a>
</div>

<div class="form-container">
    <!-- Resumen de la Reserva -->
    <div class="form-section">
        <h3 class="section-title">Información de la Reserva</h3>
        
        <div class="info-grid" style="display: grid; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <strong>Reserva #:</strong> <?= htmlspecialchars($reserva['id']) ?>
            </div>
            <div>
                <strong>Cliente:</strong> <?= htmlspecialchars($reserva['cliente_nombre']) ?>
            </div>
            <div>
                <strong>Fecha de Servicio:</strong> <?= date('d/m/Y', strtotime($reserva['fecha'])) ?>
            </div>
            <div>
                <strong>Estado:</strong> 
                <span class="badge badge-success"><?= htmlspecialchars($reserva['estado']) ?></span>
            </div>
        </div>
    </div>

    <!-- Detalle de Servicios -->
    <div class="form-section">
        <h3 class="section-title">Servicios Prestados</h3>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Terapeuta</th>
                    <th>Hora</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detallesReserva as $detalle): ?>
                    <tr>
                        <td><?= htmlspecialchars($detalle['servicio_nombre']) ?></td>
                        <td><?= htmlspecialchars($detalle['empleado_nombre'] ?? 'N/A') ?></td>
                        <td><?= date('H:i', strtotime($detalle['hora_inicio'])) ?> - <?= date('H:i', strtotime($detalle['hora_fin'])) ?></td>
                        <td>Bs. <?= number_format($detalle['precio'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td><strong>Bs. <?= number_format($total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Formulario de Confirmación -->
    <form method="POST" action="index.php?page=ventas&action=store" class="form-vertical">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id_reserva" value="<?= htmlspecialchars($reserva['id']) ?>">

        <div class="form-section">
            <h3 class="section-title">Descuento (Opcional)</h3>
            
            <div class="form-group">
                <label for="descuento">Descuento en Bs.</label>
                <input 
                    type="number" 
                    id="descuento" 
                    name="descuento" 
                    value="0"
                    min="0"
                    max="<?= $total ?>"
                    step="0.01"
                    class="form-control"
                    onchange="calcularTotal()"
                >
            </div>

            <div class="info-box" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                <p style="margin: 0.5rem 0;"><strong>Subtotal:</strong> Bs. <span id="subtotalDisplay"><?= number_format($total, 2) ?></span></p>
                <p style="margin: 0.5rem 0;"><strong>Descuento:</strong> -Bs. <span id="descuentoDisplay">0.00</span></p>
                <hr style="margin: 0.5rem 0;">
                <p style="margin: 0.5rem 0; font-size: 1.2em;"><strong>Total a Pagar:</strong> Bs. <span id="totalDisplay"><?= number_format($total, 2) ?></span></p>
            </div>
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Confirmar Venta</button>
            <a href="index.php?page=reservas" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
const subtotalOriginal = <?= $total ?>;

function calcularTotal() {
    const descuentoInput = document.getElementById('descuento');
    const descuento = parseFloat(descuentoInput.value) || 0;
    
    // Validar que el descuento no sea mayor al subtotal
    if (descuento > subtotalOriginal) {
        descuentoInput.value = subtotalOriginal;
        descuento = subtotalOriginal;
    }
    
    if (descuento < 0) {
        descuentoInput.value = 0;
        descuento = 0;
    }
    
    const total = subtotalOriginal - descuento;
    
    // Actualizar displays
    document.getElementById('descuentoDisplay').textContent = descuento.toFixed(2);
    document.getElementById('totalDisplay').textContent = total.toFixed(2);
}

// Inicializar
calcularTotal();
</script>
