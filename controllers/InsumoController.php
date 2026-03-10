<?php
/**
 * ════════════════════════════════════════════════
 * CONTROLADOR: Insumos
 * ════════════════════════════════════════════════
 * RF009 - GESTIÓN DE INSUMOS
 * 
 * Acceso: Solo Administrador
 */

class InsumoController {

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
     * Listado de insumos
     */
    public function index(): void {
        $this->checkAuth();
        
        $insumoModel = new Insumo();
        $insumos = $insumoModel->getAll();
        
        require 'views/layout/header.php';
        require 'views/insumos/index.php';
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
        require 'views/insumos/create.php';
        require 'views/layout/footer.php';
    }

    /**
     * Guardar nuevo insumo
     */
    public function store(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=insumos&action=create');
            exit;
        }
        
        // Validación
        $errors = $this->validate($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=insumos&action=create');
            exit;
        }
        
        $insumoModel = new Insumo();
        $db = getDB();
        
        try {
            $db->beginTransaction();
            
            // Crear insumo
            $idInsumo = $insumoModel->create([
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'stock' => $_POST['stock'] ?? 0,
                'stock_minimo' => $_POST['stock_minimo'] ?? 0,
                'unidad_medida' => $_POST['unidad_medida'],
                'costo_unitario' => $_POST['costo_unitario'] ?? 0,
            ]);
            
            // Registrar movimiento inicial si hay stock
            $stockInicial = floatval($_POST['stock'] ?? 0);
            if ($stockInicial > 0) {
                $insumoModel->registrarMovimiento([
                    'id_insumo' => $idInsumo,
                    'tipo' => 'entrada',
                    'cantidad' => $stockInicial,
                    'motivo' => 'Stock inicial registrado',
                    'id_usuario' => $_SESSION['user_id'],
                ]);
            }
            
            $db->commit();
            
            $_SESSION['flash']['success'] = 'Insumo creado exitosamente';
            header('Location: index.php?page=insumos');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Error al crear insumo: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=insumos&action=create');
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
            header('Location: index.php?page=insumos');
            exit;
        }
        
        $insumoModel = new Insumo();
        $insumo = $insumoModel->getById($id);
        
        if (!$insumo) {
            $_SESSION['flash']['error'] = 'Insumo no encontrado';
            header('Location: index.php?page=insumos');
            exit;
        }
        
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);
        
        require 'views/layout/header.php';
        require 'views/insumos/edit.php';
        require 'views/layout/footer.php';
    }

    /**
     * Actualizar insumo
     */
    public function update(): void {
        $this->checkAuth();
        
        // Verificar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash']['error'] = 'Token CSRF inválido';
            header('Location: index.php?page=insumos');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=insumos');
            exit;
        }
        
        // Validación
        $errors = $this->validate($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=insumos&action=edit&id=' . $id);
            exit;
        }
        
        $insumoModel = new Insumo();
        $db = getDB();
        
        try {
            $db->beginTransaction();
            
            // Obtener stock anterior
            $insumoAnterior = $insumoModel->getById($id);
            $stockAnterior = floatval($insumoAnterior['stock']);
            $stockNuevo = floatval($_POST['stock']);
            
            // Actualizar insumo
            $insumoModel->update($id, [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'stock' => $stockNuevo,
                'stock_minimo' => $_POST['stock_minimo'] ?? 0,
                'unidad_medida' => $_POST['unidad_medida'],
                'costo_unitario' => $_POST['costo_unitario'] ?? 0,
            ]);
            
            // Si cambió el stock, registrar movimiento
            if ($stockNuevo != $stockAnterior) {
                $tipo = $stockNuevo > $stockAnterior ? 'entrada' : 'salida';
                $cantidad = abs($stockNuevo - $stockAnterior);
                
                $insumoModel->registrarMovimiento([
                    'id_insumo' => $id,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'motivo' => 'Ajuste manual de inventario',
                    'id_usuario' => $_SESSION['user_id'],
                ]);
            }
            
            $db->commit();
            
            $_SESSION['flash']['success'] = 'Insumo actualizado exitosamente';
            header('Location: index.php?page=insumos');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Error al actualizar insumo: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=insumos&action=edit&id=' . $id);
            exit;
        }
    }

    /**
     * Eliminar insumo
     */
    public function delete(): void {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash']['error'] = 'ID no especificado';
            header('Location: index.php?page=insumos');
            exit;
        }
        
        $insumoModel = new Insumo();
        
        $insumo = $insumoModel->getById($id);
        if (!$insumo) {
            $_SESSION['flash']['error'] = 'Insumo no encontrado';
            header('Location: index.php?page=insumos');
            exit;
        }
        
        // Verificar si está asignado a servicios
        if ($insumoModel->isAsignadoAServicios($id)) {
            $_SESSION['flash']['error'] = "No se puede eliminar. El insumo está asignado a uno o más servicios.";
            header('Location: index.php?page=insumos');
            exit;
        }
        
        $db = getDB();
        
        try {
            $db->beginTransaction();
            
            // Eliminar movimientos de inventario
            $insumoModel->deleteMovimientos($id);
            
            // Eliminar insumo
            $insumoModel->delete($id);
            
            $db->commit();
            
            $_SESSION['flash']['success'] = 'Insumo eliminado exitosamente';
            header('Location: index.php?page=insumos');
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Error al eliminar insumo: ' . $e->getMessage();
            header('Location: index.php?page=insumos');
            exit;
        }
    }

    /**
     * Validar datos del insumo
     */
    private function validate($data): array {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }
        
        if (!isset($data['stock']) || $data['stock'] < 0) {
            $errors['stock'] = 'El stock debe ser mayor o igual a 0';
        }
        
        if (!isset($data['stock_minimo']) || $data['stock_minimo'] < 0) {
            $errors['stock_minimo'] = 'El stock mínimo debe ser mayor o igual a 0';
        }
        
        if (empty($data['unidad_medida'])) {
            $errors['unidad_medida'] = 'La unidad de medida es obligatoria';
        }
        
        if (isset($data['costo_unitario']) && $data['costo_unitario'] < 0) {
            $errors['costo_unitario'] = 'El costo unitario debe ser mayor o igual a 0';
        }
        
        return $errors;
    }
}
