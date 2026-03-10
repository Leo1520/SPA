CREATE DATABASE spa_america;
USE spa_america;

-- ===============================
-- ROLES
-- ===============================

CREATE TABLE Rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- ===============================
-- EMPLEADOS
-- ===============================

CREATE TABLE Empleado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    ci VARCHAR(20) UNIQUE,
    email VARCHAR(120),
    telefono VARCHAR(20),
    cargo VARCHAR(80),
    fecha_contratacion DATE,
    activo BOOLEAN DEFAULT TRUE
);

-- ===============================
-- USUARIOS
-- ===============================

CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    id_rol INT NOT NULL,
    id_empleado INT UNIQUE,

    FOREIGN KEY (id_rol) REFERENCES Rol(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id)
);

-- ===============================
-- CLIENTES
-- ===============================

CREATE TABLE Cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    ci VARCHAR(20) UNIQUE,
    email VARCHAR(120),
    telefono VARCHAR(20),
    fecha_nacimiento DATE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ===============================
-- ESPECIALIDADES
-- ===============================

CREATE TABLE Especialidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE Empleado_Especialidad (
    id_empleado INT,
    id_especialidad INT,

    PRIMARY KEY (id_empleado, id_especialidad),

    FOREIGN KEY (id_empleado) REFERENCES Empleado(id),
    FOREIGN KEY (id_especialidad) REFERENCES Especialidad(id)
);

-- ===============================
-- SALAS
-- ===============================

CREATE TABLE Sala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT,
    ubicacion VARCHAR(120)
);

-- ===============================
-- SERVICIOS
-- ===============================

CREATE TABLE Servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    duracion_min INT,
    precio DECIMAL(10,2),
    activo BOOLEAN DEFAULT TRUE
);

-- ===============================
-- INSUMOS
-- ===============================

CREATE TABLE Insumo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    stock DECIMAL(10,2),
    stock_minimo DECIMAL(10,2),
    unidad_medida VARCHAR(50),
    costo_unitario DECIMAL(10,2)
);

-- ===============================
-- RELACION SERVICIO INSUMO
-- ===============================

CREATE TABLE Servicio_Insumo (
    id_servicio INT,
    id_insumo INT,
    cantidad_usada DECIMAL(10,2),

    PRIMARY KEY (id_servicio, id_insumo),

    FOREIGN KEY (id_servicio) REFERENCES Servicio(id),
    FOREIGN KEY (id_insumo) REFERENCES Insumo(id)
);

-- ===============================
-- RESERVAS
-- ===============================

CREATE TABLE Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    estado VARCHAR(30),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_cliente INT,

    FOREIGN KEY (id_cliente) REFERENCES Cliente(id)
);

-- ===============================
-- DETALLE RESERVA
-- ===============================

CREATE TABLE Detalle_Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hora_inicio TIME,
    hora_fin TIME,
    precio DECIMAL(10,2),
    observaciones TEXT,
    id_reserva INT,
    id_servicio INT,
    id_empleado INT,
    id_sala INT,

    FOREIGN KEY (id_reserva) REFERENCES Reserva(id),
    FOREIGN KEY (id_servicio) REFERENCES Servicio(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id),
    FOREIGN KEY (id_sala) REFERENCES Sala(id)
);

-- ===============================
-- VENTAS
-- ===============================

CREATE TABLE Venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),
    id_reserva INT NULL,

    FOREIGN KEY (id_reserva) REFERENCES Reserva(id)
);

-- ===============================
-- DETALLE VENTA
-- ===============================

CREATE TABLE Detalle_Venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    id_venta INT,
    id_servicio INT NULL,
    id_insumo INT NULL,

    FOREIGN KEY (id_venta) REFERENCES Venta(id),
    FOREIGN KEY (id_servicio) REFERENCES Servicio(id),
    FOREIGN KEY (id_insumo) REFERENCES Insumo(id)
);

-- ===============================
-- METODOS DE PAGO
-- ===============================

CREATE TABLE Metodo_Pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- ===============================
-- PAGOS
-- ===============================

CREATE TABLE Pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2),
    id_venta INT,
    id_metodo_pago INT,

    FOREIGN KEY (id_venta) REFERENCES Venta(id),
    FOREIGN KEY (id_metodo_pago) REFERENCES Metodo_Pago(id)
);

-- ===============================
-- FACTURAS
-- ===============================

CREATE TABLE Factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    nit_cliente VARCHAR(30),
    razon_social VARCHAR(150),
    total DECIMAL(10,2),
    id_venta INT UNIQUE,

    FOREIGN KEY (id_venta) REFERENCES Venta(id)
);

-- ===============================
-- MOVIMIENTOS INVENTARIO
-- ===============================

CREATE TABLE Movimiento_Inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(20),
    cantidad DECIMAL(10,2),
    motivo VARCHAR(150),
    id_insumo INT,
    id_detalle_reserva INT,
    id_usuario INT,

    FOREIGN KEY (id_insumo) REFERENCES Insumo(id),
    FOREIGN KEY (id_detalle_reserva) REFERENCES Detalle_Reserva(id),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);