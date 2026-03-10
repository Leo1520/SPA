/**
 * ════════════════════════════════════════
 * JAVASCRIPT PRINCIPAL - SPA LAS AMÉRICA
 * ════════════════════════════════════════
 * Funcionalidades dinámicas en JavaScript vanilla
 */

// ════════════════════════════════════════
// GESTIÓN DE SERVICIOS DINÁMICOS EN RESERVAS
// ════════════════════════════════════════

let servicioIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la página de crear reservas
    const reservaForm = document.getElementById('reservaForm');
    
    if (reservaForm) {
        initReservaForm();
    }

    // Auto-hide para alertas después de 5 segundos
    autoHideAlerts();

    // Confirmación de eliminación mejorada
    setupDeleteConfirmations();
});

/**
 * Inicializar formulario de reservas
 */
function initReservaForm() {
    // Agregar el primer servicio automáticamente
    addServicio();

    // Event listener para agregar servicio
    const addBtn = document.getElementById('addServicioBtn');
    if (addBtn) {
        addBtn.addEventListener('click', addServicio);
    }
}

/**
 * Agregar un nuevo servicio al formulario de reservas
 */
function addServicio() {
    const template = document.getElementById('servicioTemplate');
    const container = document.getElementById('serviciosContainer');
    
    if (!template || !container) {
        return;
    }
    
    // Clonar template
    const clone = template.content.cloneNode(true);
    
    // Reemplazar INDEX con el índice actual
    const tempDiv = document.createElement('div');
    tempDiv.appendChild(clone);
    const html = tempDiv.innerHTML.replace(/INDEX/g, servicioIndex);
    
    // Agregar al contenedor
    container.insertAdjacentHTML('beforeend', html);
    
    // Actualizar número de servicio
    const servicioItems = container.querySelectorAll('.servicio-item');
    const lastItem = servicioItems[servicioItems.length - 1];
    const numberSpan = lastItem.querySelector('.servicio-number');
    if (numberSpan) {
        numberSpan.textContent = servicioIndex + 1;
    }
    
    servicioIndex++;
}

/**
 * Eliminar un servicio del formulario de reservas
 */
function removeServicio(button) {
    const servicioItem = button.closest('.servicio-item');
    const container = document.getElementById('serviciosContainer');
    
    if (!servicioItem || !container) {
        return;
    }
    
    // No permitir eliminar si es el único servicio
    const servicioItems = container.querySelectorAll('.servicio-item');
    if (servicioItems.length <= 1) {
        alert('Debe haber al menos un servicio en la reserva');
        return;
    }
    
    servicioItem.remove();
    
    // Renumerar servicios restantes
    renumberServicios();
}

/**
 * Renumerar los servicios después de eliminar uno
 */
function renumberServicios() {
    const container = document.getElementById('serviciosContainer');
    if (!container) {
        return;
    }
    
    const servicioItems = container.querySelectorAll('.servicio-item');
    servicioItems.forEach((item, index) => {
        const numberSpan = item.querySelector('.servicio-number');
        if (numberSpan) {
            numberSpan.textContent = index + 1;
        }
    });
}

/**
 * Auto-ocultar alertas después de 5 segundos
 */
function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

/**
 * Configurar confirmaciones de eliminación personalizadas
 */
function setupDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validar horarios en formulario de reservas
 */
function validateTimeRange(horaInicio, horaFin) {
    if (!horaInicio || !horaFin) {
        return true; // Dejar que la validación del servidor maneje campos vacíos
    }
    
    const inicio = new Date('2000-01-01 ' + horaInicio);
    const fin = new Date('2000-01-01 ' + horaFin);
    
    return fin > inicio;
}

/**
 * Formatear fecha para mostrar en español
 */
function formatDateES(dateString) {
    const months = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];
    
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${day} de ${month} de ${year}`;
}

/**
 * Utilidad para búsqueda en tiempo real (opcional)
 */
function setupLiveSearch(inputId, targetSelector) {
    const input = document.getElementById(inputId);
    if (!input) {
        return;
    }
    
    let debounceTimer;
    
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        debounceTimer = setTimeout(() => {
            const searchTerm = this.value.toLowerCase();
            const targets = document.querySelectorAll(targetSelector);
            
            targets.forEach(target => {
                const text = target.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    target.style.display = '';
                } else {
                    target.style.display = 'none';
                }
            });
        }, 300);
    });
}

// ════════════════════════════════════════
// EXPORTAR FUNCIONES GLOBALES
// ════════════════════════════════════════

// Hacer algunas funciones accesibles globalmente para uso inline
window.removeServicio = removeServicio;
window.addServicio = addServicio;
window.validateTimeRange = validateTimeRange;
