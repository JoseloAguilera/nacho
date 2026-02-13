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

                    <form action="<?= base_url('settings/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Datos de la Empresa</h4>
                                
                                <div class="form-group">
                                    <label for="company_name" class="form-label">Nombre de la Empresa *</label>
                                    <input 
                                        type="text" 
                                        id="company_name" 
                                        name="company_name" 
                                        class="form-control" 
                                        value="<?= old('company_name', $settings['company_name'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_ruc" class="form-label">RUC / Documento *</label>
                                    <input 
                                        type="text" 
                                        id="company_ruc" 
                                        name="company_ruc" 
                                        class="form-control" 
                                        value="<?= old('company_ruc', $settings['company_ruc'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_address" class="form-label">Dirección *</label>
                                    <input 
                                        type="text" 
                                        id="company_address" 
                                        name="company_address" 
                                        class="form-control" 
                                        value="<?= old('company_address', $settings['company_address'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_email" class="form-label">Email *</label>
                                    <input 
                                        type="email" 
                                        id="company_email" 
                                        name="company_email" 
                                        class="form-control" 
                                        value="<?= old('company_email', $settings['company_email'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_phone" class="form-label">Teléfono</label>
                                    <input 
                                        type="text" 
                                        id="company_phone" 
                                        name="company_phone" 
                                        class="form-control" 
                                        value="<?= old('company_phone', $settings['company_phone'] ?? '') ?>"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="mb-3">Seguridad y Validaciones</h4>
                                
                                <div class="alert alert-warning">
                                    <strong>Importante:</strong> Esta contraseña se solicitará cuando un vendedor intente vender un producto por debajo del precio mínimo establecido.
                                </div>

                                <div class="form-group">
                                    <label for="min_price_password" class="form-label">Contraseña de Autorización (Precio Mínimo) *</label>
                                    <input 
                                        type="text" 
                                        id="min_price_password" 
                                        name="min_price_password" 
                                        class="form-control" 
                                        value="<?= old('min_price_password', $settings['min_price_password'] ?? '0000') ?>"
                                        required
                                        minlength="4"
                                    >
                                    <small class="text-muted">Por defecto: 0000</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar Configuración</button>
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
