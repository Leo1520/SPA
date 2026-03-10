<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Listado de Empleados
 * ════════════════════════════════════════════════
 * RF014 - GESTIÓN DE EMPLEADOS
 */
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Empleados</h2>
    <a href="index.php?page=empleados&action=create" class="btn btn-primary">+ Nuevo Empleado</a>
</div>

<!-- Tabla de empleados -->
<div class="table-container">
    <?php if (empty($empleados)): ?>
        <div class="empty-state">
            <p>No se encontraron empleados</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Cargo</th>
                    <th>Especialidades</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                    <tr>
                        <td><?= htmlspecialchars($empleado['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?></strong>
                            <?php if ($empleado['email']): ?>
                                <br><small style="color: #666;"><?= htmlspecialchars($empleado['email']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($empleado['ci']) ?></td>
                        <td><?= htmlspecialchars($empleado['cargo']) ?></td>
                        <td>
                            <?php if (!empty($empleado['especialidades'])): ?>
                                <?php 
                                $especialidades = explode(', ', $empleado['especialidades']);
                                foreach ($especialidades as $esp): 
                                ?>
                                    <span class="badge badge-success" style="margin: 2px;"><?= htmlspecialchars($esp) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span style="color: #999;">Sin especialidades</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $empleado['activo'] ? 'badge-success' : 'badge-secondary' ?>">
                                <?= $empleado['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <!-- Botón Editar -->
                            <a href="index.php?page=empleados&action=edit&id=<?= $empleado['id'] ?>" 
                               class="btn-action btn-edit"
                               title="Editar">
                                ✏️ Editar
                            </a>

                            <!-- Botón Activar/Desactivar -->
                            <a href="index.php?page=empleados&action=toggle&id=<?= $empleado['id'] ?>" 
                               class="btn-action <?= $empleado['activo'] ? 'btn-warning' : 'btn-success' ?>"
                               title="<?= $empleado['activo'] ? 'Desactivar' : 'Activar' ?>"
                               onclick="return confirm('¿Está seguro de <?= $empleado['activo'] ? 'desactivar' : 'activar' ?> este empleado?')">
                                <?= $empleado['activo'] ? '⊗ Desactivar' : '✓ Activar' ?>
                            </a>

                            <!-- Usuario asociado -->
                            <?php if (!empty($empleado['username'])): ?>
                                <span class="badge badge-info" title="Usuario: <?= htmlspecialchars($empleado['username']) ?>">
                                    👤 <?= htmlspecialchars($empleado['username']) ?>
                                </span>
                            <?php else: ?>
                                <a href="index.php?page=empleados&action=edit&id=<?= $empleado['id'] ?>" 
                                   class="btn-action btn-secondary"
                                   title="Crear usuario">
                                    + Usuario
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
