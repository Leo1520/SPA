<?php
/**
 * ════════════════════════════════════════════════
 * VISTA: Listado de Salas
 * ════════════════════════════════════════════════
 * RF008 - GESTIÓN DE SALAS
 */
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Salas</h2>
    <a href="index.php?page=salas&action=create" class="btn btn-primary">+ Nueva Sala</a>
</div>

<!-- Tabla de salas -->
<div class="table-container">
    <?php if (empty($salas)): ?>
        <div class="empty-state">
            <p>No se encontraron salas</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Capacidad</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salas as $sala): ?>
                    <tr>
                        <td><?= htmlspecialchars($sala['id']) ?></td>
                        <td><strong><?= htmlspecialchars($sala['nombre']) ?></strong></td>
                        <td>
                            <?= $sala['capacidad'] ? htmlspecialchars($sala['capacidad']) . ' persona(s)' : '<span style="color: #999;">No especificada</span>' ?>
                        </td>
                        <td><?= $sala['ubicacion'] ? htmlspecialchars($sala['ubicacion']) : '<span style="color: #999;">No especificada</span>' ?></td>
                        <td class="table-actions">
                            <!-- Botón Editar -->
                            <a href="index.php?page=salas&action=edit&id=<?= $sala['id'] ?>" 
                               class="btn-action btn-edit"
                               title="Editar">
                                ✏️ Editar
                            </a>

                            <!-- Botón Eliminar -->
                            <a href="index.php?page=salas&action=delete&id=<?= $sala['id'] ?>" 
                               class="btn-action btn-delete"
                               title="Eliminar"
                               onclick="return confirm('¿Está seguro de eliminar esta sala?')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
