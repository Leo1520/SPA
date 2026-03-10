<?php
/**
 * ════════════════════════════════════════
 * MODELO: Reserva
 * ════════════════════════════════════════
 * Gestión de reservas del spa
 */

class Reserva {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todas las reservas con filtro opcional por estado
     * @param string|null $estado
     * @return array
     */
    public function getAll($estado = null) {
        $sql = "
            SELECT r.*, 
                   CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                   COUNT(dr.id) as cantidad_servicios
            FROM Reserva r
            INNER JOIN Cliente c ON r.id_cliente = c.id
            LEFT JOIN Detalle_Reserva dr ON r.id = dr.id_reserva
        ";
        
        if ($estado) {
            $sql .= " WHERE r.estado = ?";
        }
        
        $sql .= " GROUP BY r.id ORDER BY r.fecha DESC, r.id DESC";
        
        if ($estado) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$estado]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener reserva por ID con detalles
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
                   c.email as cliente_email,
                   c.telefono as cliente_telefono
            FROM Reserva r
            INNER JOIN Cliente c ON r.id_cliente = c.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear nueva reserva
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Reserva (fecha, estado, fecha_registro, id_cliente)
            VALUES (?, ?, NOW(), ?)
        ");
        $stmt->execute([
            $data['fecha'],
            $data['estado'] ?? 'Pendiente',
            $data['id_cliente']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar estado de reserva
     * @param int $id
     * @param string $estado
     * @return bool
     */
    public function updateEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE Reserva SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Eliminar reserva
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Reserva WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si una reserva tiene venta asociada
     * @param int $id
     * @return bool
     */
    public function hasVenta($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Venta WHERE id_reserva = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
