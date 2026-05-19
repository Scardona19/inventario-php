-- ============================================================
--  Sistema de Gestión de Inventarios
--  Base de Datos: inventario_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS inventario_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_db;

-- Tabla 1: categorias
CREATE TABLE IF NOT EXISTS categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla 2: proveedores
CREATE TABLE IF NOT EXISTS proveedores (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    contacto    VARCHAR(100),
    telefono    VARCHAR(20),
    email       VARCHAR(100),
    direccion   TEXT,
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla 3: productos
CREATE TABLE IF NOT EXISTS productos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(150) NOT NULL,
    descripcion    TEXT,
    precio         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock          INT NOT NULL DEFAULT 0,
    stock_minimo   INT NOT NULL DEFAULT 5,
    categoria_id   INT NOT NULL,
    proveedor_id   INT NOT NULL,
    creado_en      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla 4: movimientos (historial de entradas/salidas)
CREATE TABLE IF NOT EXISTS movimientos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    producto_id  INT NOT NULL,
    tipo         ENUM('entrada','salida') NOT NULL,
    cantidad     INT NOT NULL,
    observacion  VARCHAR(255),
    fecha        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  Datos de ejemplo
-- ============================================================

INSERT INTO categorias (nombre, descripcion) VALUES
('Electrónica',   'Dispositivos y componentes electrónicos'),
('Papelería',     'Artículos de oficina y escritura'),
('Herramientas',  'Herramientas manuales y eléctricas'),
('Limpieza',      'Productos de limpieza e higiene');

INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES
('TechSupply S.A.',  'Carlos Méndez',   '555-1001', 'carlos@techsupply.com',  'Av. Industrial 123'),
('OficMax',          'Laura Torres',    '555-2002', 'laura@oficmax.com',       'Calle Comercio 456'),
('HerraFácil',       'Roberto Ruiz',    '555-3003', 'roberto@herrafacil.com',  'Zona Industrial 789'),
('LimpiaTodo',       'Ana González',    '555-4004', 'ana@limpiatodo.com',      'Blvd. Norte 321');

INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id, proveedor_id) VALUES
('Laptop HP 15"',         'Laptop Intel i5, 8GB RAM, 256GB SSD',   12500.00, 10, 3, 1, 1),
('Teclado Inalámbrico',   'Teclado USB/BT compatible Windows/Mac',   350.00, 25, 5, 1, 1),
('Mouse Óptico',          'Mouse USB 1200 DPI ergonómico',           150.00, 30, 5, 1, 1),
('Resma Papel A4',        '500 hojas, 75g/m²',                        85.00, 50, 10, 2, 2),
('Bolígrafos (caja x12)', 'Bolígrafos azules punta media',            45.00, 40, 10, 2, 2),
('Destornillador Set',    'Set 12 piezas Phillips y plano',          220.00, 15, 3, 3, 3),
('Cinta Métrica 5m',      'Cinta métrica acero inoxidable',           95.00,  8, 2, 3, 3),
('Desinfectante 1L',      'Desinfectante multiusos aroma cítrico',    60.00, 20, 5, 4, 4),
('Escoba con Mango',      'Escoba plástica mango aluminio',           75.00, 12, 3, 4, 4);

INSERT INTO movimientos (producto_id, tipo, cantidad, observacion) VALUES
(1, 'entrada', 10, 'Compra inicial'),
(2, 'entrada', 25, 'Compra inicial'),
(3, 'entrada', 30, 'Compra inicial'),
(1, 'salida',  2,  'Venta a cliente'),
(4, 'entrada', 50, 'Reposición de stock'),
(5, 'salida',  5,  'Uso interno');
