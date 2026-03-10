<?php
/**
 * ════════════════════════════════════════
 * MODELO: DetalleReserva
 * ════════════════════════════════════════
 * Gestión de detalles de reservas (servicios)
 */

class DetalleReserva {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener detalles de una reserva
     * @param int $idReserva
     * @return array
     */
    public function getByReserva($idReserva) {
        $stmt = $this->db->prepare("
            SELECT dr.*, 
                   s.nombre as servicio_nombre,
                   CONCAT(e.nombre, ' ', e.apellido) as empleado_nombre,
                   sa.nombre as sala_nombre
            FROM Detalle_Reserva dr
            INNER JOIN Servicio s ON dr.id_servicio = s.id
            LEFT JOIN Empleado e ON dr.id_empleado = e.id
            LEFT JOIN Sala sa ON dr.id_sala = sa.id
            WHERE dr.id_reserva = ?
        ");
        $stmt->execute([$idReserva]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo detalle de reserva
     * @param array $data
     * @return int
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO Detalle_Reserva 
            (hora_inicio, hora_fin, precio, observaciones, id_reserva, id_servicio, id_empleado, id_sala)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['precio'],
            $data['observaciones'] ?? null,
            $data['id_reserva'],
            $data['id_servicio'],
            $data['id_empleado'] ?? null,
            $data['id_sala'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Verificar disponibilidad de empleado en horario específico
     * @param int $idEmpleado
     * @param string $fecha
     * @param string $horaInicio
     * @param string $horaFin
     * @return bool true si está disponible
     */
    public function isEmpleadoDisponible($idEmpleado, $fecha, $horaInicio, $horaFin) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM Detalle_Reserva dr
            INNER JOIN Reserva r ON dr.id_reserva = r.id
            WHERE dr.id_empleado = ?
            AND r.fecha = ?
            AND r.estado != 'Cancelada'
            AND (
                (dr.hora_inicio < ? AND dr.hora_fin > ?)
                OR (dr.hora_inicio < ? AND dr.hora_fin > ?)
                OR (dr.hora_inicio >= ? AND dr.hora_fin <= ?)
            )
        ");
        $stmt->execute([
            $idEmpleado, 
            $fecha, 
            $horaFin, $horaInicio,  // Solapamiento tipo 1
            $horaFin, $horaInicio,  // Solapamiento tipo 2
            $horaInicio, $horaFin   // Contenido completo
        ]);
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }

    /**
     * Verificar disponibilidad de sala en horario específico
     * @param int $idSala
     * @param string $fecha
     * @param string $horaInicio
     * @param string $horaFin
     * @return bool true si está disponible
     */
    public function isSalaDisponible($idSala, $fecha, $horaInicio, $horaFin) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM Detalle_Reserva dr
            INNER JOIN Reserva r ON dr.id_reserva = r.id
            WHERE dr.id_sala = ?
            AND r.fecha = ?
            AND r.estado != 'Cancelada'
            AND (
                (dr.hora_inicio < ? AND dr.hora_fin > ?)
                OR (dr.hora_inicio < ? AND dr.hora_fin > ?)
                OR (dr.hora_inicio >= ? AND dr.hora_fin <= ?)
            )
        ");
        $stmt->execute([
            $idSala, 
            $fecha, 
            $horaFin, $horaInicio,
            $horaFin, $horaInicio,
            $horaInicio, $horaFin
        ]);
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }

    /**
     * Eliminar detalles de una reserva
     * @param int $idReserva
     * @return bool
     */
    public function deleteByReserva($idReserva) {
        $stmt = $this->db->prepare("DELETE FROM Detalle_Reserva WHERE id_reserva = ?");
        return $stmt->execute([$idReserva]);
    }
}
