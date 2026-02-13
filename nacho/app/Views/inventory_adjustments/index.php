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
                <a href="<?= base_url('inventory-adjustments/create') ?>" class="btn btn-primary">
                    ➕ Nuevo Ajuste
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Stock Anterior</th>
                                    <th>Stock Nuevo</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($adjustments)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            No hay ajustes de inventario registrados
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($adjustments as $adjustment): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($adjustment['created_at'])) ?></td>
                                            <td>
                                                <strong><?= esc($adjustment['product_code']) ?></strong><br>
                                                <small><?= esc($adjustment['product_name']) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($adjustment['adjustment_type'] === 'increase'): ?>
                                                    <span class="badge badge-success">➕ Incremento</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">➖ Decremento</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= $adjustment['quantity'] ?></strong></td>
                                            <td><?= $adjustment['previous_stock'] ?></td>
                                            <td><strong><?= $adjustment['new_stock'] ?></strong></td>
                                            <td><?= esc($adjustment['username']) ?></td>
                                            <td>
                                                <a href="<?= base_url('inventory-adjustments/view/' . $adjustment['id']) ?>" class="btn btn-sm btn-primary">
                                                    👁️ Ver
                                                </a>
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
