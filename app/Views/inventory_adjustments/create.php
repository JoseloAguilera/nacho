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
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('inventory-adjustments/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="product_id" class="form-label">Producto *</label>
                            <select id="product_id" name="product_id" class="form-control" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" data-stock="<?= $product['stock'] ?>">
                                        <?= esc($product['code']) ?> - <?= esc($product['name']) ?> (Stock actual: <?= $product['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stock Actual</label>
                            <input type="text" id="current_stock" class="form-control" readonly style="background-color: #f3f4f6;">
                        </div>

                        <div class="form-group">
                            <label for="adjustment_type" class="form-label">Tipo de Ajuste *</label>
                            <select id="adjustment_type" name="adjustment_type" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="increase">➕ Incrementar Stock</option>
                                <option value="decrease">➖ Decrementar Stock</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity" class="form-label">Cantidad *</label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                class="form-control" 
                                min="1"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stock Resultante</label>
                            <input type="text" id="new_stock" class="form-control" readonly style="background-color: #f3f4f6;">
                        </div>

                        <div class="form-group">
                            <label for="reason" class="form-label">Motivo *</label>
                            <select id="reason" name="reason" class="form-control" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="Inventario físico">Inventario físico</option>
                                <option value="Producto dañado">Producto dañado</option>
                                <option value="Producto vencido">Producto vencido</option>
                                <option value="Corrección de error">Corrección de error</option>
                                <option value="Devolución">Devolución</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                class="form-control"
                                placeholder="Detalles adicionales del ajuste..."
                            ></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar Ajuste</button>
                            <a href="<?= base_url('inventory-adjustments') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const currentStockInput = document.getElementById('current_stock');
    const adjustmentTypeSelect = document.getElementById('adjustment_type');
    const quantityInput = document.getElementById('quantity');
    const newStockInput = document.getElementById('new_stock');

    productSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const stock = option.dataset.stock || 0;
        currentStockInput.value = stock;
        calculateNewStock();
    });

    adjustmentTypeSelect.addEventListener('change', calculateNewStock);
    quantityInput.addEventListener('input', calculateNewStock);

    function calculateNewStock() {
        const currentStock = parseInt(currentStockInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const type = adjustmentTypeSelect.value;

        if (!type || !quantity) {
            newStockInput.value = '';
            return;
        }

        let newStock;
        if (type === 'increase') {
            newStock = currentStock + quantity;
            newStockInput.style.color = '#10b981';
        } else {
            newStock = currentStock - quantity;
            newStockInput.style.color = newStock < 0 ? '#ef4444' : '#10b981';
        }

        newStockInput.value = newStock;
    }
});
</script>

<?php echo view('templates/footer'); ?>
