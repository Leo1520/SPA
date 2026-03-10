<?php
/**
 * ════════════════════════════════════════
 * MODELO: Sala
 * ════════════════════════════════════════
 * Gestión de salas del spa
 */

class Sala {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todas las salas
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM Sala ORDER BY nombre");
        return $stmt->fetchAll();
    }

    /**
     * Obtener sala por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Sala WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * ════════════════════════════════════════
     * RF008 - GESTIÓN DE SALAS
     * ════════════════════════════════════════
     */

    /**
     * Crear nueva sala
     * @param array $data
     * @return int ID de la sala creada
     */
    public function create($data) {
        $query = "
            INSERT INTO Sala (nombre, capacidad, ubicacion)
            VALUES (:nombre, :capacidad, :ubicacion)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':capacidad' => $data['capacidad'] ?? null,
            ':ubicacion' => $data['ubicacion'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar sala
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "
            UPDATE Sala 
            SET nombre = :nombre,
                capacidad = :capacidad,
                ubicacion = :ubicacion
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':capacidad' => $data['capacidad'] ?? null,
            ':ubicacion' => $data['ubicacion'] ?? null,
        ]);
    }

    /**
     * Eliminar sala
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM Sala WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Contar reservas futuras activas de una sala
     * @param int $id
     * @return int
     */
    public function countReservasFuturas($id) {
        $query = "
            SELECT COUNT(*) 
            FROM Detalle_Reserva dr
            JOIN Reserva r ON dr.id_reserva = r.id
            WHERE dr.id_sala = :id
              AND r.fecha >= CURDATE()
              AND r.estado IN ('Pendiente', 'Confirmada')
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }
}
