<?php
/**
 * ════════════════════════════════════════
 * MODELO: Usuario
 * ════════════════════════════════════════
 * Gestión de usuarios del sistema
 */

class Usuario {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Buscar usuario por username
     * @param string $username
     * @return array|false
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre as rol_nombre, e.nombre as empleado_nombre, 
                   e.apellido as empleado_apellido
            FROM Usuario u
            INNER JOIN Rol r ON u.id_rol = r.id
            LEFT JOIN Empleado e ON u.id_empleado = e.id
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Verificar si el usuario está activo
     * @param int $userId
     * @return bool
     */
    public function isActive($userId) {
        $stmt = $this->db->prepare("SELECT activo FROM Usuario WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result && $result['activo'] == 1;
    }

    /**
     * Crear nuevo usuario
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Usuario (username, password, activo, id_rol, id_empleado)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['activo'] ?? 1,
            $data['id_rol'],
            $data['id_empleado'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar usuario
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE Usuario 
            SET username = ?, activo = ?, id_rol = ?, id_empleado = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['username'],
            $data['activo'],
            $data['id_rol'],
            $data['id_empleado'] ?? null,
            $id
        ]);
    }

    /**
     * Verificar si un username ya existe
     * @param string $username
     * @param int|null $excludeId ID a excluir en la búsqueda
     * @return bool
     */
    public function existsByUsername($username, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM Usuario WHERE username = :username";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $username);
        if ($excludeId !== null) {
            $stmt->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener todos los roles disponibles
     * @return array
     */
    public function getAllRoles() {
        $stmt = $this->db->query("SELECT * FROM Rol ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
