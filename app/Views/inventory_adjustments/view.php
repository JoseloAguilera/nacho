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
                <a href="<?= base_url('inventory-adjustments') ?>" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($adjustment['created_at'])) ?></p>
                            <p><strong>Producto:</strong> <?= esc($adjustment['product_code']) ?> - <?= esc($adjustment['product_name']) ?></p>
                            <p><strong>Stock Actual:</strong> <span class="badge badge-primary"><?= $adjustment['current_stock'] ?></span></p>
                        </div>
                        <div>
                            <p><strong>Tipo de Ajuste:</strong> 
                                <?php if ($adjustment['adjustment_type'] === 'increase'): ?>
                                    <span class="badge badge-success">➕ Incremento</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">➖ Decremento</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Cantidad:</strong> <?= $adjustment['quantity'] ?></p>
                            <p><strong>Realizado por:</strong> <?= esc($adjustment['username']) ?></p>
                        </div>
                    </div>

                    <hr>

                    <h4>Detalle del Ajuste</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Stock Anterior</th>
                                    <th>Ajuste</th>
                                    <th>Stock Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong><?= $adjustment['previous_stock'] ?></strong></td>
                                    <td>
                                        <?php if ($adjustment['adjustment_type'] === 'increase'): ?>
                                            <span style="color: #10b981;">+<?= $adjustment['quantity'] ?></span>
                                        <?php else: ?>
                                            <span style="color: #ef4444;">-<?= $adjustment['quantity'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $adjustment['new_stock'] ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($adjustment['reason']): ?>
                        <div style="margin-top: 20px;">
                            <p><strong>Motivo:</strong> <?= esc($adjustment['reason']) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($adjustment['notes']): ?>
                        <div style="margin-top: 10px;">
                            <p><strong>Notas:</strong></p>
                            <p style="background-color: #f3f4f6; padding: 10px; border-radius: 5px;">
                                <?= nl2br(esc($adjustment['notes'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
