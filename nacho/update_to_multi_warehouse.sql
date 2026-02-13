-- 1. Crear tabla de depósitos (Warehouses)
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci,
  `is_active` boolean NOT NULL DEFAULT true,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insertar depósito por defecto (Depósito Central)
INSERT INTO `warehouses` (`name`, `description`, `address`, `is_active`) 
SELECT 'Depósito Central', 'Depósito principal', 'Dirección Principal', 1 
WHERE NOT EXISTS (SELECT 1 FROM `warehouses` WHERE `name` = 'Depósito Central');

-- 3. Crear tabla de stock por producto y depósito
CREATE TABLE IF NOT EXISTS `product_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_warehouse` (`warehouse_id`),
  CONSTRAINT `product_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Migrar stock existente al Depósito Central (ID 1 asumiendo que es el primero creado)
INSERT INTO `product_stock` (`product_id`, `warehouse_id`, `quantity`)
SELECT `id`, (SELECT `id` FROM `warehouses` WHERE `name` = 'Depósito Central' LIMIT 1), `stock`
FROM `products`;

-- 5. Agregar warehouse_id a las tablas de transacciones
-- Sales
ALTER TABLE `sales` ADD COLUMN `warehouse_id` int DEFAULT NULL AFTER `user_id`;
-- Asignar ventas anteriores al depósito central
UPDATE `sales` SET `warehouse_id` = (SELECT `id` FROM `warehouses` WHERE `name` = 'Depósito Central' LIMIT 1) WHERE `warehouse_id` IS NULL;
ALTER TABLE `sales` MODIFY COLUMN `warehouse_id` int NOT NULL;
ALTER TABLE `sales` ADD CONSTRAINT `sales_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT;

-- Purchases
ALTER TABLE `purchases` ADD COLUMN `warehouse_id` int DEFAULT NULL AFTER `user_id`;
-- Asignar compras anteriores al depósito central
UPDATE `purchases` SET `warehouse_id` = (SELECT `id` FROM `warehouses` WHERE `name` = 'Depósito Central' LIMIT 1) WHERE `warehouse_id` IS NULL;
ALTER TABLE `purchases` MODIFY COLUMN `warehouse_id` int NOT NULL;
ALTER TABLE `purchases` ADD CONSTRAINT `purchases_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT;

-- Inventory Adjustments
ALTER TABLE `inventory_adjustments` ADD COLUMN `warehouse_id` int DEFAULT NULL AFTER `product_id`;
-- Asignar ajustes anteriores al depósito central
UPDATE `inventory_adjustments` SET `warehouse_id` = (SELECT `id` FROM `warehouses` WHERE `name` = 'Depósito Central' LIMIT 1) WHERE `warehouse_id` IS NULL;
ALTER TABLE `inventory_adjustments` MODIFY COLUMN `warehouse_id` int NOT NULL;
ALTER TABLE `inventory_adjustments` ADD CONSTRAINT `inventory_adjustments_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT;
