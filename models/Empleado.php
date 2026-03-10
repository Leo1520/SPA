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

    /**
     * ════════════════════════════════════════
     * RF014 - GESTIÓN DE EMPLEADOS
     * ════════════════════════════════════════
     */

    /**
     * Obtener todos los empleados con especialidades y usuario
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT 
                e.*,
                u.username,
                GROUP_CONCAT(esp.nombre SEPARATOR ', ') AS especialidades
            FROM Empleado e
            LEFT JOIN Usuario u ON u.id_empleado = e.id
            LEFT JOIN Empleado_Especialidad ee ON ee.id_empleado = e.id
            LEFT JOIN Especialidad esp ON esp.id = ee.id_especialidad
            GROUP BY e.id
            ORDER BY e.nombre ASC
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un CI ya existe
     * @param string $ci
     * @param int|null $excludeId ID a excluir en la búsqueda (para edición)
     * @return bool
     */
    public function existsByCi($ci, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM Empleado WHERE ci = :ci";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':ci', $ci);
        if ($excludeId !== null) {
            $stmt->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear un nuevo empleado
     * @param array $data
     * @return int ID del empleado creado
     */
    public function create($data) {
        $query = "
            INSERT INTO Empleado (nombre, apellido, ci, email, telefono, cargo, fecha_contratacion, activo)
            VALUES (:nombre, :apellido, :ci, :email, :telefono, :cargo, :fecha_contratacion, 1)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':ci' => $data['ci'],
            ':email' => $data['email'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':cargo' => $data['cargo'],
            ':fecha_contratacion' => $data['fecha_contratacion'] ?? date('Y-m-d'),
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar un empleado
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "
            UPDATE Empleado 
            SET nombre = :nombre,
                apellido = :apellido,
                ci = :ci,
                email = :email,
                telefono = :telefono,
                cargo = :cargo,
                fecha_contratacion = :fecha_contratacion
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':ci' => $data['ci'],
            ':email' => $data['email'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':cargo' => $data['cargo'],
            ':fecha_contratacion' => $data['fecha_contratacion'],
        ]);
    }

    /**
     * Activar/Desactivar empleado
     * @param int $id
     * @return bool
     */
    public function toggle($id) {
        $query = "UPDATE Empleado SET activo = NOT activo WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtener especialidades de un empleado
     * @param int $idEmpleado
     * @return array
     */
    public function getEspecialidades($idEmpleado) {
        $query = "
            SELECT id_especialidad 
            FROM Empleado_Especialidad 
            WHERE id_empleado = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $idEmpleado]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_especialidad');
    }

    /**
     * Asignar especialidades a un empleado
     * @param int $idEmpleado
     * @param array $especialidades IDs de especialidades
     * @return void
     */
    public function setEspecialidades($idEmpleado, $especialidades) {
        // Eliminar especialidades actuales
        $deleteStmt = $this->db->prepare("DELETE FROM Empleado_Especialidad WHERE id_empleado = :id");
        $deleteStmt->execute([':id' => $idEmpleado]);

        // Insertar nuevas especialidades
        if (!empty($especialidades)) {
            $insertStmt = $this->db->prepare("
                INSERT INTO Empleado_Especialidad (id_empleado, id_especialidad) 
                VALUES (:id_empleado, :id_especialidad)
            ");
            foreach ($especialidades as $idEspecialidad) {
                $insertStmt->execute([
                    ':id_empleado' => $idEmpleado,
                    ':id_especialidad' => $idEspecialidad,
                ]);
            }
        }
    }

    /**
     * Contar reservas futuras activas de un empleado
     * @param int $id
     * @return int
     */
    public function countReservasFuturas($id) {
        $query = "
            SELECT COUNT(*) 
            FROM Detalle_Reserva dr
            JOIN Reserva r ON dr.id_reserva = r.id
            WHERE dr.id_empleado = :id
              AND r.fecha >= CURDATE()
              AND r.estado IN ('Pendiente', 'Confirmada')
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Verificar si el empleado tiene un usuario asociado
     * @param int $idEmpleado
     * @return bool
     */
    public function hasUsuario($idEmpleado) {
        $query = "SELECT COUNT(*) FROM Usuario WHERE id_empleado = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $idEmpleado]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener todas las especialidades disponibles
     * @return array
     */
    public function getAllEspecialidades() {
        $stmt = $this->db->query("SELECT * FROM Especialidad ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
