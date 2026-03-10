<?php
/**
 * ════════════════════════════════════════
 * MODELO: Pago
 * ════════════════════════════════════════
 * Gestión de pagos de ventas
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */

class Pago {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los pagos de una venta
     * @param int $idVenta
     * @return array
     */
    public function getByVenta($idVenta) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   mp.nombre as metodo_pago
            FROM Pago p
            INNER JOIN Metodo_Pago mp ON p.id_metodo_pago = mp.id
            WHERE p.id_venta = ?
            ORDER BY p.fecha_pago DESC
        ");
        $stmt->execute([$idVenta]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener total pagado de una venta
     * @param int $idVenta
     * @return float
     */
    public function getTotalPagado($idVenta) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto), 0) as total 
            FROM Pago 
            WHERE id_venta = ?
        ");
        $stmt->execute([$idVenta]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Crear nuevo pago
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Pago (monto, referencia, id_venta, id_metodo_pago)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['monto'],
            $data['referencia'] ?? null,
            $data['id_venta'],
            $data['id_metodo_pago']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Eliminar pago
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Pago WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
