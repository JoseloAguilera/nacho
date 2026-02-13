<aside class="sidebar">
    <?php helper('permission'); ?>
    <div class="sidebar-header">
        <div class="sidebar-logo">🚀 Nacho</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Principal</div>
            <?php if (can_view('dashboard')): ?>
            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Inventario</div>
            
            <?php if (can_view('categories')): ?>
            <a href="<?= base_url('categories') ?>" class="nav-link <?= strpos(uri_string(), 'categories') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📁</span>
                <span>Categorías</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('products')): ?>
            <a href="<?= base_url('products') ?>" class="nav-link <?= strpos(uri_string(), 'products') !== false && strpos(uri_string(), 'product-stock') === false ? 'active' : '' ?>">
                <span class="nav-icon">📦</span>
                <span>Productos</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('product_stock')): ?>
            <a href="<?= base_url('product-stock') ?>" class="nav-link <?= strpos(uri_string(), 'product-stock') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Stock de Productos</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('inventory_adjustments')): ?>
            <a href="<?= base_url('inventory-adjustments') ?>" class="nav-link <?= strpos(uri_string(), 'inventory-adjustments') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Ajustes de Inventario</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Contactos</div>
            
            <?php if (can_view('customers')): ?>
            <a href="<?= base_url('customers') ?>" class="nav-link <?= strpos(uri_string(), 'customers') !== false ? 'active' : '' ?>">
                <span class="nav-icon">👥</span>
                <span>Clientes</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('suppliers')): ?>
            <a href="<?= base_url('suppliers') ?>" class="nav-link <?= strpos(uri_string(), 'suppliers') !== false ? 'active' : '' ?>">
                <span class="nav-icon">🏢</span>
                <span>Proveedores</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Operaciones</div>
            
            <?php if (can_view('sales')): ?>
            <a href="<?= base_url('sales') ?>" class="nav-link <?= strpos(uri_string(), 'sales') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💰</span>
                <span>Ventas</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('purchases')): ?>
            <a href="<?= base_url('purchases') ?>" class="nav-link <?= strpos(uri_string(), 'purchases') !== false ? 'active' : '' ?>">
                <span class="nav-icon">🛒</span>
                <span>Compras</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Finanzas</div>
            
            <?php if (can_view('collections')): ?>
            <a href="<?= base_url('collections') ?>" class="nav-link <?= strpos(uri_string(), 'collections') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💰</span>
                <span>Cobranzas</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('payments')): ?>
            <a href="<?= base_url('payments') ?>" class="nav-link <?= strpos(uri_string(), 'payments') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💳</span>
                <span>Pagos</span>
            </a>
            <?php endif; ?>

            <?php if (can_view('expenses')): ?>
            <a href="<?= base_url('expenses') ?>" class="nav-link <?= strpos(uri_string(), 'expenses') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💸</span>
                <span>Gastos</span>
            </a>
            <?php endif; ?>
        </div>

        <?php 
        // Show Sistema section if user can view settings OR roles
        if (can_view('settings') || can_view('roles')): 
        ?>
        <div class="nav-section">
            <div class="nav-section-title">Sistema</div>
            <?php if (can_view('settings')): ?>
            <a href="<?= base_url('settings') ?>" class="nav-link <?= strpos(uri_string(), 'settings') !== false ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span>
                <span>Configuración</span>
            </a>
            <?php endif; ?>
            
            <?php if (can_view('roles')): ?>
            <a href="<?= base_url('roles') ?>" class="nav-link <?= strpos(uri_string(), 'roles') !== false ? 'active' : '' ?>">
                <span class="nav-icon">🔐</span>
                <span>Roles</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
            </div>
            <div class="user-details">
                <a href="<?= base_url('profile') ?>" style="text-decoration: none; color: inherit;">
                    <p class="user-name"><?= esc(session()->get('username')) ?></p>
                </a>
                <p class="user-role"><?= ucfirst(session()->get('role_name') ?? session()->get('role') ?? '') ?></p>
            </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width: 100%;">
            Cerrar Sesión
        </a>
    </div>
</aside>
