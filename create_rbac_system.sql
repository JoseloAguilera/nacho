-- ============================================
-- RBAC System Migration Script
-- Sistema de Control de Accesos Basado en Roles
-- ============================================

USE nacho;

-- ============================================
-- Tabla: roles
-- Almacena los roles del sistema
-- ============================================
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_system TINYINT(1) DEFAULT 0 COMMENT 'Roles del sistema no se pueden eliminar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: role_permissions
-- Almacena los permisos de cada rol por módulo
-- ============================================
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    module VARCHAR(50) NOT NULL COMMENT 'Nombre del módulo del sistema',
    can_view CHAR(1) DEFAULT 'N' COMMENT 'Permiso para consultar (S/N)',
    can_insert CHAR(1) DEFAULT 'N' COMMENT 'Permiso para insertar (S/N)',
    can_update CHAR(1) DEFAULT 'N' COMMENT 'Permiso para modificar (S/N)',
    can_delete CHAR(1) DEFAULT 'N' COMMENT 'Permiso para eliminar (S/N)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_module (role_id, module),
    INDEX idx_role (role_id),
    INDEX idx_module (module)
) ENGINE=InnoDB;

-- ============================================
-- Migración de la tabla users
-- ============================================

-- Agregar columna role_id
ALTER TABLE users 
    ADD COLUMN role_id INT NULL AFTER password;

-- Crear roles del sistema
INSERT INTO roles (name, description, is_system) VALUES 
    ('Administrador', 'Acceso total al sistema con todos los permisos', 1),
    ('Ventas', 'Acceso a módulos de ventas y operaciones básicas', 1);

-- Configurar permisos completos para Administrador (role_id = 1)
INSERT INTO role_permissions (role_id, module, can_view, can_insert, can_update, can_delete) VALUES
    (1, 'dashboard', 'S', 'S', 'S', 'S'),
    (1, 'categories', 'S', 'S', 'S', 'S'),
    (1, 'products', 'S', 'S', 'S', 'S'),
    (1, 'product_stock', 'S', 'S', 'S', 'S'),
    (1, 'inventory_adjustments', 'S', 'S', 'S', 'S'),
    (1, 'customers', 'S', 'S', 'S', 'S'),
    (1, 'suppliers', 'S', 'S', 'S', 'S'),
    (1, 'sales', 'S', 'S', 'S', 'S'),
    (1, 'purchases', 'S', 'S', 'S', 'S'),
    (1, 'collections', 'S', 'S', 'S', 'S'),
    (1, 'payments', 'S', 'S', 'S', 'S'),
    (1, 'expenses', 'S', 'S', 'S', 'S'),
    (1, 'settings', 'S', 'S', 'S', 'S'),
    (1, 'roles', 'S', 'S', 'S', 'S');

-- Configurar permisos para Ventas (role_id = 2)
INSERT INTO role_permissions (role_id, module, can_view, can_insert, can_update, can_delete) VALUES
    (2, 'dashboard', 'S', 'N', 'N', 'N'),
    (2, 'categories', 'S', 'N', 'N', 'N'),
    (2, 'products', 'S', 'N', 'N', 'N'),
    (2, 'product_stock', 'S', 'N', 'N', 'N'),
    (2, 'inventory_adjustments', 'S', 'S', 'N', 'N'),
    (2, 'customers', 'S', 'S', 'S', 'N'),
    (2, 'suppliers', 'S', 'N', 'N', 'N'),
    (2, 'sales', 'S', 'S', 'N', 'N'),
    (2, 'purchases', 'S', 'N', 'N', 'N'),
    (2, 'collections', 'S', 'S', 'N', 'N'),
    (2, 'payments', 'S', 'N', 'N', 'N'),
    (2, 'expenses', 'N', 'N', 'N', 'N'),
    (2, 'settings', 'N', 'N', 'N', 'N'),
    (2, 'roles', 'N', 'N', 'N', 'N');

-- Migrar usuarios existentes al nuevo sistema
UPDATE users SET role_id = 1 WHERE role = 'admin';
UPDATE users SET role_id = 2 WHERE role = 'ventas';

-- Agregar foreign key constraint
ALTER TABLE users 
    ADD CONSTRAINT fk_users_role 
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT;

-- Eliminar la columna role antigua (comentado por seguridad, descomentar después de verificar)
-- ALTER TABLE users DROP COLUMN role;

-- ============================================
-- Verificación
-- ============================================
-- SELECT * FROM roles;
-- SELECT * FROM role_permissions ORDER BY role_id, module;
-- SELECT id, username, email, role_id FROM users;
