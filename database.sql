CREATE DATABASE IF NOT EXISTS agenda_corte_urbano
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agenda_corte_urbano;

CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'recepcion') NOT NULL DEFAULT 'recepcion',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(30) NOT NULL,
  correo VARCHAR(120) NULL,
  notas TEXT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE empleados (
  id_empleado INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  especialidad VARCHAR(80) NOT NULL,
  telefono VARCHAR(30) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE servicios (
  id_servicio INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) NULL,
  duracion_minutos INT NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE citas (
  id_cita INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  id_empleado INT NOT NULL,
  id_servicio INT NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  estado ENUM('pendiente', 'confirmada', 'atendida', 'cancelada') NOT NULL DEFAULT 'pendiente',
  observaciones TEXT NULL,
  creada_por INT NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_citas_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
  CONSTRAINT fk_citas_empleado FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
  CONSTRAINT fk_citas_servicio FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),
  CONSTRAINT fk_citas_usuario FOREIGN KEY (creada_por) REFERENCES usuarios(id_usuario),
  UNIQUE KEY uq_empleado_fecha_hora (id_empleado, fecha, hora)
) ENGINE=InnoDB;

CREATE TABLE pagos (
  id_pago INT AUTO_INCREMENT PRIMARY KEY,
  id_cita INT NOT NULL UNIQUE,
  metodo ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente', 'pagado') NOT NULL DEFAULT 'pendiente',
  pagado_en DATETIME NULL,
  CONSTRAINT fk_pagos_cita FOREIGN KEY (id_cita) REFERENCES citas(id_cita)
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES
('Administrador', 'admin@corteurbano.com', '$2y$12$U4fLgDgD6s9jneOHwVNBxO1nAIXe8P/ieoaHI96CN65NisBApYMmy', 'admin');

INSERT INTO clientes (nombre, telefono, correo, notas) VALUES
('Laura Martinez', '3001112233', 'laura@email.com', 'Prefiere citas en la tarde.'),
('Carlos Rios', '3014445566', 'carlos@email.com', 'Corte clasico.'),
('Andres Gomez', '3027778899', NULL, 'Cliente frecuente.');

INSERT INTO empleados (nombre, especialidad, telefono) VALUES
('Diego Torres', 'Barbero senior', '3105550101'),
('Mafe Castillo', 'Color y barba', '3105550102'),
('Nicolas Perez', 'Corte urbano', '3105550103');

INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio) VALUES
('Corte de cabello', 'Corte personalizado con lavado.', 40, 28000),
('Barba premium', 'Perfilado, vapor y aceite.', 30, 22000),
('Corte + barba', 'Servicio completo para imagen personal.', 70, 45000),
('Color basico', 'Aplicacion de color de una tonalidad.', 90, 70000);

INSERT INTO citas (id_cliente, id_empleado, id_servicio, fecha, hora, estado, observaciones, creada_por) VALUES
(1, 1, 3, CURDATE(), '10:00:00', 'confirmada', 'Cliente llega 10 minutos antes.', 1),
(2, 2, 2, CURDATE(), '14:30:00', 'pendiente', NULL, 1),
(3, 3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', 'confirmada', NULL, 1);

INSERT INTO pagos (id_cita, metodo, monto, estado, pagado_en) VALUES
(1, 'tarjeta', 45000, 'pagado', NOW()),
(2, 'efectivo', 22000, 'pendiente', NULL),
(3, 'transferencia', 28000, 'pendiente', NULL);
