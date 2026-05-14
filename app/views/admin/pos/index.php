<?php $pageTitle = 'Punto de Venta (POS)'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Caja rápida – Cobro inmediato</div>
</div>

<div class="card-grid" style="grid-template-columns:1.5fr 1fr;">
    <!-- Catálogo -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3>Catálogo</h3>
                <input type="text" id="searchItem" class="search-bar" placeholder="🔍 Buscar producto o servicio..." style="margin:0;">
            </div>
            <div style="margin-bottom:16px;">
                <button class="btn-admin btn-sm btn-outline active" data-tab="services">Servicios</button>
                <button class="btn-admin btn-sm btn-outline" data-tab="products">Productos</button>
            </div>
            <div id="catalogServices" class="catalog-grid">
                <?php foreach ($services as $s): ?>
                    <div class="catalog-item" data-type="servicio" data-id="<?= $s['id'] ?>" data-name="<?= e($s['name']) ?>" data-price="<?= e($s['price']) ?>" data-tax="<?= e($s['tax_rate']) ?>">
                        <strong><?= e($s['name']) ?></strong>
                        <small><?= (int)$s['duration_minutes'] ?> min</small>
                        <div class="price"><?= money($s['price']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="catalogProducts" class="catalog-grid" style="display:none;">
                <?php foreach ($products as $p): ?>
                    <div class="catalog-item" data-type="producto" data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-price="<?= e($p['price']) ?>" data-tax="<?= e($p['tax_rate']) ?>" data-stock="<?= (int)$p['stock'] ?>">
                        <strong><?= e($p['name']) ?></strong>
                        <small>Stock: <?= (int)$p['stock'] ?></small>
                        <div class="price"><?= money($p['price']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Ticket -->
    <div>
        <form method="post" action="<?= url('/admin/facturas/nueva') ?>" id="posForm" class="card">
            <?= csrf_field() ?>
            <h3>🧾 Ticket</h3>

            <div class="form-group">
                <label>Cliente</label>
                <select name="customer_id" id="customerSelect">
                    <option value="">Consumidor final</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" data-rtn="<?= e($c['tax_id']) ?>"><?= e(trim($c['first_name'].' '.$c['last_name'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="document_type" value="factura">
            <input type="hidden" name="customer_name" id="customerName" value="Consumidor final">
            <input type="hidden" name="customer_rtn" id="customerRtn">

            <table class="table" style="margin-top:12px;font-size:.9rem;">
                <thead><tr><th>Item</th><th>Cant</th><th></th></tr></thead>
                <tbody id="ticketBody">
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px;">Selecciona items del catálogo</td></tr>
                </tbody>
            </table>

            <div style="padding:14px;background:var(--bg);border-radius:8px;margin-top:12px;">
                <div style="display:flex;justify-content:space-between;"><span>Subtotal:</span><span id="posSub">L. 0.00</span></div>
                <div style="display:flex;justify-content:space-between;"><span>ISV:</span><span id="posTax">L. 0.00</span></div>
                <div style="display:flex;justify-content:space-between;font-size:1.4rem;margin-top:6px;border-top:1px solid var(--border);padding-top:6px;"><strong>TOTAL:</strong><strong style="color:var(--accent);" id="posTotal">L. 0.00</strong></div>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label>Forma de pago</label>
                <select name="payment_method">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="bizum">Bizum</option>
                </select>
            </div>

            <button type="submit" class="btn-admin btn-accent btn-block" style="padding:16px;font-size:1.1rem;">💰 Cobrar y emitir</button>
            <button type="button" class="btn-admin btn-outline btn-block" style="margin-top:8px;" onclick="clearTicket()">🗑️ Limpiar</button>
        </form>
    </div>
</div>

<style>
.catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; max-height: 500px; overflow-y: auto; padding: 4px; }
.catalog-item { background: var(--bg); padding: 14px; border-radius: 8px; cursor: pointer; transition: var(--transition); border: 2px solid transparent; }
.catalog-item:hover { background: var(--brand); color: #fff; transform: translateY(-2px); }
.catalog-item:hover small, .catalog-item:hover .price { color: #fff !important; }
.catalog-item strong { display: block; margin-bottom: 4px; font-size: .9rem; }
.catalog-item small { display: block; color: var(--muted); font-size: .75rem; }
.catalog-item .price { margin-top: 6px; font-weight: 700; color: var(--accent); font-family: 'Playfair Display', serif; font-size: 1.05rem; }
button[data-tab].active { background: var(--brand) !important; color: #fff !important; }
</style>

<script>
const ticket = new Map();
let lineCounter = 0;

document.querySelectorAll('.catalog-item').forEach(el => {
    el.addEventListener('click', () => {
        const key = el.dataset.type + '-' + el.dataset.id;
        if (ticket.has(key)) {
            ticket.get(key).qty++;
        } else {
            ticket.set(key, {
                type: el.dataset.type, id: el.dataset.id,
                name: el.dataset.name, price: parseFloat(el.dataset.price),
                tax: parseFloat(el.dataset.tax), qty: 1
            });
        }
        renderTicket();
    });
});

function renderTicket() {
    const tbody = document.getElementById('ticketBody');
    if (ticket.size === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px;">Selecciona items del catálogo</td></tr>';
        document.querySelectorAll('input[name^="items["]').forEach(el => el.remove());
        updateTotals();
        return;
    }
    tbody.innerHTML = '';
    document.querySelectorAll('input[name^="items["]').forEach(el => el.remove());

    let idx = 0;
    ticket.forEach((it, key) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${it.name}</strong><br><small>L. ${it.price.toFixed(2)} · ISV ${it.tax}%</small></td>
            <td>
                <button type="button" onclick="changeQty('${key}', -1)" style="background:none;border:1px solid var(--border);width:24px;height:24px;border-radius:4px;cursor:pointer;">-</button>
                <span style="padding:0 8px;">${it.qty}</span>
                <button type="button" onclick="changeQty('${key}', 1)" style="background:none;border:1px solid var(--border);width:24px;height:24px;border-radius:4px;cursor:pointer;">+</button>
            </td>
            <td><button type="button" onclick="removeItem('${key}')" style="background:none;border:none;color:#dc3545;cursor:pointer;">✕</button></td>`;
        tbody.appendChild(tr);

        // Hidden inputs para enviar a InvoiceController
        const form = document.getElementById('posForm');
        ['item_type','description','quantity','unit_price','tax_rate', it.type === 'servicio' ? 'service_id' : 'product_id'].forEach((field, i) => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = `items[${idx}][${field}]`;
            inp.value = ({item_type:it.type, description:it.name, quantity:it.qty, unit_price:it.price, tax_rate:it.tax, service_id:it.id, product_id:it.id})[field];
            form.appendChild(inp);
        });
        idx++;
    });
    updateTotals();
}

function changeQty(key, delta) {
    const it = ticket.get(key);
    it.qty += delta;
    if (it.qty <= 0) ticket.delete(key);
    renderTicket();
}
function removeItem(key) { ticket.delete(key); renderTicket(); }
function clearTicket()   { ticket.clear(); renderTicket(); }

function updateTotals() {
    let sub = 0, tax = 0;
    ticket.forEach(it => {
        const line = it.qty * it.price;
        sub += line;
        tax += line * (it.tax / 100);
    });
    document.getElementById('posSub').textContent   = 'L. ' + sub.toFixed(2);
    document.getElementById('posTax').textContent   = 'L. ' + tax.toFixed(2);
    document.getElementById('posTotal').textContent = 'L. ' + (sub + tax).toFixed(2);
}

// Tabs
document.querySelectorAll('[data-tab]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-tab]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('catalogServices').style.display = btn.dataset.tab === 'services' ? '' : 'none';
        document.getElementById('catalogProducts').style.display = btn.dataset.tab === 'products' ? '' : 'none';
    });
});

// Búsqueda
document.getElementById('searchItem').addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.catalog-item').forEach(it => {
        it.style.display = it.dataset.name.toLowerCase().includes(term) ? '' : 'none';
    });
});

// Cliente seleccionado → llenar nombre y RTN
document.getElementById('customerSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('customerName').value = this.value ? opt.textContent.trim() : 'Consumidor final';
    document.getElementById('customerRtn').value = opt.dataset.rtn || '';
});
</script>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
