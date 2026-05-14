<?php $pageTitle = 'Nueva factura'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb"><a href="<?= url('/admin/facturas') ?>">← Volver</a></div>
</div>

<form method="post" action="<?= url('/admin/facturas/nueva') ?>" class="card" id="invoiceForm">
    <?= csrf_field() ?>

    <div class="form-section">
        <h3>Tipo de documento</h3>
        <div class="form-group">
            <select name="document_type" required>
                <option value="factura">Factura</option>
                <option value="recibo">Recibo</option>
                <option value="nota_credito">Nota de Crédito</option>
                <option value="nota_debito">Nota de Débito</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h3>Cliente</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Cliente registrado</label>
                <select name="customer_id" id="customerSelect">
                    <option value="">— Consumidor final —</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" data-rtn="<?= e($c['tax_id']) ?>" <?= ($appointment && $appointment['cid'] == $c['id']) ? 'selected' : '' ?>>
                            <?= e(trim($c['first_name'].' '.$c['last_name'])) ?> – <?= e($c['phone']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Nombre (si consumidor final)</label><input type="text" name="customer_name" value="<?= e($appointment ? trim(($appointment['first_name'] ?? '').' '.($appointment['last_name'] ?? '')) : '') ?>"></div>
            <div class="form-group"><label>RTN</label><input type="text" name="customer_rtn" id="customerRtn" value="<?= e($appointment['tax_id'] ?? '') ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Dirección</label><input type="text" name="customer_address" value="<?= e($appointment['address'] ?? '') ?>"></div>
        </div>
        <?php if ($appointment): ?><input type="hidden" name="appointment_id" value="<?= (int)$appointment['id'] ?>"><?php endif; ?>
    </div>

    <div class="form-section">
        <h3>Líneas del documento</h3>
        <div class="table-wrap">
        <table class="table" id="itemsTable">
            <thead><tr>
                <th style="width:120px;">Tipo</th>
                <th>Descripción</th>
                <th style="width:90px;">Cant.</th>
                <th style="width:120px;">P. Unit.</th>
                <th style="width:100px;">ISV %</th>
                <th style="width:120px;">Total</th>
                <th></th>
            </tr></thead>
            <tbody id="itemsBody">
                <?php if ($appointment): ?>
                <tr>
                    <td><select name="items[0][item_type]"><option value="servicio">Servicio</option><option value="producto">Producto</option><option value="otro">Otro</option></select></td>
                    <td><input type="text" name="items[0][description]" value="<?= e($appointment['service_name']) ?>" required></td>
                    <td><input type="number" name="items[0][quantity]" value="1" step="1" required class="qty"></td>
                    <td><input type="number" name="items[0][unit_price]" value="<?= e($appointment['service_price']) ?>" step="0.01" required class="price"></td>
                    <td><select name="items[0][tax_rate]" class="tax"><option value="15" selected>15%</option><option value="18">18%</option><option value="0">0%</option></select></td>
                    <td class="line-total"><?= money($appointment['service_price'] * 1.15) ?></td>
                    <td><button type="button" class="btn-icon danger" onclick="removeLine(this)">✕</button></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <button type="button" class="btn-admin btn-outline btn-sm" onclick="addLine()">+ Añadir línea</button>
        <button type="button" class="btn-admin btn-outline btn-sm" onclick="addServiceLine()">+ Servicio</button>
        <button type="button" class="btn-admin btn-outline btn-sm" onclick="addProductLine()">+ Producto</button>
    </div>

    <div class="form-section">
        <h3>Descuento y pago</h3>
        <div class="form-grid">
            <div class="form-group"><label>Descuento (L.)</label><input type="number" name="discount" id="discount" step="0.01" value="0" onchange="recalc()"></div>
            <div class="form-group"><label>Motivo descuento</label><input type="text" name="discount_reason"></div>
            <div class="form-group"><label>Código promo</label><input type="text" name="promo_code"></div>
            <div class="form-group"><label>Propina (L.)</label><input type="number" name="tip" step="0.01" value="0"></div>
            <div class="form-group">
                <label>Forma de pago</label>
                <select name="payment_method">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="cheque">Cheque</option>
                    <option value="tarjeta_regalo">Tarjeta Regalo</option>
                    <option value="bono">Bono</option>
                    <option value="mixto">Mixto</option>
                </select>
            </div>
            <div class="form-group"><label>Referencia pago</label><input type="text" name="payment_reference"></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Resumen</h3>
        <div style="max-width:400px;margin-left:auto;">
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>Subtotal:</span><strong id="sumSubtotal">L. 0.00</strong></div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>Gravado 15%:</span><span id="sumGravado15">L. 0.00</span></div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>Gravado 18%:</span><span id="sumGravado18">L. 0.00</span></div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>Exento:</span><span id="sumExento">L. 0.00</span></div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>ISV 15%:</span><span id="sumIsv15">L. 0.00</span></div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;"><span>ISV 18%:</span><span id="sumIsv18">L. 0.00</span></div>
            <hr>
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:1.3rem;"><strong>Total:</strong><strong id="sumTotal" style="color:var(--accent);">L. 0.00</strong></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Notas</h3>
        <div class="form-group"><textarea name="notes" rows="2"></textarea></div>
    </div>

    <div class="form-actions">
        <a href="<?= url('/admin/facturas') ?>" class="btn-admin btn-outline">Cancelar</a>
        <button type="submit" class="btn-admin btn-accent">Emitir documento</button>
    </div>
</form>

<script>
const services = <?= json_encode(array_map(fn($s) => ['id'=>$s['id'], 'name'=>$s['name'], 'price'=>$s['price'], 'tax'=>$s['tax_rate']], $services)) ?>;
const products = <?= json_encode(array_map(fn($p) => ['id'=>$p['id'], 'name'=>$p['name'], 'price'=>$p['sale_price'], 'tax'=>$p['tax_rate'], 'stock'=>$p['stock']], $products)) ?>;

let lineIndex = <?= isset($appointment) ? 1 : 0 ?>;

function addLine(type = 'otro', desc = '', price = 0, tax = 15) {
    const tbody = document.getElementById('itemsBody');
    const i = lineIndex++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[${i}][item_type]"><option value="servicio"${type==='servicio'?' selected':''}>Servicio</option><option value="producto"${type==='producto'?' selected':''}>Producto</option><option value="otro"${type==='otro'?' selected':''}>Otro</option></select></td>
        <td><input type="text" name="items[${i}][description]" value="${desc}" required></td>
        <td><input type="number" name="items[${i}][quantity]" value="1" step="1" required class="qty"></td>
        <td><input type="number" name="items[${i}][unit_price]" value="${price}" step="0.01" required class="price"></td>
        <td><select name="items[${i}][tax_rate]" class="tax"><option value="15"${tax==15?' selected':''}>15%</option><option value="18"${tax==18?' selected':''}>18%</option><option value="0"${tax==0?' selected':''}>0%</option></select></td>
        <td class="line-total">L. 0.00</td>
        <td><button type="button" class="btn-icon danger" onclick="removeLine(this)">✕</button></td>`;
    tbody.appendChild(tr);
    bindEvents(tr);
    recalc();
}

function addServiceLine() {
    const id = prompt("ID del servicio:\n" + services.map(s => s.id + ': ' + s.name).join('\n'));
    if (!id) return;
    const s = services.find(x => x.id == id);
    if (!s) return alert('No encontrado');
    addLine('servicio', s.name, s.price, s.tax);
}

function addProductLine() {
    const id = prompt("ID del producto:\n" + products.map(p => p.id + ': ' + p.name + ' (stock: ' + p.stock + ')').join('\n'));
    if (!id) return;
    const p = products.find(x => x.id == id);
    if (!p) return alert('No encontrado');
    addLine('producto', p.name, p.price, p.tax);
}

function removeLine(btn) { btn.closest('tr').remove(); recalc(); }

function bindEvents(tr) {
    tr.querySelectorAll('.qty, .price, .tax').forEach(el => el.addEventListener('input', recalc));
}

function recalc() {
    let sub = 0, g15 = 0, g18 = 0, ex = 0, isv15 = 0, isv18 = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const q = parseFloat(tr.querySelector('.qty')?.value || 0);
        const p = parseFloat(tr.querySelector('.price')?.value || 0);
        const t = parseFloat(tr.querySelector('.tax')?.value || 0);
        const line = q * p;
        sub += line;
        if (t == 0) ex += line;
        else if (t == 18) { g18 += line; isv18 += line * 0.18; }
        else { g15 += line; isv15 += line * 0.15; }
        const total = line * (1 + t/100);
        const cell = tr.querySelector('.line-total');
        if (cell) cell.textContent = 'L. ' + total.toFixed(2);
    });

    const disc = parseFloat(document.getElementById('discount').value) || 0;
    if (disc > 0 && sub > 0) {
        const f = (sub - disc) / sub;
        g15 *= f; g18 *= f; ex *= f;
        isv15 = g15 * 0.15; isv18 = g18 * 0.18;
    }
    const total = g15 + g18 + ex + isv15 + isv18;

    document.getElementById('sumSubtotal').textContent  = 'L. ' + sub.toFixed(2);
    document.getElementById('sumGravado15').textContent = 'L. ' + g15.toFixed(2);
    document.getElementById('sumGravado18').textContent = 'L. ' + g18.toFixed(2);
    document.getElementById('sumExento').textContent    = 'L. ' + ex.toFixed(2);
    document.getElementById('sumIsv15').textContent     = 'L. ' + isv15.toFixed(2);
    document.getElementById('sumIsv18').textContent     = 'L. ' + isv18.toFixed(2);
    document.getElementById('sumTotal').textContent     = 'L. ' + total.toFixed(2);
}

document.querySelectorAll('#itemsBody tr').forEach(bindEvents);
recalc();

// Autocompletar RTN al seleccionar cliente
document.getElementById('customerSelect').addEventListener('change', function() {
    const rtn = this.options[this.selectedIndex].dataset.rtn || '';
    document.getElementById('customerRtn').value = rtn;
});
</script>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
