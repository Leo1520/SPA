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
}
