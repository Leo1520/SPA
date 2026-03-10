<?php
/**
 * ════════════════════════════════════════
 * MODELO: Insumo
 * ════════════════════════════════════════
 * RF009 - GESTIÓN DE INSUMOS
 */

class Insumo {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los insumos
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM Insumo ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener insumo por ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Insumo WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo insumo
     * @param array $data
     * @return int ID del insumo creado
     */
    public function create($data) {
        $query = "
            INSERT INTO Insumo (nombre, descripcion, stock, stock_minimo, unidad_medida, costo_unitario)
            VALUES (:nombre, :descripcion, :stock, :stock_minimo, :unidad_medida, :costo_unitario)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':stock' => $data['stock'] ?? 0,
            ':stock_minimo' => $data['stock_minimo'] ?? 0,
            ':unidad_medida' => $data['unidad_medida'],
            ':costo_unitario' => $data['costo_unitario'] ?? 0,
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar insumo
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "
            UPDATE Insumo 
            SET nombre = :nombre,
                descripcion = :descripcion,
                stock = :stock,
                stock_minimo = :stock_minimo,
                unidad_medida = :unidad_medida,
                costo_unitario = :costo_unitario
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':stock' => $data['stock'],
            ':stock_minimo' => $data['stock_minimo'],
            ':unidad_medida' => $data['unidad_medida'],
            ':costo_unitario' => $data['costo_unitario'] ?? 0,
        ]);
    }

    /**
     * Eliminar insumo
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM Insumo WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Verificar si el insumo está asignado a servicios
     * @param int $id
     * @return bool
     */
    public function isAsignadoAServicios($id) {
        $query = "SELECT COUNT(*) FROM Servicio_Insumo WHERE id_insumo = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Registrar movimiento de inventario
     * @param array $data
     * @return int ID del movimiento creado
     */
    public function registrarMovimiento($data) {
        $query = "
            INSERT INTO Movimiento_Inventario (id_insumo, tipo, cantidad, motivo, id_usuario, fecha)
            VALUES (:id_insumo, :tipo, :cantidad, :motivo, :id_usuario, NOW())
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_insumo' => $data['id_insumo'],
            ':tipo' => $data['tipo'],
            ':cantidad' => $data['cantidad'],
            ':motivo' => $data['motivo'],
            ':id_usuario' => $data['id_usuario'],
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Eliminar movimientos de inventario de un insumo
     * @param int $idInsumo
     * @return bool
     */
    public function deleteMovimientos($idInsumo) {
        $query = "DELETE FROM Movimiento_Inventario WHERE id_insumo = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $idInsumo]);
    }
}
