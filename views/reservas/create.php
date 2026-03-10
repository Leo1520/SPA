<?php
/**
 * ════════════════════════════════════════
 * VISTA: Crear Reserva
 * ════════════════════════════════════════
 */
?>

<div class="page-header">
    <h2 class="page-title">Nueva Reserva</h2>
    <a href="index.php?page=reservas" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form method="POST" action="index.php?page=reservas&action=store" class="form-vertical" id="reservaForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- ═══════════════════════════════════════ -->
        <!-- PASO 1: Selección de Cliente y Fecha -->
        <!-- ═══════════════════════════════════════ -->
        <div class="form-section">
            <h3 class="section-title">Datos de la Reserva</h3>

            <!-- Cliente -->
            <div class="form-group <?= isset($errors['id_cliente']) ? 'has-error' : '' ?>">
                <label for="id_cliente" class="required">Cliente</label>
                <select id="id_cliente" name="id_cliente" class="form-control" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option 
                            value="<?= $cliente['id'] ?>"
                            <?= (isset($old['id_cliente']) && $old['id_cliente'] == $cliente['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido'] . ' - ' . $cliente['ci']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['id_cliente'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['id_cliente']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Fecha -->
            <div class="form-group <?= isset($errors['fecha']) ? 'has-error' : '' ?>">
                <label for="fecha" class="required">Fecha de la Reserva</label>
                <input 
                    type="date" 
                    id="fecha" 
                    name="fecha" 
                    value="<?= htmlspecialchars($old['fecha'] ?? date('Y-m-d')) ?>"
                    class="form-control"
                    required
                >
                <?php if (isset($errors['fecha'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['fecha']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ═══════════════════════════════════════ -->
        <!-- PASO 2: Servicios -->
        <!-- ═══════════════════════════════════════ -->
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">Servicios</h3>
                <button type="button" class="btn btn-secondary btn-sm" id="addServicioBtn">+ Agregar Servicio</button>
            </div>

            <?php if (isset($errors['servicios'])): ?>
                <div class="error-message mb-3"><?= htmlspecialchars($errors['servicios']) ?></div>
            <?php endif; ?>

            <div id="serviciosContainer">
                <!-- Los servicios se agregarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear Reserva</button>
            <a href="index.php?page=reservas" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Template para servicios (oculto, se clona con JavaScript) -->
<template id="servicioTemplate">
    <div class="servicio-item">
        <div class="servicio-header">
            <h4>Servicio <span class="servicio-number"></span></h4>
            <button type="button" class="btn-remove" onclick="removeServicio(this)">✕</button>
        </div>
        
        <div class="servicio-grid">
            <!-- Servicio -->
            <div class="form-group">
                <label class="required">Servicio</label>
                <select name="servicios[INDEX][id_servicio]" class="form-control" required>
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= $servicio['id'] ?>">
                            <?= htmlspecialchars($servicio['nombre'] . ' - Bs. ' . number_format($servicio['precio'], 2)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Terapeuta -->
            <div class="form-group">
                <label>Terapeuta</label>
                <select name="servicios[INDEX][id_empleado]" class="form-control">
                    <option value="">Seleccione un terapeuta</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?= $empleado['id'] ?>">
                            <?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sala -->
            <div class="form-group">
                <label>Sala</label>
                <select name="servicios[INDEX][id_sala]" class="form-control">
                    <option value="">Seleccione una sala</option>
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?= $sala['id'] ?>">
                            <?= htmlspecialchars($sala['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Hora Inicio -->
            <div class="form-group">
                <label class="required">Hora Inicio</label>
                <input type="time" name="servicios[INDEX][hora_inicio]" class="form-control" required>
            </div>

            <!-- Hora Fin -->
            <div class="form-group">
                <label class="required">Hora Fin</label>
                <input type="time" name="servicios[INDEX][hora_fin]" class="form-control" required>
            </div>

            <!-- Observaciones -->
            <div class="form-group full-width">
                <label>Observaciones</label>
                <textarea name="servicios[INDEX][observaciones]" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
// Este script maneja la adición dinámica de servicios
// Se movió aquí temporalmente, se reorganizará en app.js
let servicioIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Agregar el primer servicio automáticamente
    addServicio();

    // Event listener para agregar servicio
    document.getElementById('addServicioBtn').addEventListener('click', addServicio);
});

function addServicio() {
    const template = document.getElementById('servicioTemplate');
    const container = document.getElementById('serviciosContainer');
    
    // Clonar template
    const clone = template.content.cloneNode(true);
    
    // Reemplazar INDEX con el índice actual
    const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, servicioIndex);
    
    // Agregar al contenedor
    container.insertAdjacentHTML('beforeend', html);
    
    // Actualizar número de servicio
    const servicioItems = container.querySelectorAll('.servicio-item');
    servicioItems[servicioItems.length - 1].querySelector('.servicio-number').textContent = servicioIndex + 1;
    
    servicioIndex++;
}

function removeServicio(button) {
    const servicioItem = button.closest('.servicio-item');
    const container = document.getElementById('serviciosContainer');
    
    // No permitir eliminar si es el único servicio
    if (container.querySelectorAll('.servicio-item').length <= 1) {
        alert('Debe haber al menos un servicio en la reserva');
        return;
    }
    
    servicioItem.remove();
    
    // Renumerar servicios
    const servicioItems = container.querySelectorAll('.servicio-item');
    servicioItems.forEach((item, index) => {
        item.querySelector('.servicio-number').textContent = index + 1;
    });
}
</script>
