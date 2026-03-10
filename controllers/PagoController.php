<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: PagoController
 * ════════════════════════════════════════
 * Gestión de pagos y facturas
 * RF004 - REGISTRAR PAGO Y EMITIR FACTURA
 */

class PagoController {
    private $pagoModel;
    private $facturaModel;
    private $ventaModel;

    public function __construct() {
        // Verificar permisos: Solo Admin y Cajero pueden registrar pagos
        $idRol = $_SESSION['id_rol'];
        if (!in_array($idRol, [1, 3])) {
            $_SESSION['flash']['error'] = 'No tiene permisos para registrar pagos';
            header('Location: index.php?page=ventas');
            exit();
        }

        $this->pagoModel = new Pago();
        $this->facturaModel = new Factura();
        $this->ventaModel = new Venta();
    }

    /**
     * Registrar nuevo pago
     * RF004 - POST ?page=ventas&action=storePago
     */
    public function store() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=ventas');
            exit();
        }

        $idVenta = $_POST['id_venta'] ?? null;
        $monto = floatval($_POST['monto'] ?? 0);
        $idMetodoPago = $_POST['id_metodo_pago'] ?? null;

        // Validaciones
        $errors = [];

        if (!$idVenta) {
            $errors[] = 'Venta no especificada';
        }

        if ($monto <= 0) {
            $errors[] = 'El monto debe ser mayor a cero';
        }

        if (!$idMetodoPago) {
            $errors[] = 'Debe seleccionar un método de pago';
        }

        // Verificar que la venta existe
        $venta = $this->ventaModel->getById($idVenta);
        
        if (!$venta) {
            $errors[] = 'Venta no encontrada';
        }

        // Calcular saldo pendiente
        if ($venta) {
            $totalPagado = $this->pagoModel->getTotalPagado($idVenta);
            $saldoPendiente = $venta['total'] - $totalPagado;

            // Validar que el monto no supere el saldo pendiente
            if ($monto > $saldoPendiente) {
                $errors[] = 'El monto no puede ser mayor al saldo pendiente (Bs. ' . number_format($saldoPendiente, 2) . ')';
            }
        }

        // Si hay errores, regresar
        if (!empty($errors)) {
            $_SESSION['flash']['error'] = implode('. ', $errors);
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();
        }

        try {
            // Registrar pago
            $this->pagoModel->create([
                'monto' => $monto,
                'id_venta' => $idVenta,
                'id_metodo_pago' => $idMetodoPago
            ]);

            $_SESSION['flash']['success'] = 'Pago registrado exitosamente';
            
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al registrar el pago: ' . $e->getMessage();
        }

        header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
        exit();
    }

    /**
     * Mostrar formulario para emitir factura
     * RF004 - ?page=ventas&action=emitirFactura&id_venta=X
     */
    public function emitirFactura() {
        $idVenta = $_GET['id_venta'] ?? null;

        if (!$idVenta) {
            $_SESSION['flash']['error'] = 'Venta no especificada';
            header('Location: index.php?page=ventas');
            exit();
        }

        // Obtener venta
        $venta = $this->ventaModel->getById($idVenta);
        
        if (!$venta) {
            $_SESSION['flash']['error'] = 'Venta no encontrada';
            header('Location: index.php?page=ventas');
            exit();
        }

        // Verificar que no exista factura previa
        if ($this->facturaModel->existsByVenta($idVenta)) {
            $_SESSION['flash']['error'] = 'Esta venta ya tiene factura emitida';
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();
        }

        // Verificar que el saldo esté pagado completamente
        $totalPagado = $this->pagoModel->getTotalPagado($idVenta);
        $saldoPendiente = $venta['total'] - $totalPagado;

        if ($saldoPendiente > 0) {
            $_SESSION['flash']['error'] = 'No se puede emitir factura con saldo pendiente (Bs. ' . number_format($saldoPendiente, 2) . ')';
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();
        }

        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ventas/emitirFactura.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Guardar factura
     * RF004 - POST ?page=ventas&action=storeFactura
     */
    public function storeFactura() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=ventas');
            exit();
        }

        $idVenta = $_POST['id_venta'] ?? null;
        $nit = trim($_POST['nit'] ?? '');
        $razonSocial = trim($_POST['razon_social'] ?? '');

        // Validaciones
        $errors = [];

        if (!$idVenta) {
            $_SESSION['flash']['error'] = 'Venta no especificada';
            header('Location: index.php?page=ventas');
            exit();
        }

        if (empty($nit)) {
            $errors['nit'] = 'El NIT es obligatorio';
        }

        if (empty($razonSocial)) {
            $errors['razon_social'] = 'La razón social es obligatoria';
        }

        // Verificar que la venta existe
        $venta = $this->ventaModel->getById($idVenta);
        
        if (!$venta) {
            $_SESSION['flash']['error'] = 'Venta no encontrada';
            header('Location: index.php?page=ventas');
            exit();
        }

        // Verificar que no exista factura previa
        if ($this->facturaModel->existsByVenta($idVenta)) {
            $_SESSION['flash']['error'] = 'Esta venta ya tiene factura emitida';
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();
        }

        // Verificar saldo
        $totalPagado = $this->pagoModel->getTotalPagado($idVenta);
        $saldoPendiente = $venta['total'] - $totalPagado;

        if ($saldoPendiente > 0) {
            $_SESSION['flash']['error'] = 'No se puede emitir factura con saldo pendiente';
            header('Location: index.php?page=ventas&action=show&id=' . $idVenta);
            exit();
        }

        // Si hay errores de validación, regresar
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=ventas&action=emitirFactura&id_venta=' . $idVenta);
            exit();
        }

        try {
            // Crear factura
            $idFactura = $this->facturaModel->create([
                'nit_cliente' => $nit,
                'razon_social' => $razonSocial,
                'total' => $venta['total'],
                'id_venta' => $idVenta
            ]);

            $_SESSION['flash']['success'] = 'Factura emitida exitosamente';
            header('Location: index.php?page=ventas&action=factura&id=' . $idFactura);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al emitir la factura: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=ventas&action=emitirFactura&id_venta=' . $idVenta);
            exit();
        }
    }

    /**
     * Ver factura imprimible
     * RF004 - ?page=ventas&action=factura&id=X
     */
    public function verFactura() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Factura no especificada';
            header('Location: index.php?page=ventas');
            exit();
        }

        $factura = $this->facturaModel->getById($id);
        
        if (!$factura) {
            $_SESSION['flash']['error'] = 'Factura no encontrada';
            header('Location: index.php?page=ventas');
            exit();
        }

        // Obtener detalles de la venta
        $detallesVenta = $this->ventaModel->getDetalles($factura['id_venta']);

        require_once __DIR__ . '/../views/ventas/factura.php';
    }
}
