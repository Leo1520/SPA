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
}
