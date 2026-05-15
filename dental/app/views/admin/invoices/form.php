<?php /** @var array $treatments */ /** @var int $patient_id */ /** @var float $tax_rate */ ?>
<div class="page-header">
    <h1>Nueva factura</h1>
    <a href="<?= url('/admin/facturas') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= url('/admin/facturas/nueva') ?>" id="invoice-form">
    <?= csrf_field() ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Paciente *</label>
                    <input type="text" id="patient-search" placeholder="Buscar paciente...">
                    <input type="hidden" name="patient_id" id="patient-id" value="<?= $patient_id ?>" required>
                    <div id="patient-results" style="display:none;background:#fff;border:1px solid #ddd;border-radius:6px;max-height:200px;overflow-y:auto;"></div>
                    <div id="patient-selected" class="text-muted" style="font-size:0.85rem;"></div>
                </div>
                <div class="form-group">
                    <label>Fecha emisión</label>
                    <input type="date" name="issue_date" value="<?= date('Y-m-d') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Ítems</h2>
            <button type="button" id="add-item" class="btn btn-sm">+ Agregar línea</button>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>Tratamiento</th><th>Descripción</th><th>Pieza</th><th>Cant</th><th>Precio unit.</th><th>Total</th><th></th></tr></thead>
                <tbody id="items-body"></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Descuento</label>
                    <input type="number" step="0.01" name="discount" value="0" id="discount"></div>
                <div class="form-group"><label>Impuesto (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="<?= e($tax_rate) ?>" id="tax-rate"></div>
                <div class="form-group" style="text-align:right;font-size:1.1rem;">
                    <div>Subtotal: <strong id="subtotal-disp">L 0.00</strong></div>
                    <div>Impuesto: <strong id="tax-disp">L 0.00</strong></div>
                    <div style="font-size:1.3rem;color:var(--primary);">Total: <strong id="total-disp">L 0.00</strong></div>
                </div>
            </div>
            <div class="form-group">
                <label>Notas</label>
                <textarea name="notes" rows="2"></textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="<?= url('/admin/facturas') ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn btn-primary">Crear factura</button>
    </div>
</form>

<script>
const TREATMENTS = <?= json_encode(array_map(fn($t) => [
    'id' => (int)$t['id'], 'name' => $t['name'], 'price' => (float)$t['default_price'], 'code' => $t['code']
], $treatments)) ?>;

const tbody = document.getElementById('items-body');

function addRow(preset = {}) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="item_treatment[]" class="treatment-sel">
            <option value="">— libre —</option>
            ${TREATMENTS.map(t => '<option value="'+t.id+'" data-name="'+t.name+'" data-price="'+t.price+'">'+t.name+'</option>').join('')}
        </select></td>
        <td><input type="text" name="item_description[]" required></td>
        <td><input type="text" name="item_tooth[]" placeholder="ej: 16" style="width:60px;"></td>
        <td><input type="number" name="item_qty[]" value="1" min="1" class="qty" style="width:65px;"></td>
        <td><input type="number" name="item_price[]" step="0.01" value="0" class="price" style="width:100px;"></td>
        <td class="line-total" style="font-weight:600;">L 0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger remove">×</button></td>
    `;
    tbody.appendChild(tr);

    const sel = tr.querySelector('.treatment-sel');
    sel.addEventListener('change', () => {
        const opt = sel.options[sel.selectedIndex];
        if (opt.value) {
            tr.querySelector('input[name="item_description[]"]').value = opt.dataset.name;
            tr.querySelector('.price').value = opt.dataset.price;
            recalc();
        }
    });
    tr.querySelector('.qty').addEventListener('input', recalc);
    tr.querySelector('.price').addEventListener('input', recalc);
    tr.querySelector('.remove').addEventListener('click', () => { tr.remove(); recalc(); });
    recalc();
}

function recalc() {
    let subtotal = 0;
    tbody.querySelectorAll('tr').forEach(tr => {
        const q = parseFloat(tr.querySelector('.qty').value) || 0;
        const p = parseFloat(tr.querySelector('.price').value) || 0;
        const t = q * p;
        tr.querySelector('.line-total').textContent = 'L ' + t.toFixed(2);
        subtotal += t;
    });
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const rate = parseFloat(document.getElementById('tax-rate').value) || 0;
    const base = Math.max(0, subtotal - discount);
    const tax = base * rate / 100;
    document.getElementById('subtotal-disp').textContent = 'L ' + subtotal.toFixed(2);
    document.getElementById('tax-disp').textContent      = 'L ' + tax.toFixed(2);
    document.getElementById('total-disp').textContent    = 'L ' + (base + tax).toFixed(2);
}

document.getElementById('add-item').addEventListener('click', () => addRow());
document.getElementById('discount').addEventListener('input', recalc);
document.getElementById('tax-rate').addEventListener('input', recalc);
addRow();

// Búsqueda de paciente (mismo patrón que en citas)
const search = document.getElementById('patient-search');
const results = document.getElementById('patient-results');
const idInput = document.getElementById('patient-id');
const selDisp = document.getElementById('patient-selected');
let timer;
search.addEventListener('input', () => {
    clearTimeout(timer);
    const q = search.value.trim();
    if (q.length < 2) { results.style.display = 'none'; return; }
    timer = setTimeout(async () => {
        const data = await apiFetch('/api/patients?q=' + encodeURIComponent(q));
        results.innerHTML = '';
        data.data.slice(0, 8).forEach(p => {
            const it = document.createElement('div');
            it.style.cssText = 'padding:0.5rem 0.75rem;cursor:pointer;border-bottom:1px solid #eee;';
            it.innerHTML = '<strong>'+p.last_name+', '+p.first_name+'</strong> ('+p.code+')';
            it.onclick = () => {
                idInput.value = p.id;
                selDisp.innerHTML = '✓ <strong>'+p.first_name+' '+p.last_name+'</strong>';
                results.style.display = 'none'; search.value = '';
            };
            results.appendChild(it);
        });
        results.style.display = data.data.length ? 'block' : 'none';
    }, 250);
});

(async () => {
    if (idInput.value && idInput.value !== '0') {
        try {
            const p = await apiFetch('/api/patients/' + idInput.value);
            selDisp.innerHTML = '✓ <strong>'+p.first_name+' '+p.last_name+'</strong> ('+p.code+')';
        } catch (e) {}
    }
})();
</script>
