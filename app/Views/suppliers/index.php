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
                <a href="<?= base_url('suppliers/create') ?>" class="btn btn-primary">
                    ➕ Nuevo Proveedor
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
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($suppliers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No hay proveedores registrados
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <tr>
                                            <td><strong><?= esc($supplier['name']) ?></strong></td>
                                            <td><?= esc($supplier['document']) ?></td>
                                            <td><?= esc($supplier['phone']) ?></td>
                                            <td><?= esc($supplier['email']) ?></td>
                                            <td>
                                                <a href="<?= base_url('suppliers/account/' . $supplier['id']) ?>" class="btn btn-sm btn-primary">
                                                    💰 Cuenta
                                                </a>
                                                <a href="<?= base_url('suppliers/edit/' . $supplier['id']) ?>" class="btn btn-sm btn-secondary">
                                                    ✏️ Editar
                                                </a>
                                                <a href="<?= base_url('suppliers/delete/' . $supplier['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este proveedor?')">
                                                    🗑️ Eliminar
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
