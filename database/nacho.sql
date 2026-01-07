-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.31 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para nacho
CREATE DATABASE IF NOT EXISTS `nacho` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `nacho`;

-- Volcando estructura para tabla nacho.categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.categories: ~4 rows (aproximadamente)
INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
	(1, 'Electrónica', 'Productos electrónicos y tecnología', '2025-12-28 20:21:42'),
	(2, 'Alimentos', 'Productos alimenticios', '2025-12-28 20:21:42'),
	(3, 'Bebidas', 'Bebidas en general', '2025-12-28 20:21:42'),
	(4, 'Limpieza', 'Productos de limpieza', '2025-12-28 20:21:42');

-- Volcando estructura para tabla nacho.customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_document` (`document`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.customers: ~3 rows (aproximadamente)
INSERT INTO `customers` (`id`, `name`, `document`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
	(1, 'Juan Pérez', '12345678', '555-0001', 'juan@email.com', 'Calle Principal 123', '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(2, 'María García', '87654321', '555-0002', 'maria@email.com', 'Avenida Central 456', '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(3, 'Carlos López', '11223344', '555-0003', 'carlos@email.com', 'Plaza Mayor 789', '2025-12-28 20:21:42', '2025-12-28 20:21:42');

-- Volcando estructura para tabla nacho.customer_payments
DROP TABLE IF EXISTS `customer_payments`;
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_date` (`payment_date`),
  CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.customer_payments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.expenses
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_category` (`category_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.expenses: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.expense_categories
DROP TABLE IF EXISTS `expense_categories`;
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.expense_categories: ~9 rows (aproximadamente)
INSERT INTO `expense_categories` (`id`, `name`, `description`, `created_at`) VALUES
	(1, 'Servicios', 'Luz, agua, internet, teléfono', '2025-12-28 23:31:48'),
	(2, 'Alquiler', 'Alquiler de local o depósito', '2025-12-28 23:31:48'),
	(3, 'Sueldos', 'Pago de salarios y cargas sociales', '2025-12-28 23:31:48'),
	(4, 'Impuestos', 'Impuestos y tasas municipales', '2025-12-28 23:31:48'),
	(5, 'Mantenimiento', 'Reparaciones y mantenimiento', '2025-12-28 23:31:48'),
	(6, 'Transporte', 'Combustible, fletes, envíos', '2025-12-28 23:31:48'),
	(7, 'Publicidad', 'Marketing y publicidad', '2025-12-28 23:31:48'),
	(8, 'Insumos', 'Materiales de oficina y limpieza', '2025-12-28 23:31:48'),
	(9, 'Otros', 'Gastos varios', '2025-12-28 23:31:48');

-- Volcando estructura para tabla nacho.inventory_adjustments
DROP TABLE IF EXISTS `inventory_adjustments`;
CREATE TABLE IF NOT EXISTS `inventory_adjustments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `adjustment_type` enum('increase','decrease') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `previous_stock` int NOT NULL,
  `new_stock` int NOT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `inventory_adjustments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_adjustments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.inventory_adjustments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.products
DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price` decimal(10,2) NOT NULL,
  `min_sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_name` (`name`),
  KEY `idx_category` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.products: ~5 rows (aproximadamente)
INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `cost_price`, `price`, `min_sale_price`, `stock`, `created_at`, `updated_at`) VALUES
	(1, 1, 'PROD001', 'Mouse Inalámbrico', 'Mouse inalámbrico USB', 0.00, 15.99, 0.00, 50, '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(2, 1, 'PROD002', 'Teclado USB', 'Teclado estándar USB', 0.00, 25.50, 0.00, 30, '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(3, 2, 'PROD003', 'Arroz 1kg', 'Arroz blanco premium', 1.00, 50.00, 25.00, 100, '2025-12-28 20:21:42', '2025-12-29 03:54:37'),
	(4, 3, 'PROD004', 'Coca Cola 2L', 'Bebida gaseosa', 0.00, 3.00, 0.00, 80, '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(5, 4, 'PROD005', 'Detergente 500ml', 'Detergente líquido', 0.00, 4.50, 0.00, 60, '2025-12-28 20:21:42', '2025-12-28 20:21:42');

-- Volcando estructura para tabla nacho.purchases
DROP TABLE IF EXISTS `purchases`;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `user_id` int NOT NULL,
  `purchase_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `payment_type` enum('cash','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','partial','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_number` (`purchase_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_purchase_number` (`purchase_number`),
  KEY `idx_date` (`date`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.purchases: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.purchase_details
DROP TABLE IF EXISTS `purchase_details`;
CREATE TABLE IF NOT EXISTS `purchase_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `purchase_details_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.purchase_details: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.sales
DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `sale_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `payment_type` enum('cash','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','partial','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sale_number` (`sale_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_sale_number` (`sale_number`),
  KEY `idx_date` (`date`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.sales: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.sale_details
DROP TABLE IF EXISTS `sale_details`;
CREATE TABLE IF NOT EXISTS `sale_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.sale_details: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.settings: ~6 rows (aproximadamente)
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
	(1, 'company_name', 'Mi Empresa S.A.', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
	(2, 'company_ruc', '12345678-9', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
	(3, 'company_address', 'Av. Principal 123', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
	(4, 'company_email', 'contacto@miempresa.com', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
	(5, 'company_phone', '+595 981 123 456', '2025-12-29 00:33:14', '2025-12-29 00:33:14'),
	(6, 'min_price_password', '0000', '2025-12-29 00:33:14', '2025-12-29 00:33:14');

-- Volcando estructura para tabla nacho.suppliers
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_document` (`document`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.suppliers: ~2 rows (aproximadamente)
INSERT INTO `suppliers` (`id`, `name`, `document`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
	(1, 'Distribuidora Tech S.A.', '20123456789', '555-1001', 'ventas@tech.com', 'Zona Industrial 100', '2025-12-28 20:21:42', '2025-12-28 20:21:42'),
	(2, 'Alimentos del Sur', '20987654321', '555-1002', 'info@alimentos.com', 'Mercado Central 200', '2025-12-28 20:21:42', '2025-12-28 20:21:42');

-- Volcando estructura para tabla nacho.supplier_payments
DROP TABLE IF EXISTS `supplier_payments`;
CREATE TABLE IF NOT EXISTS `supplier_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_date` (`payment_date`),
  CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.supplier_payments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla nacho.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','ventas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ventas',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla nacho.users: ~2 rows (aproximadamente)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
	(5, 'admin', 'admin@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 'admin', '2025-12-28 21:08:31', '2025-12-28 21:08:31'),
	(6, 'vendedor1', 'ventas@nacho.com', '$2y$10$53C/gxqHf10baGlSnSSIg.Ngab5rIVWdo6.T8kXjcwyjD4MbsQhaC', 'ventas', '2025-12-28 21:08:31', '2025-12-28 21:08:31');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
