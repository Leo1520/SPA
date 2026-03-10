<?php
/**
 * ════════════════════════════════════════
 * CONTROLADOR: ClienteController
 * ════════════════════════════════════════
 * Gestión CRUD de clientes
 * RF001 - GESTIÓN DE CLIENTES
 */

class ClienteController {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    /**
     * Listado de clientes con búsqueda
     */
    public function index() {
        // Cargar todos los clientes (el filtrado se hace en el frontend)
        $clientes = $this->clienteModel->getAll();
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/clientes/index.php';
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
        require_once __DIR__ . '/../views/clientes/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Guardar nuevo cliente
     */
    public function store() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=clientes&action=create');
            exit();
        }

        // Recoger datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null
        ];

        // Validaciones
        $errors = $this->validateCliente($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?page=clientes&action=create');
            exit();
        }

        // Crear cliente
        try {
            $this->clienteModel->create($data);
            $_SESSION['flash']['success'] = 'Cliente registrado exitosamente';
            header('Location: index.php?page=clientes');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al guardar el cliente: ' . $e->getMessage();
            $_SESSION['old'] = $data;
            header('Location: index.php?page=clientes&action=create');
            exit();
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $_SESSION['flash']['error'] = 'Cliente no especificado';
            header('Location: index.php?page=clientes');
            exit();
        }

        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $_SESSION['flash']['error'] = 'Cliente no encontrado';
            header('Location: index.php?page=clientes');
            exit();
        }

        // Generar token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/clientes/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Actualizar cliente
     */
    public function update() {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token de seguridad inválido';
            header('Location: index.php?page=clientes');
            exit();
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Cliente no especificado';
            header('Location: index.php?page=clientes');
            exit();
        }

        // Recoger datos del formulario
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null
        ];

        // Validaciones (pasando el ID para excluir en verificación de CI)
        $errors = $this->validateCliente($data, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=clientes&action=edit&id=' . $id);
            exit();
        }

        // Actualizar cliente
        try {
            $this->clienteModel->update($id, $data);
            $_SESSION['flash']['success'] = 'Cliente actualizado exitosamente';
            header('Location: index.php?page=clientes');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar el cliente: ' . $e->getMessage();
            header('Location: index.php?page=clientes&action=edit&id=' . $id);
            exit();
        }
    }

    /**
     * Eliminar cliente
     */
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Cliente no especificado';
            header('Location: index.php?page=clientes');
            exit();
        }

        // Verificar si el cliente tiene reservas
        if ($this->clienteModel->hasReservas($id)) {
            $_SESSION['flash']['error'] = 'No se puede eliminar el cliente porque tiene reservas asociadas';
            header('Location: index.php?page=clientes');
            exit();
        }

        // Eliminar cliente
        try {
            $this->clienteModel->delete($id);
            $_SESSION['flash']['success'] = 'Cliente eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar el cliente: ' . $e->getMessage();
        }

        header('Location: index.php?page=clientes');
        exit();
    }

    /**
     * Validar datos del cliente
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validateCliente($data, $excludeId = null) {
        $errors = [];

        // Nombre obligatorio
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }

        // Apellido obligatorio
        if (empty($data['apellido'])) {
            $errors['apellido'] = 'El apellido es obligatorio';
        }

        // CI obligatorio y único
        if (empty($data['ci'])) {
            $errors['ci'] = 'El CI es obligatorio';
        } elseif ($this->clienteModel->existsCI($data['ci'], $excludeId)) {
            $errors['ci'] = 'El CI ya está registrado';
        }

        // Email obligatorio y válido
        if (empty($data['email'])) {
            $errors['email'] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no tiene un formato válido';
        }

        return $errors;
    }
}
