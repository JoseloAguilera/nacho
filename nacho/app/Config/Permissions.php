<?php

namespace Config;

/**
 * Permission Configuration
 * Define all system modules and their display names
 */
class Permissions
{
    /**
     * System modules with their display names
     */
    public const MODULES = [
        'dashboard' => 'Dashboard',
        'categories' => 'Categorías',
        'products' => 'Productos',
        'product_stock' => 'Stock de Productos',
        'inventory_adjustments' => 'Ajustes de Inventario',
        'customers' => 'Clientes',
        'suppliers' => 'Proveedores',
        'sales' => 'Ventas',
        'purchases' => 'Compras',
        'collections' => 'Cobranzas',
        'payments' => 'Pagos',
        'expenses' => 'Gastos',
        'settings' => 'Configuración',
        'roles' => 'Roles'
    ];

    /**
     * Permission actions
     */
    public const ACTIONS = [
        'view' => 'Consultar',
        'insert' => 'Insertar',
        'update' => 'Modificar',
        'delete' => 'Eliminar'
    ];

    /**
     * Get all modules
     */
    public static function getModules(): array
    {
        return self::MODULES;
    }

    /**
     * Get all actions
     */
    public static function getActions(): array
    {
        return self::ACTIONS;
    }

    /**
     * Get module display name
     */
    public static function getModuleName(string $module): string
    {
        return self::MODULES[$module] ?? $module;
    }
}
