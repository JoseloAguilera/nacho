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
                <?php if ($purchase['payment_type'] === 'credit' && $pending_balance > 0): ?>
                <a href="<?= base_url('payments/create/' . $purchase['id']) ?>" class="btn btn-success">
                    💳 Registrar Pago
                </a>
                <?php endif; ?>
                <a href="<?= base_url('purchases') ?>" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <p><strong>Número:</strong> <?= $purchase['purchase_number'] ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($purchase['date'])) ?></p>
                            <p><strong>Proveedor:</strong> <?= esc($purchase['supplier_name']) ?></p>
                            <p><strong>Documento:</strong> <?= esc($purchase['supplier_document']) ?></p>
                        </div>
                        <div>
                            <p><strong>Tipo de Pago:</strong> 
                                <span class="badge <?= $purchase['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $purchase['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                </span>
                            </p>
                            <p><strong>Estado:</strong> 
                                <?php
                                $badges = ['paid' => 'badge-success', 'partial' => 'badge-warning', 'pending' => 'badge-danger'];
                                $labels = ['paid' => 'Pagado', 'partial' => 'Parcial', 'pending' => 'Pendiente'];
                                ?>
                                <span class="badge <?= $badges[$purchase['status']] ?>">
                                    <?= $labels[$purchase['status']] ?>
                                </span>
                            </p>
                            <?php if ($purchase['payment_type'] === 'credit'): ?>
                            <p><strong>Saldo Pendiente:</strong> <span class="text-danger">$<?= number_format($pending_balance, 2) ?></span></p>
                            <?php endif; ?>
                            <p><strong>Usuario:</strong> <?= esc($purchase['user_name']) ?></p>
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
                                <?php foreach ($purchase['details'] as $detail): ?>
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
                                    <td><strong>$<?= number_format($purchase['subtotal'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Impuestos:</strong></td>
                                    <td><strong>$<?= number_format($purchase['tax'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                    <td><strong class="text-primary">$<?= number_format($purchase['total'], 2) ?></strong></td>
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
