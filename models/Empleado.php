<?php
/**
 * ════════════════════════════════════════
 * MODELO: Empleado
 * ════════════════════════════════════════
 * Gestión de empleados del spa
 */

class Empleado {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los empleados activos
     * @return array
     */
    public function getAllActive() {
        $stmt = $this->db->query("
            SELECT * FROM Empleado 
            WHERE activo = 1 
            ORDER BY nombre, apellido
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener empleado por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Empleado WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los empleados
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM Empleado ORDER BY nombre, apellido");
        return $stmt->fetchAll();
    }
}
