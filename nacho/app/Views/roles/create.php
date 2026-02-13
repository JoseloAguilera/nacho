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
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('roles/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label for="name">Nombre del Rol *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name') ?>" 
                                   required 
                                   maxlength="100">
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      maxlength="500"><?= old('description') ?></textarea>
                        </div>

                        <h4 style="margin-top: 2rem; margin-bottom: 1rem;">Permisos por Módulo</h4>
                        <p class="text-muted" style="margin-bottom: 1.5rem;">
                            Seleccione los permisos que tendrá este rol en cada módulo del sistema.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Módulo</th>
                                        <th style="width: 17.5%; text-align: center;">
                                            Consultar
                                            <br><small><a href="#" onclick="toggleColumn('view'); return false;">Todos</a></small>
                                        </th>
                                        <th style="width: 17.5%; text-align: center;">
                                            Insertar
                                            <br><small><a href="#" onclick="toggleColumn('insert'); return false;">Todos</a></small>
                                        </th>
                                        <th style="width: 17.5%; text-align: center;">
                                            Modificar
                                            <br><small><a href="#" onclick="toggleColumn('update'); return false;">Todos</a></small>
                                        </th>
                                        <th style="width: 17.5%; text-align: center;">
                                            Eliminar
                                            <br><small><a href="#" onclick="toggleColumn('delete'); return false;">Todos</a></small>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $moduleKey => $moduleName): ?>
                                        <tr>
                                            <td><strong><?= esc($moduleName) ?></strong></td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" 
                                                       name="perm_<?= $moduleKey ?>_view" 
                                                       value="S" 
                                                       class="perm-view">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" 
                                                       name="perm_<?= $moduleKey ?>_insert" 
                                                       value="S" 
                                                       class="perm-insert">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" 
                                                       name="perm_<?= $moduleKey ?>_update" 
                                                       value="S" 
                                                       class="perm-update">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" 
                                                       name="perm_<?= $moduleKey ?>_delete" 
                                                       value="S" 
                                                       class="perm-delete">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary">Guardar Rol</button>
                            <a href="<?= base_url('roles') ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleColumn(action) {
    const checkboxes = document.querySelectorAll('.perm-' + action);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}
</script>

<?php echo view('templates/footer'); ?>
