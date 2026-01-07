<!-- Purchases create view - Similar to sales create, just change customer to supplier -->
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
                    <form action="<?= base_url('purchases/store') ?>" method="POST" id="purchaseForm">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label class="form-label">Número de Compra</label>
                            <input type="text" class="form-control" value="<?= $purchase_number ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="supplier_id" class="form-label">Proveedor *</label>
                            <select id="supplier_id" name="supplier_id" class="form-control" required>
                                <option value="">Seleccione un proveedor</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['id'] ?>"><?= esc($supplier['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_type" class="form-label">Tipo de Pago *</label>
                            <select id="payment_type" name="payment_type" class="form-control" required>
                                <option value="cash">Contado</option>
                                <option value="credit">Crédito</option>
                            </select>
                        </div>

                        <hr>
                        <h4>Productos</h4>
                        
                        <div id="products-container">
                            <div class="product-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 10px; align-items: end;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Producto</label>
                                    <select class="form-control product-select" required>
                                        <option value="">Seleccione...</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                                <?= esc($product['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" class="form-control quantity-input" min="1" value="1" required>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control price-input" step="0.01" required>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal-display" readonly>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-product" style="margin-top: 24px;">🗑️</button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary" id="addProduct">➕ Agregar Producto</button>

                        <hr>
                        <div style="text-align: right;">
                            <h3>Total: $<span id="totalAmount">0.00</span></h3>
                        </div>

                        <div class="d-flex gap-2" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">💾 Guardar Compra</button>
                            <a href="<?= base_url('purchases') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Same JavaScript as sales/create.php but for purchases
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('products-container');
    const addBtn = document.getElementById('addProduct');
    const form = document.getElementById('purchaseForm');

    addBtn.addEventListener('click', function() {
        const firstRow = container.querySelector('.product-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(input => {
            if (!input.classList.contains('product-select')) {
                input.value = input.type === 'number' ? '1' : '';
            }
        });
        container.appendChild(newRow);
        attachRowEvents(newRow);
    });

    function attachRowEvents(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const subtotalDisplay = row.querySelector('.subtotal-display');
        const removeBtn = row.querySelector('.remove-product');

        productSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            priceInput.value = option.dataset.price || '';
            calculateSubtotal();
        });

        quantityInput.addEventListener('input', calculateSubtotal);
        priceInput.addEventListener('input', calculateSubtotal);

        removeBtn.addEventListener('click', function() {
            if (container.querySelectorAll('.product-row').length > 1) {
                row.remove();
                calculateTotal();
            }
        });

        function calculateSubtotal() {
            const qty = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const subtotal = qty * price;
            subtotalDisplay.value = '$' + subtotal.toFixed(2);
            calculateTotal();
        }
    }

    function calculateTotal() {
        let total = 0;
        container.querySelectorAll('.product-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += qty * price;
        });
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const products = [];
        container.querySelectorAll('.product-row').forEach(row => {
            const productId = row.querySelector('.product-select').value;
            const quantity = row.querySelector('.quantity-input').value;
            const price = row.querySelector('.price-input').value;
            
            if (productId && quantity && price) {
                products.push({
                    product_id: productId,
                    quantity: quantity,
                    price: price
                });
            }
        });

        if (products.length === 0) {
            alert('Debe agregar al menos un producto');
            return;
        }

        products.forEach((product, index) => {
            Object.keys(product).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `products[${index}][${key}]`;
                input.value = product[key];
                form.appendChild(input);
            });
        });

        form.submit();
    });

    attachRowEvents(container.querySelector('.product-row'));
});
</script>

<?php echo view('templates/footer'); ?>
