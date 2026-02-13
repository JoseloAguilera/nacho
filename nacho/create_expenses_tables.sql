-- Tabla de categorías de gastos
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- Tabla de gastos
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description VARCHAR(500) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_date (date),
    INDEX idx_category (category_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Categorías de gastos predefinidas
INSERT INTO expense_categories (name, description) VALUES
('Servicios', 'Luz, agua, internet, teléfono'),
('Alquiler', 'Alquiler de local o depósito'),
('Sueldos', 'Pago de salarios y cargas sociales'),
('Impuestos', 'Impuestos y tasas municipales'),
('Mantenimiento', 'Reparaciones y mantenimiento'),
('Transporte', 'Combustible, fletes, envíos'),
('Publicidad', 'Marketing y publicidad'),
('Insumos', 'Materiales de oficina y limpieza'),
('Otros', 'Gastos varios');
