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
                <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary">
                    ➕ Nueva Compra
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
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchases)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No hay compras registradas
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchases as $purchase): ?>
                                        <tr>
                                            <td><strong><?= esc($purchase['purchase_number']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($purchase['date'])) ?></td>
                                            <td><?= esc($purchase['supplier_name']) ?></td>
                                            <td>
                                                <span class="badge <?= $purchase['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= $purchase['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                                </span>
                                            </td>
                                            <td>$<?= number_format($purchase['total'], 2) ?></td>
                                            <td>
                                                <?php
                                                $badges = ['paid' => 'badge-success', 'partial' => 'badge-warning', 'pending' => 'badge-danger'];
                                                $labels = ['paid' => 'Pagado', 'partial' => 'Parcial', 'pending' => 'Pendiente'];
                                                ?>
                                                <span class="badge <?= $badges[$purchase['status']] ?>">
                                                    <?= $labels[$purchase['status']] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('purchases/view/' . $purchase['id']) ?>" class="btn btn-sm btn-primary">
                                                    👁️ Ver
                                                </a>
                                                <?php if (session()->get('role') === 'admin'): ?>
                                                <a href="<?= base_url('purchases/delete/' . $purchase['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta compra?')">
                                                    🗑️
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
