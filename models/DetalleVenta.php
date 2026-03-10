<?php
/**
 * ════════════════════════════════════════
 * MODELO: DetalleVenta
 * ════════════════════════════════════════
 * Gestión de detalles de ventas (servicios vendidos)
 * RF003 - REGISTRAR DETALLE DE VENTA
 */

class DetalleVenta {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Crear nuevo detalle de venta
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Detalle_Venta 
            (cantidad, precio_unitario, subtotal, id_venta, id_servicio, id_empleado)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['cantidad'],
            $data['precio_unitario'],
            $data['subtotal'],
            $data['id_venta'],
            $data['id_servicio'],
            $data['id_empleado'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener detalles de una venta
     * @param int $idVenta
     * @return array
     */
    public function getByVenta($idVenta) {
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
     * Eliminar detalles de una venta
     * @param int $idVenta
     * @return bool
     */
    public function deleteByVenta($idVenta) {
        $stmt = $this->db->prepare("DELETE FROM Detalle_Venta WHERE id_venta = ?");
        return $stmt->execute([$idVenta]);
    }
}
