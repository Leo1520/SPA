<?php
/**
 * ════════════════════════════════════════
 * VISTA: Listado de Clientes
 * ════════════════════════════════════════
 */
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Clientes</h2>
    <a href="index.php?page=clientes&action=create" class="btn btn-primary">+ Nuevo Cliente</a>
</div>

<!-- Formulario de búsqueda -->
<div class="search-box">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="clientes">
        <input 
            type="text" 
            name="search" 
            placeholder="Buscar por nombre, apellido o CI..." 
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            class="search-input"
        >
        <button type="submit" class="btn btn-secondary">Buscar</button>
        <?php if (isset($_GET['search'])): ?>
            <a href="index.php?page=clientes" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de clientes -->
<div class="table-container">
    <?php if (empty($clientes)): ?>
        <div class="empty-state">
            <p>No se encontraron clientes</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>CI</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['id']) ?></td>
                        <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
                        <td><?= htmlspecialchars($cliente['ci']) ?></td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                        <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                        <td class="table-actions">
                            <a 
                                href="index.php?page=clientes&action=edit&id=<?= $cliente['id'] ?>" 
                                class="btn-action btn-edit"
                                title="Editar"
                            >
                                ✏️
                            </a>
                            <a 
                                href="index.php?page=clientes&action=delete&id=<?= $cliente['id'] ?>" 
                                class="btn-action btn-delete"
                                onclick="return confirm('¿Está seguro de eliminar este cliente?')"
                                title="Eliminar"
                            >
                                🗑️
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
