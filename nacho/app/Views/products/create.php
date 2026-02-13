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

                    <form action="<?= base_url('products/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label">Categoría *</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="code" class="form-label">Código *</label>
                            <input 
                                type="text" 
                                id="code" 
                                name="code" 
                                class="form-control" 
                                value="<?= old('code') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Nombre *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="form-control" 
                                value="<?= old('name') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control"
                            ><?= old('description') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost_price" class="form-label">Precio Costo *</label>
                                    <input 
                                        type="number" 
                                        id="cost_price" 
                                        name="cost_price" 
                                        class="form-control" 
                                        step="0.01"
                                        min="0"
                                        value="<?= old('cost_price') ?>"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price" class="form-label">Precio Venta *</label>
                                    <input 
                                        type="number" 
                                        id="price" 
                                        name="price" 
                                        class="form-control" 
                                        step="0.01"
                                        min="0"
                                        value="<?= old('price') ?>"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_sale_price" class="form-label">Precio Mínimo *</label>
                                    <input 
                                        type="number" 
                                        id="min_sale_price" 
                                        name="min_sale_price" 
                                        class="form-control" 
                                        step="0.01"
                                        min="0"
                                        value="<?= old('min_sale_price') ?>"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stock" class="form-label">Stock Inicial *</label>
                            <input 
                                type="number" 
                                id="stock" 
                                name="stock" 
                                class="form-control" 
                                min="0"
                                value="<?= old('stock', 0) ?>"
                                required
                            >
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                💾 Guardar
                            </button>
                            <a href="<?= base_url('products') ?>" class="btn btn-secondary">
                                ❌ Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
