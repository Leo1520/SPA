<?php
/**
 * ════════════════════════════════════════════════
 * CONTROLADOR: Empleados
 * ════════════════════════════════════════════════
 * RF014 - GESTIÓN DE EMPLEADOS
 * 
 * Acceso: Solo Administrador
 */

class EmpleadoController {

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
     * Listado de empleados
     */
    public function index(): void {
        $this->checkAuth();
        
        $empleadoModel = new Empleado();
        $empleados = $empleadoModel->getAllWithDetails();
        
        require 'views/layout/header.php';
        require 'views/empleados/index.php';
        require 'views/layout/footer.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): void {
        $this->checkAuth();
        
        $empleadoModel = new Empleado();
        $usuarioModel = new Usuario();
        
        // Obtener especialidades y roles para los selects
        $especialidades = $empleadoModel->getAllEspecialidades();
        $roles = $usuarioModel->getAllRoles();
        
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        
        require 'views/layout/header.php';
        require 'views/empleados/create.php';
        require 'views/layout/footer.php';
    }

    /**
     * Guardar nuevo empleado
     */
    public function store(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=empleados&action=create');
            exit;
        }
        
        $empleadoModel = new Empleado();
        $usuarioModel = new Usuario();
        $db = getDB();
        
        // Validación
        $errors = $this->validateEmpleado($_POST);
        
        // Validar CI único
        if ($empleadoModel->existsByCi($_POST['ci'])) {
            $errors['ci'] = 'El CI ya está registrado';
        }
        
        // Si se marcó crear usuario, validar esos campos
        $crearUsuario = isset($_POST['crear_usuario']) && $_POST['crear_usuario'] === '1';
        if ($crearUsuario) {
            $errorsUsuario = $this->validateUsuario($_POST);
            $errors = array_merge($errors, $errorsUsuario);
            
            if ($usuarioModel->existsByUsername($_POST['username'] ?? '')) {
                $errors['username'] = 'El username ya está registrado';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=empleados&action=create');
            exit;
        }
        
        try {
            $db->beginTransaction();
            
            // 1. Crear empleado
            $idEmpleado = $empleadoModel->create([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'ci' => $_POST['ci'],
                'email' => $_POST['email'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'cargo' => $_POST['cargo'],
                'fecha_contratacion' => $_POST['fecha_contratacion'] ?? date('Y-m-d'),
            ]);
            
            // 2. Asignar especialidades
            $especialidades = $_POST['especialidades'] ?? [];
            if (!empty($especialidades)) {
                $empleadoModel->setEspecialidades($idEmpleado, $especialidades);
            }
            
            // 3. Crear usuario si se solicitó
            if ($crearUsuario) {
                $usuarioModel->create([
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'id_rol' => $_POST['id_rol'],
                    'id_empleado' => $idEmpleado,
                    'activo' => 1,
                ]);
            }
            
            $db->commit();
            
            $_SESSION['flash']['success'] = 'Empleado creado exitosamente';
            header('Location: index.php?page=empleados');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Error al crear empleado: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=empleados&action=create');
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
            header('Location: index.php?page=empleados');
            exit;
        }
        
        $empleadoModel = new Empleado();
        $usuarioModel = new Usuario();
        
        $empleado = $empleadoModel->getById($id);
        if (!$empleado) {
            $_SESSION['flash']['error'] = 'Empleado no encontrado';
            header('Location: index.php?page=empleados');
            exit;
        }
        
        // Obtener especialidades actuales
        $especialidadesAsignadas = $empleadoModel->getEspecialidades($id);
        
        // Obtener todas las especialidades y roles
        $especialidades = $empleadoModel->getAllEspecialidades();
        $roles = $usuarioModel->getAllRoles();
        
        // Verificar si tiene usuario
        $tieneUsuario = $empleadoModel->hasUsuario($id);
        
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        
        require 'views/layout/header.php';
        require 'views/empleados/edit.php';
        require 'views/layout/footer.php';
    }

    /**
     * Actualizar empleado
     */
    public function update(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=empleados');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=empleados');
            exit;
        }
        
