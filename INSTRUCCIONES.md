# SPA LAS AMÉRICA - Sistema de Gestión
## Instrucciones de Instalación y Configuración

════════════════════════════════════════════════════════════════
## 📋 REQUISITOS PREVIOS
════════════════════════════════════════════════════════════════

- **Laragon** instalado y ejecutándose
- **PHP 7.4+**
- **MySQL/MariaDB** en puerto 3306
- Base de datos `spa_america` creada (según archivo SPA_AMERICA.sql)

════════════════════════════════════════════════════════════════
## 🚀 PASOS DE INSTALACIÓN
════════════════════════════════════════════════════════════════

### 1. Verificar Base de Datos

Asegúrese de que la base de datos `spa_america` existe. Si no existe, créela:

```sql
CREATE DATABASE spa_america CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego ejecute el script de creación de tablas y población de datos que ya tiene.

### 2. Cargar Datos de Prueba

**Ya tiene un script completo de población de datos** en el archivo `SPA_AMERICA.sql` que incluye:
- Roles (Administrador, Recepcionista, Cajero, Terapeuta)
- 8 Empleados con diferentes cargos
- 6 Salas
- 15 Servicios
- 15 Clientes
- Reservas de ejemplo (completadas, confirmadas, pendientes, canceladas)
- Usuarios del sistema

**Ejecute el script de población completo:**
```bash
# En MySQL Workbench o terminal de Laragon:
mysql -u root -p spa_america < SPA_AMERICA.sql
```

O ejecute directamente el contenido del archivo en MySQL Workbench.

### 3. Usuarios Disponibles

El sistema viene con los siguientes usuarios precargados:

| Usuario  | Contraseña | Rol            |
|----------|------------|----------------|
| admin    | password   | Administrador  |
| valeria  | password   | Recepcionista  |
| sofia    | password   | Terapeuta      |
| daniela  | password   | Terapeuta      |
| luciana  | password   | Terapeuta      |
| roberto  | password   | Terapeuta      |
| marcela  | password   | Cajero         |
| fernando | password   | Recepcionista  |

### 4. Acceder al Sistema

1. Abra su navegador y vaya a: `http://localhost/SPA/`
2. El sistema lo redirigirá automáticamente a la página de login
3. Ingrese las credenciales:
   - **Usuario:** admin
   - **Contraseña:** password

════════════════════════════════════════════════════════════════
## 🎯 FUNCIONALIDADES IMPLEMENTADAS
════════════════════════════════════════════════════════════════

### ✅ RF005 - LOGIN Y ROLES (Autenticación)

- ✓ Login con usuario y contraseña
- ✓ Verificación de cuenta activa
- ✓ Contador de intentos fallidos (máximo 3)
- ✓ Bloqueo temporal de 15 minutos tras 3 intentos
- ✓ Sesión con timeout de 30 minutos por inactividad
- ✓ Cerrar sesión
- ✓ Control de acceso por rol

### ✅ RF001 - GESTIÓN DE CLIENTES (CRUD Completo)

- ✓ Listado de clientes con búsqueda
- ✓ Crear nuevo cliente con validaciones:
  - Nombre, apellido, CI y email obligatorios
  - CI único
  - Formato de email válido
- ✓ Editar cliente existente
- ✓ Eliminar cliente (con verificación de reservas asociadas)
- ✓ Mensajes flash de confirmación

### ✅ RF002 - GESTIÓN DE RESERVAS

- ✓ Listado de reservas con filtros por estado
- ✓ Badges de color por estado (Pendiente/Confirmada/Completada/Cancelada)
- ✓ Crear nueva reserva:
  - Selección de cliente y fecha
  - Agregar múltiples servicios dinámicamente
  - Selección de terapeuta y sala por servicio
  - Horarios de inicio y fin
- ✓ Validaciones de disponibilidad:
  - Terapeuta no puede tener conflictos de horario
  - Sala no puede estar ocupada en el mismo horario
  - Hora fin debe ser mayor que hora inicio
- ✓ Transacciones para garantizar integridad de datos

════════════════════════════════════════════════════════════════
## 🎨 PALETA DE COLORES APLICADA
════════════════════════════════════════════════════════════════

