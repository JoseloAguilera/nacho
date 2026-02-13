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
                <?php if (can_insert('roles')): ?>
                <a href="<?= base_url('roles/create') ?>" class="btn btn-primary">
                    ➕ Nuevo Rol
                </a>
                <?php endif; ?>
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
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($roles)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No hay roles registrados
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><strong><?= esc($role['name']) ?></strong></td>
                                            <td><?= esc($role['description']) ?></td>
                                            <td>
                                                <?php if ($role['is_system'] == 1): ?>
                                                    <span class="badge badge-primary">Sistema</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Personalizado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (can_update('roles')): ?>
                                                <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-secondary">
                                                    ✏️ Editar
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if (can_delete('roles') && $role['is_system'] == 0): ?>
                                                <a href="<?= base_url('roles/delete/' . $role['id']) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('¿Está seguro de eliminar este rol?')">
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
