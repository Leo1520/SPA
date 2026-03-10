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
    <input 
        type="text" 
        id="searchInput"
        placeholder="Buscar por nombre, apellido o CI..." 
        class="search-input"
    >
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

<script>
// Búsqueda en tiempo real de clientes
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('.data-table tbody');
    const emptyState = document.querySelector('.empty-state');
    
    if (!searchInput || !table) return;
    
    // Obtener todas las filas de clientes
    const rows = Array.from(table.querySelectorAll('tr'));
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Obtener el texto de las columnas: nombre, CI, email, teléfono
            const nombre = row.cells[1]?.textContent.toLowerCase() || '';
            const ci = row.cells[2]?.textContent.toLowerCase() || '';
            const email = row.cells[3]?.textContent.toLowerCase() || '';
            const telefono = row.cells[4]?.textContent.toLowerCase() || '';
            
            // Verificar si coincide con la búsqueda
            const matches = nombre.includes(searchTerm) || 
                          ci.includes(searchTerm) || 
                          email.includes(searchTerm) || 
                          telefono.includes(searchTerm);
            
            // Mostrar u ocultar la fila
            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        if (visibleCount === 0 && searchTerm !== '') {
            if (!document.querySelector('.no-results-message')) {
                const noResultsMsg = document.createElement('tr');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.innerHTML = '<td colspan="7" style="text-align: center; padding: 2rem; color: #666;">No se encontraron clientes que coincidan con "' + searchTerm + '"</td>';
                table.appendChild(noResultsMsg);
            } else {
                document.querySelector('.no-results-message td').innerHTML = 'No se encontraron clientes que coincidan con "' + searchTerm + '"';
            }
        } else {
            // Eliminar mensaje de no resultados si existe
            const noResultsMsg = document.querySelector('.no-results-message');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    });
});
</script>