        $empleadoModel = new Empleado();
        $usuarioModel = new Usuario();
        $db = getDB();
        
        // Validación
        $errors = $this->validateEmpleado($_POST, $id);
        
        // Validar CI único (excluyendo el actual)
        if ($empleadoModel->existsByCi($_POST['ci'], $id)) {
            $errors['ci'] = 'El CI ya está registrado';
        }
        
        // Si se marcó crear usuario nuevo, validar
        $crearUsuario = isset($_POST['crear_usuario']) && $_POST['crear_usuario'] === '1';
        $tieneUsuario = $empleadoModel->hasUsuario($id);
        
        if ($crearUsuario && !$tieneUsuario) {
            $errorsUsuario = $this->validateUsuario($_POST);
            $errors = array_merge($errors, $errorsUsuario);
            
            if ($usuarioModel->existsByUsername($_POST['username'] ?? '')) {
                $errors['username'] = 'El username ya está registrado';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=empleados&action=edit&id=' . $id);
            exit;
        }
        
        try {
            $db->beginTransaction();
            
            // 1. Actualizar empleado
            $empleadoModel->update($id, [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'ci' => $_POST['ci'],
                'email' => $_POST['email'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'cargo' => $_POST['cargo'],
                'fecha_contratacion' => $_POST['fecha_contratacion'],
            ]);
            
            // 2. Actualizar especialidades
            $especialidades = $_POST['especialidades'] ?? [];
            $empleadoModel->setEspecialidades($id, $especialidades);
            
            // 3. Crear usuario si se solicitó y no tenía
            if ($crearUsuario && !$tieneUsuario) {
                $usuarioModel->create([
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'id_rol' => $_POST['id_rol'],
                    'id_empleado' => $id,
                    'activo' => 1,
                ]);
            }
            
            $db->commit();
            
            $_SESSION['flash']['success'] = 'Empleado actualizado exitosamente';
            header('Location: index.php?page=empleados');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Error al actualizar empleado: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=empleados&action=edit&id=' . $id);
            exit;
        }
    }

    /**
     * Activar/Desactivar empleado
     */
    public function toggle(): void {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=empleados');
            exit;
        }
        
        $empleadoModel = new Empleado();
        
        $empleado = $empleadoModel->getById($id);
        if (!$empleado) {
            $_SESSION['flash']['error'] = 'Empleado no encontrado';
            header('Location: index.php?page=empleados');
            exit;
        }
        
        // Si está activo y se intenta desactivar, verificar reservas futuras
        if ($empleado['activo']) {
            $count = $empleadoModel->countReservasFuturas($id);
            if ($count > 0) {
                $_SESSION['flash']['error'] = "No se puede desactivar. El empleado tiene {$count} reserva(s) futura(s) pendiente(s) o confirmada(s).";
                header('Location: index.php?page=empleados');
                exit;
            }
        }
        
        // Cambiar estado
        $empleadoModel->toggle($id);
        
        $nuevoEstado = $empleado['activo'] ? 'desactivado' : 'activado';
        $_SESSION['flash']['success'] = "Empleado {$nuevoEstado} exitosamente";
        header('Location: index.php?page=empleados');
        exit;
    }

    /**
     * Validar datos del empleado
     */
    private function validateEmpleado($data, $id = null): array {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }
        
        if (empty($data['apellido'])) {
            $errors['apellido'] = 'El apellido es obligatorio';
        }
        
        if (empty($data['ci'])) {
            $errors['ci'] = 'El CI es obligatorio';
        }
        
        if (empty($data['cargo'])) {
            $errors['cargo'] = 'El cargo es obligatorio';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        }
        
        return $errors;
    }

    /**
     * Validar datos del usuario
     */
    private function validateUsuario($data): array {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors['username'] = 'El username es obligatorio';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es obligatoria';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if (empty($data['password_confirm'])) {
            $errors['password_confirm'] = 'Debe confirmar la contraseña';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden';
        }
        
        if (empty($data['id_rol'])) {
            $errors['id_rol'] = 'El rol es obligatorio';
        }
        
        return $errors;
    }
}
