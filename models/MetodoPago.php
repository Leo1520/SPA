<?php
/**
 * ════════════════════════════════════════
 * MODELO: MetodoPago
 * ════════════════════════════════════════
 * Gestión de métodos de pago disponibles
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */

class MetodoPago {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los métodos de pago
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT * 
            FROM Metodo_Pago 
            ORDER BY nombre ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener método de pago por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Metodo_Pago WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
