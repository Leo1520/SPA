<?php
/**
 * ════════════════════════════════════════
 * MODELO: Venta
 * ════════════════════════════════════════
 * Gestión de ventas del spa
 * RF003 - REGISTRAR DETALLE DE VENTA
 */

class Venta {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todas las ventas con información de cliente y estado de pago
     * @param string|null $fechaDesde
     * @param string|null $fechaHasta
     * @return array
     */
    public function getAll($fechaDesde = null, $fechaHasta = null) {
        $sql = "
            SELECT v.*, 
                   r.fecha as fecha_reserva,
                   CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                   COALESCE(SUM(p.monto), 0) as total_pagado,
                   CASE 
                       WHEN COALESCE(SUM(p.monto), 0) >= v.total THEN 'Pagada'
                       ELSE 'Pendiente'
                   END as estado_pago
            FROM Venta v
            INNER JOIN Reserva r ON v.id_reserva = r.id
            INNER JOIN Cliente c ON r.id_cliente = c.id
            LEFT JOIN Pago p ON v.id = p.id_venta
        ";
        
        $conditions = [];
        $params = [];
        
        if ($fechaDesde) {
            $conditions[] = "v.fecha >= ?";
            $params[] = $fechaDesde;
        }
        
        if ($fechaHasta) {
            $conditions[] = "v.fecha <= ?";
            $params[] = $fechaHasta;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY v.id ORDER BY v.fecha DESC, v.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener venta por ID con detalles completos
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   r.fecha as fecha_reserva,
                   r.id as reserva_id,
                   CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                   c.email as cliente_email,
                   c.telefono as cliente_telefono,
                   COALESCE(SUM(p.monto), 0) as total_pagado
            FROM Venta v
            INNER JOIN Reserva r ON v.id_reserva = r.id
            INNER JOIN Cliente c ON r.id_cliente = c.id
            LEFT JOIN Pago p ON v.id = p.id_venta
            WHERE v.id = ?
            GROUP BY v.id
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Verificar si existe una venta para una reserva
     * @param int $idReserva
     * @return bool
     */
    public function existsByReserva($idReserva) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Venta WHERE id_reserva = ?");
        $stmt->execute([$idReserva]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nueva venta
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Venta (total, fecha, id_reserva)
            VALUES (?, NOW(), ?)
        ");
        $stmt->execute([
            $data['total'],
            $data['id_reserva']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener detalles de venta (servicios)
     * @param int $idVenta
     * @return array
     */
    public function getDetalles($idVenta) {
        $stmt = $this->db->prepare("
            SELECT dv.*, 
                   s.nombre as servicio_nombre,
                   CONCAT(e.nombre, ' ', e.apellido) as empleado_nombre
            FROM Detalle_Venta dv
            INNER JOIN Servicio s ON dv.id_servicio = s.id
            LEFT JOIN Empleado e ON dv.id_empleado = e.id
            WHERE dv.id_venta = ?
            ORDER BY dv.id
        ");
        $stmt->execute([$idVenta]);
        return $stmt->fetchAll();
    }

    /**
     * Eliminar venta
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Venta WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
