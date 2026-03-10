<?php
/**
 * ════════════════════════════════════════
 * VISTA: Editar Cliente
 * ════════════════════════════════════════
 */
?>

<div class="page-header">
    <h2 class="page-title">Editar Cliente</h2>
    <a href="index.php?page=clientes" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form method="POST" action="index.php?page=clientes&action=update" class="form-vertical">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">

        <!-- Nombre -->
        <div class="form-group <?= isset($errors['nombre']) ? 'has-error' : '' ?>">
            <label for="nombre" class="required">Nombre</label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="<?= htmlspecialchars($cliente['nombre']) ?>"
                class="form-control"
                required
            >
            <?php if (isset($errors['nombre'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['nombre']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Apellido -->
        <div class="form-group <?= isset($errors['apellido']) ? 'has-error' : '' ?>">
            <label for="apellido" class="required">Apellido</label>
            <input 
                type="text" 
                id="apellido" 
                name="apellido" 
                value="<?= htmlspecialchars($cliente['apellido']) ?>"
                class="form-control"
                required
            >
            <?php if (isset($errors['apellido'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['apellido']) ?></span>
            <?php endif; ?>
        </div>

        <!-- CI -->
        <div class="form-group <?= isset($errors['ci']) ? 'has-error' : '' ?>">
            <label for="ci" class="required">CI</label>
            <input 
                type="text" 
                id="ci" 
                name="ci" 
                value="<?= htmlspecialchars($cliente['ci']) ?>"
                class="form-control"
                required
            >
            <?php if (isset($errors['ci'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['ci']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
            <label for="email" class="required">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($cliente['email']) ?>"
                class="form-control"
                required
            >
            <?php if (isset($errors['email'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                class="form-control"
            >
        </div>

        <!-- Fecha de Nacimiento -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input 
                type="date" 
                id="fecha_nacimiento" 
                name="fecha_nacimiento" 
                value="<?= htmlspecialchars($cliente['fecha_nacimiento'] ?? '') ?>"
                class="form-control"
            >
        </div>

        <!-- Botones -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
            <a href="index.php?page=clientes" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
