-- Tabla de configuraciones
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertar configuraciones por defecto
INSERT INTO settings (setting_key, setting_value) VALUES
('company_name', 'Mi Empresa S.A.'),
('company_ruc', '12345678-9'),
('company_address', 'Av. Principal 123'),
('company_email', 'contacto@miempresa.com'),
('company_phone', '+595 981 123 456'),
('min_price_password', '0000')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
