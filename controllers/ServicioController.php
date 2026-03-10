<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: ServicioController
 * ════════════════════════════════════════
 * Gestión del catálogo de servicios
 * RF007 - GESTIÓN DEL CATÁLOGO DE SERVICIOS
 */

class ServicioController {
    private $servicioModel;

    public function __construct() {
        // Solo Admin puede gestionar servicios
        if ($_SESSION['id_rol'] !== 1) {
            $_SESSION['flash']['error'] = 'Solo el administrador puede gestionar servicios';
            header('Location: index.php?page=reservas');
            exit();
        }

        $this->servicioModel = new Servicio();
    }

    /**
     * Listado de servicios
     */
    public function index() {
        $servicios = $this->servicioModel->getAll();
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/servicios/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function create() {
        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/servicios/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Guardar nuevo servicio
     */
    public function store() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=servicios&action=create');
            exit();
        }

        // Recoger datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'duracion_min' => intval($_POST['duracion_min'] ?? 0),
            'precio' => floatval($_POST['precio'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        // Validaciones
        $errors = $this->validateServicio($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?page=servicios&action=create');
            exit();
        }

        // Crear servicio
        try {
            $this->servicioModel->create($data);
            $_SESSION['flash']['success'] = 'Servicio registrado exitosamente';
            header('Location: index.php?page=servicios');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al guardar el servicio: ' . $e->getMessage();
            $_SESSION['old'] = $data;
            header('Location: index.php?page=servicios&action=create');
            exit();
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $_SESSION['flash']['error'] = 'Servicio no especificado';
            header('Location: index.php?page=servicios');
            exit();
        }

        $servicio = $this->servicioModel->getById($id);
        
        if (!$servicio) {
            $_SESSION['flash']['error'] = 'Servicio no encontrado';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? $servicio;
        unset($_SESSION['errors'], $_SESSION['old']);

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/servicios/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Actualizar servicio
     */
    public function update() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=servicios');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Verificar que el servicio existe
        $servicio = $this->servicioModel->getById($id);
        
        if (!$servicio) {
            $_SESSION['flash']['error'] = 'Servicio no encontrado';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Recoger datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'duracion_min' => intval($_POST['duracion_min'] ?? 0),
            'precio' => floatval($_POST['precio'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        // Validaciones
        $errors = $this->validateServicio($data, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?page=servicios&action=edit&id=' . $id);
            exit();
        }

        // Actualizar servicio
        try {
            $this->servicioModel->update($id, $data);
            $_SESSION['flash']['success'] = 'Servicio actualizado exitosamente';
            header('Location: index.php?page=servicios');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar el servicio: ' . $e->getMessage();
            $_SESSION['old'] = $data;
            header('Location: index.php?page=servicios&action=edit&id=' . $id);
            exit();
        }
    }

    /**
     * Alternar estado activo/inactivo
     */
    public function toggle() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Servicio no especificado';
            header('Location: index.php?page=servicios');
            exit();
        }

        try {
            $this->servicioModel->toggle($id);
            $_SESSION['flash']['success'] = 'Estado del servicio actualizado';
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al cambiar el estado: ' . $e->getMessage();
        }

        header('Location: index.php?page=servicios');
        exit();
    }

    /**
     * Eliminar servicio
     */
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Servicio no especificado';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Verificar que el servicio existe
        $servicio = $this->servicioModel->getById($id);
        
        if (!$servicio) {
            $_SESSION['flash']['error'] = 'Servicio no encontrado';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Verificar que no tenga reservas asociadas
        if ($this->servicioModel->hasDetalleReserva($id)) {
            $_SESSION['flash']['error'] = 'No se puede eliminar, tiene reservas asociadas. Desactívelo en su lugar.';
            header('Location: index.php?page=servicios');
            exit();
        }

        // Verificar que no tenga ventas asociadas
        if ($this->servicioModel->hasDetalleVenta($id)) {
            $_SESSION['flash']['error'] = 'No se puede eliminar, tiene ventas asociadas. Desactívelo en su lugar.';
            header('Location: index.php?page=servicios');
            exit();
        }

        try {
            $this->servicioModel->delete($id);
            $_SESSION['flash']['success'] = 'Servicio eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar el servicio: ' . $e->getMessage();
        }

        header('Location: index.php?page=servicios');
        exit();
    }

    /**
     * Validar datos de servicio
     * @param array $data
     * @param int|null $id
     * @return array
     */
    private function validateServicio($data, $id = null) {
        $errors = [];

        // Nombre
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($data['nombre']) > 100) {
            $errors['nombre'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->servicioModel->existsByNombre($data['nombre'], $id)) {
            $errors['nombre'] = 'Ya existe un servicio con ese nombre';
        }

        // Duración
        if ($data['duracion_min'] <= 0) {
            $errors['duracion_min'] = 'La duración debe ser mayor a 0';
        }

        // Precio
        if ($data['precio'] <= 0) {
            $errors['precio'] = 'El precio debe ser mayor a 0';
        }

        return $errors;
    }
}
