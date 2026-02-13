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
            <div class="topbar-actions">
                <?php if (can_insert('categories')): ?>
                <a href="<?= base_url('categories/create') ?>" class="btn btn-primary">
                    ➕ Nueva Categoría
                </a>
                <?php endif; ?>
            </div>
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
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Productos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No hay categorías registradas
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><strong><?= esc($category['name']) ?></strong></td>
                                            <td><?= esc($category['description']) ?></td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <?= $category['product_count'] ?? 0 ?> productos
                                                </span>
                                            </td>
                                            <td>
                                            <td>
                                                <?php if (can_update('categories')): ?>
                                                <a href="<?= base_url('categories/edit/' . $category['id']) ?>" class="btn btn-sm btn-secondary">
                                                    ✏️ Editar
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if (can_delete('categories')): ?>
                                                <a href="<?= base_url('categories/delete/' . $category['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta categoría?')">
                                                    🗑️ Eliminar
                                                </a>
                                                <?php endif; ?>
                                            </td>
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
