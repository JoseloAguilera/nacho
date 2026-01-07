<?php 
$extraCSS = ['assets/css/auth.css'];
echo view('templates/header', ['title' => 'Iniciar Sesión', 'extraCSS' => $extraCSS]); 
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <h1>🚀 Sistema Nacho</h1>
            <p>Gestión Comercial Inteligente</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

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

        <form action="<?= base_url('auth/login') ?>" method="POST" class="auth-form">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="username" class="form-label">Usuario o Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    placeholder="Ingrese su usuario o email"
                    value="<?= old('username') ?>"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Ingrese su contraseña"
                    required
                >
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordarme</label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                Iniciar Sesión
            </button>
        </form>

        <div class="auth-footer">
            <p class="text-muted">
                Sistema de Gestión Comercial &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
