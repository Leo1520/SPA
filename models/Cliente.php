<?php
/**
 * ════════════════════════════════════════
 * MODELO: Cliente
 * ════════════════════════════════════════
 * Gestión de clientes del spa
 */

class Cliente {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los clientes con búsqueda opcional
     * @param string|null $search
     * @return array
     */
    public function getAll($search = null) {
        if ($search) {
            $stmt = $this->db->prepare("
                SELECT * FROM Cliente 
                WHERE nombre LIKE ? OR apellido LIKE ? OR ci LIKE ?
                ORDER BY fecha_registro DESC
            ");
            $searchTerm = "%{$search}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        } else {
            $stmt = $this->db->query("SELECT * FROM Cliente ORDER BY fecha_registro DESC");
        }
        return $stmt->fetchAll();
    }

    /**
     * Obtener cliente por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Cliente WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Verificar si existe un CI
     * @param string $ci
     * @param int|null $excludeId
     * @return bool
     */
    public function existsCI($ci, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Cliente WHERE ci = ? AND id != ?");
            $stmt->execute([$ci, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Cliente WHERE ci = ?");
            $stmt->execute([$ci]);
        }
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Crear nuevo cliente
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Cliente (nombre, apellido, ci, email, telefono, fecha_nacimiento, fecha_registro)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['ci'],
            $data['email'],
            $data['telefono'] ?? null,
            $data['fecha_nacimiento'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar cliente
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE Cliente 
            SET nombre = ?, apellido = ?, ci = ?, email = ?, telefono = ?, fecha_nacimiento = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['ci'],
            $data['email'],
            $data['telefono'] ?? null,
            $data['fecha_nacimiento'] ?? null,
            $id
        ]);
    }

    /**
     * Eliminar cliente
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Cliente WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si el cliente tiene reservas asociadas
     * @param int $id
     * @return bool
     */
    public function hasReservas($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Reserva WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
