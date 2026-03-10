<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Formulario de Creación de Empleado
 * ════════════════════════════════════════════════
 * RF014 - GESTIÓN DE EMPLEADOS
 */
?>

<style>
    .form-section {
        background: #f8f9fa;
        padding: 20px;
        margin-bottom: 25px;
        border-radius: 6px;
        border-left: 4px solid #007bff;
    }
    .form-section h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
        font-size: 1.1rem;
    }
    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }
    .form-check {
        display: flex;
        align-items: center;
    }
    .form-check-input {
        margin-right: 8px;
    }
    #seccionUsuario {
        display: none;
    }
    #seccionUsuario.visible {
        display: block;
    }
</style>

<div class="page-header">
    <h2 class="page-title">Nuevo Empleado</h2>
    <a href="index.php?page=empleados" class="btn btn-secondary">← Volver al Listado</a>
</div>

<div class="form-container">
    <form id="formEmpleado" action="index.php?page=empleados&action=store" method="POST" class="form">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- ═══════════════════════════════════════ -->
        <!-- SECCIÓN 1: DATOS DEL EMPLEADO -->
        <!-- ═══════════════════════════════════════ -->
        <div class="form-section">
            <h3>📋 Datos del Empleado</h3>

            <!-- Nombre -->
            <div class="form-group">
                <label for="nombre" class="form-label required">Nombre</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="nombre" 
                    name="nombre" 
                    value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                    required
                    maxlength="50">
                <?php if (isset($errors['nombre'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['nombre']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Apellido -->
            <div class="form-group">
                <label for="apellido" class="form-label required">Apellido</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="apellido" 
                    name="apellido" 
                    value="<?= htmlspecialchars($old['apellido'] ?? '') ?>" 
                    required
                    maxlength="50">
                <?php if (isset($errors['apellido'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['apellido']) ?></div>
                <?php endif; ?>
            </div>

            <!-- CI -->
            <div class="form-group">
                <label for="ci" class="form-label required">Cédula de Identidad</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="ci" 
                    name="ci" 
                    value="<?= htmlspecialchars($old['ci'] ?? '') ?>" 
                    required
                    maxlength="20">
                <?php if (isset($errors['ci'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['ci']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    maxlength="100">
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="telefono" 
                    name="telefono" 
                    value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                    maxlength="20">
                <?php if (isset($errors['telefono'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['telefono']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Cargo -->
            <div class="form-group">
                <label for="cargo" class="form-label required">Cargo</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="cargo" 
                    name="cargo" 
                    value="<?= htmlspecialchars($old['cargo'] ?? '') ?>" 
                    required
                    maxlength="50"
                    placeholder="Ej: Terapeuta, Recepcionista, Masajista">
                <?php if (isset($errors['cargo'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['cargo']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Fecha de contratación -->
            <div class="form-group">
                <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="fecha_contratacion" 
                    name="fecha_contratacion" 
                    value="<?= htmlspecialchars($old['fecha_contratacion'] ?? date('Y-m-d')) ?>">
                <small class="form-help">Si no se especifica, se usará la fecha actual</small>
            </div>
        </div>

        <!-- ═══════════════════════════════════════ -->
        <!-- SECCIÓN 2: ESPECIALIDADES -->
        <!-- ═══════════════════════════════════════ -->
        <div class="form-section">
            <h3>💆 Especialidades</h3>
            <div class="checkbox-group">
                <?php if (empty($especialidades)): ?>
                    <p style="color: #999;">No hay especialidades disponibles</p>
                <?php else: ?>
                    <?php foreach ($especialidades as $esp): ?>
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="esp_<?= $esp['id'] ?>" 
                                name="especialidades[]" 
                                value="<?= $esp['id'] ?>"
                                <?= in_array($esp['id'], $old['especialidades'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="esp_<?= $esp['id'] ?>">
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <small class="form-help">Puede seleccionar múltiples especialidades</small>
        </div>

        <!-- ═══════════════════════════════════════ -->
        <!-- SECCIÓN 3: CREAR USUARIO (OPCIONAL) -->
        <!-- ═══════════════════════════════════════ -->
        <div class="form-section">
            <h3>👤 Crear Acceso al Sistema</h3>
            
            <div class="form-check">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="crear_usuario" 
                    name="crear_usuario" 
                    value="1"
                    <?= isset($old['crear_usuario']) ? 'checked' : '' ?>
                    onchange="toggleSeccionUsuario(this)">
                <label class="form-check-label" for="crear_usuario">
                    <strong>Crear acceso al sistema para este empleado</strong>
                </label>
            </div>

            <div id="seccionUsuario" class="<?= isset($old['crear_usuario']) ? 'visible' : '' ?>">
                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label required">Username</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                        maxlength="50">
                    <?php if (isset($errors['username'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['username']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label required">Contraseña</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password"
                        minlength="8">
                    <small class="form-help">Mínimo 8 caracteres</small>
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Confirmar Password -->
                <div class="form-group">
                    <label for="password_confirm" class="form-label required">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirm" 
                        name="password_confirm"
                        minlength="8">
                    <?php if (isset($errors['password_confirm'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['password_confirm']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Rol -->
                <div class="form-group">
                    <label for="id_rol" class="form-label required">Rol</label>
                    <select class="form-control" id="id_rol" name="id_rol">
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option 
                                value="<?= $rol['id'] ?>"
                                <?= (($old['id_rol'] ?? '') == $rol['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_rol'])): ?>
                        <div class="form-error"><?= htmlspecialchars($errors['id_rol']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Empleado</button>
            <a href="index.php?page=empleados" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
function toggleSeccionUsuario(checkbox) {
    const seccion = document.getElementById('seccionUsuario');
    if (checkbox.checked) {
        seccion.classList.add('visible');
        // Hacer campos requeridos
        document.getElementById('username').setAttribute('required', 'required');
        document.getElementById('password').setAttribute('required', 'required');
        document.getElementById('password_confirm').setAttribute('required', 'required');
        document.getElementById('id_rol').setAttribute('required', 'required');
    } else {
        seccion.classList.remove('visible');
        // Quitar campos requeridos
        document.getElementById('username').removeAttribute('required');
        document.getElementById('password').removeAttribute('required');
        document.getElementById('password_confirm').removeAttribute('required');
        document.getElementById('id_rol').removeAttribute('required');
    }
}
</script>
