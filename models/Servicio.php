<?php
/**
 * ════════════════════════════════════════
 * MODELO: Servicio
 * ════════════════════════════════════════
 * Gestión de servicios del spa
 */

class Servicio {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los servicios activos
     * @return array
     */
    public function getAllActive() {
        $stmt = $this->db->query("
            SELECT * FROM Servicio 
            WHERE activo = 1 
            ORDER BY nombre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener servicio por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Servicio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los servicios
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM Servicio ORDER BY nombre");
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo servicio
     * RF007
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Servicio (nombre, descripcion, duracion_minutos, precio, activo)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['duracion_minutos'],
            $data['precio'],
            $data['activo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar servicio
     * RF007
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE Servicio 
            SET nombre = ?, descripcion = ?, duracion_minutos = ?, precio = ?, activo = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['duracion_minutos'],
            $data['precio'],
            $data['activo'] ?? 1,
            $id
        ]);
    }

    /**
     * Alternar estado activo/inactivo
     * RF007
     * @param int $id
     * @return bool
     */
    public function toggle($id) {
        $stmt = $this->db->prepare("
            UPDATE Servicio 
            SET activo = NOT activo 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Eliminar servicio
     * RF007
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        // Eliminar primero las relaciones en Servicio_Insumo (si existen)
        $stmt = $this->db->prepare("DELETE FROM Servicio_Insumo WHERE id_servicio = ?");
        $stmt->execute([$id]);
        
        // Eliminar el servicio
        $stmt = $this->db->prepare("DELETE FROM Servicio WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si el servicio tiene detalles de reserva
     * RF007
     * @param int $id
     * @return bool
     */
    public function hasDetalleReserva($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Detalle_Reserva WHERE id_servicio = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si el servicio tiene detalles de venta
     * RF007
     * @param int $id
     * @return bool
     */
    public function hasDetalleVenta($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Detalle_Venta WHERE id_servicio = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si existe un servicio con el mismo nombre
     * RF007
     * @param string $nombre
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByNombre($nombre, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM Servicio WHERE nombre = ? AND id != ?");
            $stmt->execute([$nombre, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM Servicio WHERE nombre = ?");
            $stmt->execute([$nombre]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
