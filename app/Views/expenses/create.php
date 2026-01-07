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

                    <form action="<?= base_url('expenses/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="date" class="form-label">Fecha *</label>
                            <input 
                                type="date" 
                                id="date" 
                                name="date" 
                                class="form-control" 
                                value="<?= old('date', date('Y-m-d')) ?>"
                                required
                            >
                        </div>

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
                            <label for="amount" class="form-label">Monto *</label>
                            <input 
                                type="number" 
                                id="amount" 
                                name="amount" 
                                class="form-control" 
                                step="0.01"
                                min="0.01"
                                value="<?= old('amount') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Descripción *</label>
                            <input 
                                type="text" 
                                id="description" 
                                name="description" 
                                class="form-control" 
                                maxlength="500"
                                value="<?= old('description') ?>"
                                placeholder="Ej: Pago de luz del mes de diciembre"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                class="form-control"
                                placeholder="Información adicional (opcional)"
                            ><?= old('notes') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar Gasto</button>
                            <a href="<?= base_url('expenses') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
