<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: VentaController
 * ════════════════════════════════════════
 * Gestión de ventas
 * RF003 - REGISTRAR DETALLE DE VENTA
 */

class VentaController {
    private $ventaModel;
    private $detalleVentaModel;
    private $reservaModel;
    private $detalleReservaModel;

    public function __construct() {
        // Verificar permisos: Solo Admin, Recepcionista y Cajero
        $idRol = $_SESSION['id_rol'];
        if (!in_array($idRol, [1, 2, 3])) {
            $_SESSION['flash']['error'] = 'No tiene permisos para acceder a esta sección';
            header('Location: index.php?page=reservas');
            exit();
        }

        $this->ventaModel = new Venta();
        $this->detalleVentaModel = new DetalleVenta();
        $this->reservaModel = new Reserva();
        $this->detalleReservaModel = new DetalleReserva();
    }

    /**
     * Listado de ventas con filtros
     */
    public function index() {
        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;
        
        $ventas = $this->ventaModel->getAll($fechaDesde, $fechaHasta);
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ventas/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Mostrar formulario para generar venta desde reserva
     */
    public function create() {
        $idReserva = $_GET['id_reserva'] ?? null;

        if (!$idReserva) {
            $_SESSION['flash']['error'] = 'Debe especificar una reserva';
            header('Location: index.php?page=reservas');
            exit();
        }

        // Verificar que la reserva existe
        $reserva = $this->reservaModel->getById($idReserva);
        
        if (!$reserva) {
            $_SESSION['flash']['error'] = 'Reserva no encontrada';
            header('Location: index.php?page=reservas');
            exit();
        }

        // Verificar que la reserva está Completada
        if ($reserva['estado'] !== 'Completada') {
            $_SESSION['flash']['error'] = 'Solo se pueden generar ventas de reservas completadas';
            header('Location: index.php?page=reservas');
            exit();
        }

        // Verificar que no existe venta previa
        if ($this->ventaModel->existsByReserva($idReserva)) {
            $_SESSION['flash']['error'] = 'Esta reserva ya tiene una venta registrada';
            header('Location: index.php?page=reservas');
            exit();
        }

        // Obtener detalles de la reserva (servicios)
        $detallesReserva = $this->detalleReservaModel->getByReserva($idReserva);

        // Calcular total
        $total = 0;
        foreach ($detallesReserva as $detalle) {
            $total += $detalle['precio'];
        }

        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ventas/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Guardar nueva venta
     */
    public function store() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=ventas');
            exit();
        }

        $idReserva = $_POST['id_reserva'] ?? null;
        $descuento = floatval($_POST['descuento'] ?? 0);

        if (!$idReserva) {
            $_SESSION['flash']['error'] = 'Datos insuficientes';
            header('Location: index.php?page=ventas');
            exit();
        }

        try {
            // Verificar que la reserva existe y está completada
            $reserva = $this->reservaModel->getById($idReserva);
            
            if (!$reserva || $reserva['estado'] !== 'Completada') {
                $_SESSION['flash']['error'] = 'Reserva no válida para generar venta';
                header('Location: index.php?page=reservas');
                exit();
            }

            // Verificar que no existe venta previa
            if ($this->ventaModel->existsByReserva($idReserva)) {
                $_SESSION['flash']['error'] = 'Esta reserva ya tiene una venta registrada';
                header('Location: index.php?page=reservas');
                exit();
            }

            // Obtener detalles de la reserva
            $detallesReserva = $this->detalleReservaModel->getByReserva($idReserva);

            // Calcular total
            $subtotalGeneral = 0;
            foreach ($detallesReserva as $detalle) {
                $subtotalGeneral += $detalle['precio'];
            }

            // Aplicar descuento
            $total = $subtotalGeneral - $descuento;

            if ($total <= 0) {
                $_SESSION['flash']['error'] = 'El total de la venta debe ser mayor a cero';
                header('Location: index.php?page=ventas&action=create&id_reserva=' . $idReserva);
                exit();
            }

            // Iniciar transacción
            $db = getDB();
            $db->beginTransaction();

            // Crear venta
            $idVenta = $this->ventaModel->create([
                'total' => $total,
                'id_reserva' => $idReserva
            ]);

            // Crear detalles de venta desde los detalles de reserva
            foreach ($detallesReserva as $detalle) {
                $this->detalleVentaModel->create([
                    'cantidad' => 1,
                    'precio_unitario' => $detalle['precio'],
                    'subtotal' => $detalle['precio'],
                    'id_venta' => $idVenta,
                    'id_servicio' => $detalle['id_servicio']
                ]);
            }

            // Confirmar transacción
            $db->commit();

            $_SESSION['flash']['success'] = 'Venta registrada exitosamente';
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($db)) {
                $db->rollBack();
            }
            
            $_SESSION['flash']['error'] = 'Error al registrar la venta: ' . $e->getMessage();
            header('Location: index.php?page=ventas&action=create&id_reserva=' . $idReserva);
            exit();
        }
    }

    /**
     * Ver detalle de una venta con pagos y factura
     * RF003 + RF004
     */
    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Venta no especificada';
            header('Location: index.php?page=ventas');
            exit();
        }

        $venta = $this->ventaModel->getById($id);
        
        if (!$venta) {
            $_SESSION['flash']['error'] = 'Venta no encontrada';
            header('Location: index.php?page=ventas');
            exit();
        }

        // Obtener detalles de la venta
        $detalles = $this->detalleVentaModel->getByVenta($id);

        // Obtener pagos registrados
        $pagoModel = new Pago();
        $pagos = $pagoModel->getByVenta($id);
        $totalPagado = $pagoModel->getTotalPagado($id);
        $saldoPendiente = $venta['total'] - $totalPagado;

        // Verificar si tiene factura
        $facturaModel = new Factura();
        $tieneFactura = $facturaModel->existsByVenta($id);
        $factura = $tieneFactura ? $facturaModel->getByVenta($id) : null;

        // Obtener métodos de pago para el formulario
        $metodoPagoModel = new MetodoPago();
        $metodosPago = $metodoPagoModel->getAll();

        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ventas/show.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
