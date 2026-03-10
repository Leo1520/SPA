<?php
/**
 * ════════════════════════════════════════════════
 * CONTROLADOR: Reportes
 * ════════════════════════════════════════════════
 * RF015 - Reporte Diario de Reservas
 * RF016 - Reporte de Ventas por Empleado
 * 
 * Roles permitidos:
 * - reservasDia: Administrador, Recepcionista
 * - ventasEmpleado: Solo Administrador
 */

require_once 'config/db.php';

class ReporteController {

    /**
     * Verifica sesión activa y permisos de rol
     * 
     * @param array $rolesPermitidos Lista de roles con acceso
     * @return void Redirige si no tiene permisos
     */
    private function checkAuth(array $rolesPermitidos): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
            $_SESSION['flash']['error'] = 'No tiene permisos para acceder a este módulo';
            header('Location: index.php?page=clientes');
            exit;
        }
    }

    /**
     * ════════════════════════════════════════════════
     * RF015 - REPORTE DIARIO DE RESERVAS
     * ════════════════════════════════════════════════
     * 
     * Muestra todas las reservas de una fecha específica
     * con detalles de cliente, servicio, terapeuta, sala y horarios
     * 
     * @return void Carga la vista con los resultados
     */
    public function reservasDia(): void {
        $this->checkAuth(['Administrador', 'Recepcionista']);
        
        // Fecha por defecto: hoy
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        
        $db = getDB();
        
        // Query: Obtener todas las reservas del día con sus detalles
        $query = "
            SELECT 
                CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                s.nombre AS servicio,
                CONCAT(e.nombre, ' ', e.apellido) AS terapeuta,
                sa.nombre AS sala,
                dr.hora_inicio,
                dr.hora_fin,
                r.estado
            FROM Reserva r
            JOIN Cliente c ON r.id_cliente = c.id
            JOIN Detalle_Reserva dr ON dr.id_reserva = r.id
            JOIN Servicio s ON dr.id_servicio = s.id
            JOIN Empleado e ON dr.id_empleado = e.id
            JOIN Sala sa ON dr.id_sala = sa.id
            WHERE r.fecha = :fecha
            ORDER BY dr.hora_inicio ASC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':fecha' => $fecha]);
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cargar vista
        require 'views/layout/header.php';
        require 'views/reportes/reservas_dia.php';
        require 'views/layout/footer.php';
    }

    /**
     * ════════════════════════════════════════════════
     * RF016 - REPORTE DE VENTAS POR EMPLEADO
     * ════════════════════════════════════════════════
     * 
     * Muestra servicios prestados por un empleado en un rango de fechas
     * Calcula el total de servicios completados
     * 
     * @return void Carga la vista con los resultados
     */
    public function ventasEmpleado(): void {
        $this->checkAuth(['Administrador']);
        
        $db = getDB();
        
        // Cargar lista de empleados activos para el select
        $queryEmpleados = "
            SELECT 
                id, 
                CONCAT(nombre, ' ', apellido) AS nombre_completo 
            FROM Empleado 
            WHERE activo = 1 
            ORDER BY nombre
        ";
        $empleados = $db->query($queryEmpleados)->fetchAll(PDO::FETCH_ASSOC);
        
        // Variables para resultados
        $resultados = [];
        $totalPrecio = 0;
        $empleadoSeleccionado = null;
        $fechaDesde = $_GET['desde'] ?? '';
        $fechaHasta = $_GET['hasta'] ?? '';
        $idEmpleado = $_GET['id_empleado'] ?? '';
        
        // Procesar consulta si hay filtros
        if (!empty($idEmpleado) && !empty($fechaDesde) && !empty($fechaHasta)) {
            // Query: Servicios prestados por empleado en rango de fechas
            $query = "
                SELECT
                    r.fecha,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente,
                    s.nombre AS servicio,
                    dr.precio,
                    r.estado
                FROM Detalle_Reserva dr
                JOIN Reserva r ON dr.id_reserva = r.id
                JOIN Cliente c ON r.id_cliente = c.id
                JOIN Servicio s ON dr.id_servicio = s.id
                JOIN Empleado e ON dr.id_empleado = e.id
                WHERE dr.id_empleado = :id_empleado
                  AND r.fecha BETWEEN :desde AND :hasta
                ORDER BY r.fecha DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':id_empleado' => $idEmpleado,
                ':desde' => $fechaDesde,
                ':hasta' => $fechaHasta,
            ]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total de servicios completados
            $totalPrecio = array_sum(
                array_column(
                    array_filter($resultados, function($r) {
                        return $r['estado'] === 'Completada';
                    }),
                    'precio'
                )
            );
            
            // Obtener nombre del empleado seleccionado
            $stmtEmpleado = $db->prepare("
                SELECT CONCAT(nombre, ' ', apellido) AS nombre_completo 
                FROM Empleado 
                WHERE id = :id
            ");
            $stmtEmpleado->execute([':id' => $idEmpleado]);
            $empleadoSeleccionado = $stmtEmpleado->fetchColumn();
        }
        
        // Cargar vista
        require 'views/layout/header.php';
        require 'views/reportes/ventas_empleado.php';
        require 'views/layout/footer.php';
    }
}
