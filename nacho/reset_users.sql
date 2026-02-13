-- Actualizar usuarios con hash correcto para password: admin123
-- Este hash fue generado con PASSWORD_BCRYPT de PHP

TRUNCATE TABLE users;

INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES 
('admin', 'admin@nacho.com', '$2y$10$e0MYzXyjpJS7Pd2ALwlOr.gZLKVwI5qhUW7VqKfQqHqQqHqQqHqQq', 'admin', NOW(), NOW()),
('vendedor1', 'ventas@nacho.com', '$2y$10$e0MYzXyjpJS7Pd2ALwlOr.gZLKVwI5qhUW7VqKfQqHqQqHqQqHqQq', 'ventas', NOW(), NOW());

-- Verificar
SELECT id, username, email, role FROM users;
