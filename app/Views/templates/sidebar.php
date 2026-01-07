<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">🚀 Nacho</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Principal</div>
            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Inventario</div>
            <?php if (session()->get('role') === 'admin'): ?>
            <a href="<?= base_url('categories') ?>" class="nav-link <?= strpos(uri_string(), 'categories') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📁</span>
                <span>Categorías</span>
            </a>
            <?php endif; ?>
            <a href="<?= base_url('products') ?>" class="nav-link <?= strpos(uri_string(), 'products') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📦</span>
                <span>Productos</span>
            </a>
            <a href="<?= base_url('inventory-adjustments') ?>" class="nav-link <?= strpos(uri_string(), 'inventory-adjustments') !== false ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Ajustes de Inventario</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Contactos</div>
            <a href="<?= base_url('customers') ?>" class="nav-link <?= strpos(uri_string(), 'customers') !== false ? 'active' : '' ?>">
                <span class="nav-icon">👥</span>
                <span>Clientes</span>
            </a>
            <a href="<?= base_url('suppliers') ?>" class="nav-link <?= strpos(uri_string(), 'suppliers') !== false ? 'active' : '' ?>">
                <span class="nav-icon">🏢</span>
                <span>Proveedores</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Operaciones</div>
            <a href="<?= base_url('sales') ?>" class="nav-link <?= strpos(uri_string(), 'sales') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💰</span>
                <span>Ventas</span>
            </a>
            <a href="<?= base_url('purchases') ?>" class="nav-link <?= strpos(uri_string(), 'purchases') !== false ? 'active' : '' ?>">
                <span class="nav-icon">🛒</span>
                <span>Compras</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Finanzas</div>
            <a href="<?= base_url('collections') ?>" class="nav-link <?= strpos(uri_string(), 'collections') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💰</span>
                <span>Cobranzas</span>
            </a>
            <a href="<?= base_url('payments') ?>" class="nav-link <?= strpos(uri_string(), 'payments') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💳</span>
                <span>Pagos</span>
            </a>
            <a href="<?= base_url('expenses') ?>" class="nav-link <?= strpos(uri_string(), 'expenses') !== false ? 'active' : '' ?>">
                <span class="nav-icon">💸</span>
                <span>Gastos</span>
            </a>
        </div>

        <?php if (session()->get('role') === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Sistema</div>
            <a href="<?= base_url('settings') ?>" class="nav-link <?= strpos(uri_string(), 'settings') !== false ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span>
                <span>Configuración</span>
            </a>
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
                <p class="user-role"><?= ucfirst(session()->get('role')) ?></p>
            </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width: 100%;">
            Cerrar Sesión
        </a>
    </div>
</aside>
