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
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-header">
                    <h4>Compra: <?= $purchase['purchase_number'] ?> - Proveedor: <?= esc($purchase['supplier_name']) ?></h4>
                    <p class="text-muted">Total: $<?= number_format($purchase['total'], 2) ?> | Saldo Pendiente: <span class="text-danger">$<?= number_format($pending_balance, 2) ?></span></p>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 1.25rem;">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('payments/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="purchase_id" value="<?= $purchase['id'] ?>">
                        
                        <div class="form-group">
                            <label for="amount" class="form-label">Monto a Pagar *</label>
                            <input 
                                type="number" 
                                id="amount" 
                                name="amount" 
                                class="form-control" 
                                step="0.01"
                                max="<?= $pending_balance ?>"
                                value="<?= $pending_balance ?>"
                                required
                            >
                            <small class="text-muted">Máximo: $<?= number_format($pending_balance, 2) ?></small>
                        </div>

                        <div class="form-group">
                            <label for="payment_date" class="form-label">Fecha de Pago *</label>
                            <input 
                                type="date" 
                                id="payment_date" 
                                name="payment_date" 
                                class="form-control" 
                                value="<?= date('Y-m-d') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="payment_method" class="form-label">Método de Pago *</label>
                            <select id="payment_method" name="payment_method" class="form-control" required>
                                <option value="cash">Efectivo</option>
                                <option value="transfer">Transferencia</option>
                                <option value="check">Cheque</option>
                                <option value="card">Tarjeta</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea id="notes" name="notes" class="form-control"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">💳 Registrar Pago</button>
                            <a href="<?= base_url('payments') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>

                    <?php if (!empty($payments)): ?>
                        <hr>
                        <h4>Historial de Pagos</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                                            <td>$<?= number_format($payment['amount'], 2) ?></td>
                                            <td><?= ucfirst($payment['payment_method']) ?></td>
                                            <td><?= esc($payment['notes']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
