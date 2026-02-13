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
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 1.25rem;">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('profile/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Información Personal</h4>
                                
                                <div class="form-group">
                                    <label for="username" class="form-label">Usuario *</label>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username" 
                                        class="form-control" 
                                        value="<?= old('username', $user['username']) ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">Email *</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        class="form-control" 
                                        value="<?= old('email', $user['email']) ?>"
                                        required
                                    >
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Rol</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        value="<?= ucfirst($user['role']) ?>"
                                        readonly
                                        style="background-color: #f3f4f6;"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="mb-3">Cambiar Contraseña</h4>
                                <p class="text-muted small mb-3">Dejar en blanco si no desea cambiar la contraseña</p>

                                <div class="form-group">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        class="form-control" 
                                        minlength="6"
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                                    <input 
                                        type="password" 
                                        id="password_confirm" 
                                        name="password_confirm" 
                                        class="form-control" 
                                    >
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .col-md-6 {
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }
    @media (min-width: 768px) {
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    .mb-3 { margin-bottom: 1rem; }
    .my-4 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
    .text-muted { color: #6c757d; }
    .small { font-size: 0.875em; }
</style>

<?php echo view('templates/footer'); ?>
