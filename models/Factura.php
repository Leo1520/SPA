<?php
/**
 * ════════════════════════════════════════
 * MODELO: Factura
 * ════════════════════════════════════════
 * Gestión de facturas
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */

class Factura {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener factura por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT f.*, 
                   v.total,
                   v.id_reserva,
                   r.fecha as fecha_servicio,
                   CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                   c.ci as cliente_ci
            FROM Factura f
            INNER JOIN Venta v ON f.id_venta = v.id
            INNER JOIN Reserva r ON v.id_reserva = r.id
            INNER JOIN Cliente c ON r.id_cliente = c.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener factura por ID de venta
     * @param int $idVenta
     * @return array|false
     */
    public function getByVenta($idVenta) {
        $stmt = $this->db->prepare("
            SELECT f.* 
            FROM Factura f
            WHERE f.id_venta = ?
        ");
        $stmt->execute([$idVenta]);
        return $stmt->fetch();
    }

    /**
     * Verificar si existe factura para una venta
     * @param int $idVenta
     * @return bool
     */
    public function existsByVenta($idVenta) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Factura WHERE id_venta = ?");
        $stmt->execute([$idVenta]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nueva factura
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Factura (nit_cliente, razon_social, total, id_venta)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nit_cliente'],
            $data['razon_social'],
            $data['total'],
            $data['id_venta']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Generar número de factura formateado a partir del ID
     * @param int $id
     * @return string
     */
    public function formatNumeroFactura($id) {
        // Formato: YYYY-NNNNNN (año + ID con 6 dígitos)
        $year = date('Y');
        return $year . '-' . str_pad($id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Eliminar factura
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Factura WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
