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
                            <label for="warehouse_id" class="form-label">Depósito *</label>
                            <select id="warehouse_id" name="warehouse_id" class="form-control" required>
                                <option value="">Seleccione un depósito</option>
                                <?php if (!empty($warehouses)): ?>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
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
                            <input type="text" id="current_stock" class="form-control" readonly style="background-color: #f3f4f6;" value="-">
                            <small id="stock_info_message" class="form-text text-muted"></small>
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
    const warehouseSelect = document.getElementById('warehouse_id');
    const productSelect = document.getElementById('product_id');
    const currentStockInput = document.getElementById('current_stock');
    const stockInfoMessage = document.getElementById('stock_info_message');
    const adjustmentTypeSelect = document.getElementById('adjustment_type');
    const quantityInput = document.getElementById('quantity');
    const newStockInput = document.getElementById('new_stock');

    warehouseSelect.addEventListener('change', function() {
        if (this.value) {
            productSelect.disabled = false;
            productSelect.options[0].text = "Seleccione un producto";
            if (productSelect.value) fetchStock();
        } else {
            productSelect.disabled = true;
            productSelect.value = "";
            productSelect.options[0].text = "Seleccione un depósito primero";
            currentStockInput.value = "-";
            stockInfoMessage.textContent = "";
            newStockInput.value = "";
        }
    });

    productSelect.addEventListener('change', fetchStock);

    function fetchStock() {
        const productId = productSelect.value;
        const warehouseId = warehouseSelect.value;

        if (!productId || !warehouseId) {
            currentStockInput.value = "-";
            stockInfoMessage.textContent = "";
            calculateNewStock();
            return;
        }

        currentStockInput.value = "Cargando...";
        stockInfoMessage.textContent = "Consultando...";

        fetch(`<?= base_url('inventory-adjustments/getStock') ?>?product_id=${productId}&warehouse_id=${warehouseId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.stock !== undefined) {
                    currentStockInput.value = data.stock;
                    stockInfoMessage.textContent = `Stock en depósito: ${data.stock}`;
                    stockInfoMessage.className = "form-text text-info";
                    calculateNewStock();
                } else {
                    currentStockInput.value = "-";
                    stockInfoMessage.textContent = "Error al obtener stock";
                    stockInfoMessage.className = "form-text text-danger";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                currentStockInput.value = "Error";
                stockInfoMessage.textContent = "Error de conexión";
                stockInfoMessage.className = "form-text text-danger";
            });
    }

    adjustmentTypeSelect.addEventListener('change', calculateNewStock);
    quantityInput.addEventListener('input', calculateNewStock);

    function calculateNewStock() {
        const currentStockVal = currentStockInput.value;
        if (currentStockVal === '-' || currentStockVal === 'Cargando...' || currentStockVal === 'Error') {
            newStockInput.value = '';
            return;
        }

        const currentStock = parseInt(currentStockVal) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const type = adjustmentTypeSelect.value;

        if (!type || !quantity && quantity !== 0) {
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
