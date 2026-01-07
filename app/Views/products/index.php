<?php 
$extraCSS = ['assets/css/dashboard.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]); 
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>
    
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2><?= $title ?></h2>
            </div>
            <div class="topbar-actions">
                <a href="<?= base_url('products/create') ?>" class="btn btn-primary">
                    ➕ Nuevo Producto
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Costo</th>
                                    <th>Venta</th>
                                    <th>Mínimo</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No hay productos registrados
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><code><?= esc($product['code']) ?></code></td>
                                            <td><strong><?= esc($product['name']) ?></strong></td>
                                            <td><?= esc($product['category_name']) ?></td>
                                            <td class="text-muted">$<?= number_format($product['cost_price'] ?? 0, 2) ?></td>
                                            <td class="text-primary font-weight-bold">$<?= number_format($product['price'], 2) ?></td>
                                            <td class="text-muted small">$<?= number_format($product['min_sale_price'] ?? 0, 2) ?></td>
                                            <td>
                                                <span class="badge <?= $product['stock'] <= 10 ? 'badge-danger' : 'badge-success' ?>">
                                                    <?= $product['stock'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn btn-sm btn-secondary">
                                                    ✏️ Editar
                                                </a>
                                                <?php if (session()->get('role') === 'admin'): ?>
                                                <a href="<?= base_url('products/delete/' . $product['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">
                                                    🗑️ Eliminar
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