- **Fondo general:** #F5F5F0 (crema suave)
- **Sidebar:** #2C3E35 (verde oscuro)
- **Sidebar texto:** #A8C5A0 (verde claro)
- **Sidebar activo:** #FFFFFF con fondo #3D5A4C
- **Topbar:** #FFFFFF con sombra sutil
- **Botón primario:** #4A7C5F hover #3D6B50
- **Botón secundario:** #FFFFFF borde #4A7C5F
- **Botón peligro:** #C0392B hover #A93226
- **Éxito:** #27AE60 fondo #EAFAF1
- **Error:** #E74C3C fondo #FDECEA

════════════════════════════════════════════════════════════════
## 📁 ESTRUCTURA DEL PROYECTO
════════════════════════════════════════════════════════════════

```
SPA/
├── index.php                    # Router principal
├── config/
│   └── db.php                   # Conexión PDO
├── controllers/
│   ├── AuthController.php       # Autenticación
│   ├── ClienteController.php    # CRUD Clientes
│   └── ReservaController.php    # Gestión Reservas
├── models/
│   ├── Usuario.php
│   ├── Cliente.php
│   ├── Reserva.php
│   ├── DetalleReserva.php
│   ├── Servicio.php
│   ├── Empleado.php
│   └── Sala.php
├── views/
│   ├── layout/
│   │   ├── header.php           # Topbar + Sidebar
│   │   └── footer.php
│   ├── auth/
│   │   └── login.php
│   ├── clientes/
│   │   ├── index.php            # Listado
│   │   ├── create.php           # Crear
│   │   └── edit.php             # Editar
│   └── reservas/
│       ├── index.php            # Listado
│       └── create.php           # Crear
└── assets/
    ├── css/
    │   └── style.css            # Estilos completos
    └── js/
        └── app.js               # JavaScript vanilla
```

════════════════════════════════════════════════════════════════
## 🔒 SEGURIDAD IMPLEMENTADA
════════════════════════════════════════════════════════════════

- ✓ PDO con prepared statements (previene SQL injection)
- ✓ htmlspecialchars() en toda salida HTML (previene XSS)
- ✓ Tokens CSRF en todos los formularios
- ✓ Verificación de sesión en rutas protegidas
- ✓ Password hashing con password_hash() y password_verify()
- ✓ Validación de datos del lado servidor
- ✓ Timeout de sesión por inactividad

════════════════════════════════════════════════════════════════
## 📝 NOTAS IMPORTANTES
════════════════════════════════════════════════════════════════

1. **Sistema de Contraseñas:** El sistema soporta tanto passwords con `password_hash()` 
   como SHA256. Todos los usuarios del script de población usan password_hash() 
   con la contraseña 'password'.

2. **Datos Precargados:** El sistema incluye 15 clientes, 8 empleados, 15 servicios, 
   6 salas y reservas de ejemplo en diferentes estados.

3. **Roles:** El sistema verifica permisos por rol. Solo Administrador y 
   Recepcionista pueden acceder a Clientes y Reservas.

3. **Transacciones:** La creación de reservas usa transacciones para garantizar 
   que si falla algún detalle, se revierta toda la operación.

4. **Validaciones:** Las validaciones se realizan tanto en cliente (JavaScript) 
   como en servidor (PHP) para mayor seguridad.

5. **Sin Frameworks:** Todo el sistema está construido con PHP puro, CSS puro 
   y JavaScript vanilla (sin jQuery, Bootstrap, etc.).

════════════════════════════════════════════════════════════════
## 🐛 SOLUCIÓN DE PROBLEMAS
════════════════════════════════════════════════════════════════

### Error de conexión a base de datos
- Verifique que MySQL esté ejecutándose en Laragon
- Confirme que la base de datos `spa_america` existe
- Revise las credenciales en `config/db.php`

### No se muestran los estilos
- Verifique que la carpeta `assets/css/` y `assets/js/` existan
- Confirme que el servidor está sirviendo archivos estáticos

### Error "Headers already sent"
- Asegúrese de no tener espacios en blanco antes de `<?php` en los archivos
- No use `echo` antes de los `header()` redirects

════════════════════════════════════════════════════════════════
## 🎉 ¡SISTEMA LISTO!
════════════════════════════════════════════════════════════════

El sistema está completamente funcional y listo para usar en Laragon.
Todos los requisitos funcionales RF001, RF002 y RF005 están implementados
con validaciones, seguridad y arquitectura MVC limpia.

¡Disfrute del sistema SPA Las América! 🌿
