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

                    <form action="<?= base_url('suppliers/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="document" class="form-label">Documento</label>
                            <input type="text" id="document" name="document" class="form-control" value="<?= old('document') ?>">
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone') ?>">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>">
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Dirección</label>
                            <textarea id="address" name="address" class="form-control"><?= old('address') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar</button>
                            <a href="<?= base_url('suppliers') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
