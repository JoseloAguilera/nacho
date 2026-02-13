<?php 
$extraCSS = ['assets/css/dashboard.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]); 
helper('permission');
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
                <?php if ($sale['payment_type'] === 'credit' && $pending_balance > 0 && can_insert('collections')): ?>
                <a href="<?= base_url('collections/create/' . $sale['id']) ?>" class="btn btn-success">
                    💰 Registrar Pago
                </a>
                <?php endif; ?>
                <a href="<?= base_url('sales') ?>" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <p><strong>Número:</strong> <?= $sale['sale_number'] ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($sale['date'])) ?></p>
                            <p><strong>Cliente:</strong> <?= esc($sale['customer_name']) ?></p>
                            <p><strong>Documento:</strong> <?= esc($sale['customer_document']) ?></p>
                        </div>
                        <div>
                            <p><strong>Tipo de Pago:</strong> 
                                <span class="badge <?= $sale['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $sale['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                </span>
                            </p>
                            <p><strong>Estado:</strong> 
                                <?php
                                $badges = ['paid' => 'badge-success', 'partial' => 'badge-warning', 'pending' => 'badge-danger'];
                                $labels = ['paid' => 'Pagado', 'partial' => 'Parcial', 'pending' => 'Pendiente'];
                                ?>
                                <span class="badge <?= $badges[$sale['status']] ?>">
                                    <?= $labels[$sale['status']] ?>
                                </span>
                            </p>
                            <?php if ($sale['payment_type'] === 'credit'): ?>
                            <p><strong>Saldo Pendiente:</strong> <span class="text-danger">$<?= number_format($pending_balance, 2) ?></span></p>
                            <?php endif; ?>
                            <p><strong>Vendedor:</strong> <?= esc($sale['user_name']) ?></p>
                        </div>
                    </div>

                    <h4>Productos</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sale['details'] as $detail): ?>
                                    <tr>
                                        <td><?= esc($detail['product_code']) ?></td>
                                        <td><?= esc($detail['product_name']) ?></td>
                                        <td><?= $detail['quantity'] ?></td>
                                        <td>$<?= number_format($detail['price'], 2) ?></td>
                                        <td>$<?= number_format($detail['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                    <td><strong>$<?= number_format($sale['subtotal'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Impuestos:</strong></td>
                                    <td><strong>$<?= number_format($sale['tax'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                    <td><strong class="text-primary">$<?= number_format($sale['total'], 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
