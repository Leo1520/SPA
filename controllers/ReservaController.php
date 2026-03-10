<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: ReservaController
 * ════════════════════════════════════════
 * Gestión de reservas
 * RF002 - GESTIÓN DE RESERVAS
 */

class ReservaController {
    private $reservaModel;
    private $detalleReservaModel;
    private $clienteModel;
    private $servicioModel;
    private $empleadoModel;
    private $salaModel;

    public function __construct() {
        $this->reservaModel = new Reserva();
        $this->detalleReservaModel = new DetalleReserva();
        $this->clienteModel = new Cliente();
        $this->servicioModel = new Servicio();
        $this->empleadoModel = new Empleado();
        $this->salaModel = new Sala();
    }

    /**
     * Listado de reservas con filtro por estado
     */
    public function index() {
        $estado = $_GET['estado'] ?? null;
        $reservas = $this->reservaModel->getAll($estado);
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/reservas/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Mostrar formulario de creación de reserva
     */
    public function create() {
        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Obtener datos para los selectores
        $clientes = $this->clienteModel->getAll();
        $servicios = $this->servicioModel->getAllActive();
        $empleados = $this->empleadoModel->getAllActive();
        $salas = $this->salaModel->getAll();

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/reservas/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Guardar nueva reserva
     */
    public function store() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=reservas&action=create');
            exit();
        }

        // Recoger datos básicos de la reserva
        $idCliente = $_POST['id_cliente'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $servicios = $_POST['servicios'] ?? [];

        // Validaciones básicas
        $errors = [];

        if (empty($idCliente)) {
            $errors['id_cliente'] = 'Debe seleccionar un cliente';
        }

        if (empty($fecha)) {
            $errors['fecha'] = 'Debe especificar una fecha';
        }

        if (empty($servicios)) {
            $errors['servicios'] = 'Debe agregar al menos un servicio';
        }

        // Validar cada servicio
        foreach ($servicios as $index => $servicio) {
            $horaInicio = $servicio['hora_inicio'] ?? '';
            $horaFin = $servicio['hora_fin'] ?? '';
            $idServicio = $servicio['id_servicio'] ?? null;
            $idEmpleado = $servicio['id_empleado'] ?? null;
            $idSala = $servicio['id_sala'] ?? null;

            if (empty($idServicio)) {
                $errors["servicio_{$index}_id_servicio"] = 'Debe seleccionar un servicio';
            }

            if (empty($horaInicio)) {
                $errors["servicio_{$index}_hora_inicio"] = 'Debe especificar hora de inicio';
            }

            if (empty($horaFin)) {
                $errors["servicio_{$index}_hora_fin"] = 'Debe especificar hora de fin';
            }

            // Validar que hora fin sea mayor que hora inicio
            if (!empty($horaInicio) && !empty($horaFin) && $horaFin <= $horaInicio) {
                $errors["servicio_{$index}_hora"] = 'La hora de fin debe ser mayor que la hora de inicio';
            }

            // Validar disponibilidad de empleado
            if (!empty($idEmpleado) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin)) {
                if (!$this->detalleReservaModel->isEmpleadoDisponible($idEmpleado, $fecha, $horaInicio, $horaFin)) {
                    $errors["servicio_{$index}_empleado"] = 'El terapeuta no está disponible en ese horario';
                }
            }

            // Validar disponibilidad de sala
            if (!empty($idSala) && !empty($fecha) && !empty($horaInicio) && !empty($horaFin)) {
                if (!$this->detalleReservaModel->isSalaDisponible($idSala, $fecha, $horaInicio, $horaFin)) {
                    $errors["servicio_{$index}_sala"] = 'La sala no está disponible en ese horario';
                }
            }
        }

        // Si hay errores, regresar al formulario
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=reservas&action=create');
            exit();
        }

        // Crear la reserva
        try {
            // Iniciar transacción
            $db = getDB();
            $db->beginTransaction();

            // Crear reserva principal
            $idReserva = $this->reservaModel->create([
                'fecha' => $fecha,
                'estado' => 'Pendiente',
                'id_cliente' => $idCliente
            ]);

            // Crear detalles de reserva (servicios)
            foreach ($servicios as $servicio) {
                // Obtener precio del servicio
                $servicioData = $this->servicioModel->getById($servicio['id_servicio']);
                
                $this->detalleReservaModel->create([
                    'hora_inicio' => $servicio['hora_inicio'],
                    'hora_fin' => $servicio['hora_fin'],
                    'precio' => $servicioData['precio'],
                    'observaciones' => $servicio['observaciones'] ?? null,
                    'id_reserva' => $idReserva,
                    'id_servicio' => $servicio['id_servicio'],
                    'id_empleado' => $servicio['id_empleado'] ?? null,
                    'id_sala' => $servicio['id_sala'] ?? null
                ]);
            }

            // Confirmar transacción
            $db->commit();

            $_SESSION['flash']['success'] = 'Reserva creada exitosamente';
            header('Location: index.php?page=reservas');
            exit();

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($db)) {
                $db->rollBack();
            }
            
            $_SESSION['flash']['error'] = 'Error al crear la reserva: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=reservas&action=create');
            exit();
        }
    }
}
