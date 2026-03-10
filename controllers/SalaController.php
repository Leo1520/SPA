<?php
/**
 * ════════════════════════════════════════════════
 * CONTROLADOR: Salas
 * ════════════════════════════════════════════════
 * RF008 - GESTIÓN DE SALAS
 * 
 * Acceso: Solo Administrador
 */

class SalaController {

    /**
     * Verificar autenticación y permisos de administrador
     */
    private function checkAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SESSION['rol'] !== 'Administrador') {
            $_SESSION['flash']['error'] = 'No tiene permisos para acceder a este módulo';
            header('Location: index.php?page=clientes');
            exit;
        }
    }

    /**
     * Listado de salas
     */
    public function index(): void {
        $this->checkAuth();
        
        $salaModel = new Sala();
        $salas = $salaModel->getAll();
        
        require 'views/layout/header.php';
        require 'views/salas/index.php';
        require 'views/layout/footer.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): void {
        $this->checkAuth();
        
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        
        require 'views/layout/header.php';
        require 'views/salas/create.php';
        require 'views/layout/footer.php';
    }

    /**
     * Guardar nueva sala
     */
    public function store(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=salas&action=create');
            exit;
        }
        
        // Validación
        $errors = $this->validate($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=salas&action=create');
            exit;
        }
        
        $salaModel = new Sala();
        
        try {
            $salaModel->create([
                'nombre' => $_POST['nombre'],
                'capacidad' => $_POST['capacidad'] ?? null,
                'ubicacion' => $_POST['ubicacion'] ?? null,
            ]);
            
            $_SESSION['flash']['success'] = 'Sala creada exitosamente';
            header('Location: index.php?page=salas');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al crear sala: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=salas&action=create');
            exit;
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(): void {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=salas');
            exit;
        }
        
        $salaModel = new Sala();
        $sala = $salaModel->getById($id);
        
        if (!$sala) {
            $_SESSION['flash']['error'] = 'Sala no encontrada';
            header('Location: index.php?page=salas');
            exit;
        }
        
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        
        require 'views/layout/header.php';
        require 'views/salas/edit.php';
        require 'views/layout/footer.php';
    }

    /**
     * Actualizar sala
     */
    public function update(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=salas');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=salas');
            exit;
        }
        
        // Validación
        $errors = $this->validate($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=salas&action=edit&id=' . $id);
            exit;
        }
        
        $salaModel = new Sala();
        
        try {
            $salaModel->update($id, [
                'nombre' => $_POST['nombre'],
                'capacidad' => $_POST['capacidad'] ?? null,
                'ubicacion' => $_POST['ubicacion'] ?? null,
            ]);
            
            $_SESSION['flash']['success'] = 'Sala actualizada exitosamente';
            header('Location: index.php?page=salas');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar sala: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=salas&action=edit&id=' . $id);
            exit;
        }
    }

    /**
     * Eliminar sala
     */
    public function delete(): void {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=salas');
            exit;
        }
        
        $salaModel = new Sala();
        
        $sala = $salaModel->getById($id);
        if (!$sala) {
            $_SESSION['flash']['error'] = 'Sala no encontrada';
            header('Location: index.php?page=salas');
            exit;
        }
        
        // Verificar reservas futuras
        $count = $salaModel->countReservasFuturas($id);
        if ($count > 0) {
            $_SESSION['flash']['error'] = "No se puede eliminar. La sala tiene reservas futuras asignadas.";
            header('Location: index.php?page=salas');
            exit;
        }
        
        try {
            $salaModel->delete($id);
            $_SESSION['flash']['success'] = 'Sala eliminada exitosamente';
            header('Location: index.php?page=salas');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar sala: ' . $e->getMessage();
            header('Location: index.php?page=salas');
            exit;
        }
    }

    /**
     * Validar datos de la sala
     */
    private function validate($data): array {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }
        
        if (!empty($data['capacidad']) && $data['capacidad'] < 1) {
            $errors['capacidad'] = 'La capacidad debe ser al menos 1';
        }
        
        return $errors;
    }
}
