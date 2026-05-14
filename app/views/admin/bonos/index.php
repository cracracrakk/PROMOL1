<?php $pageTitle = 'Bonos y Tarjetas Regalo'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="card-grid">
    <div>
        <div class="card">
            <div class="card-header">
                <h3>🎁 Tarjetas Regalo</h3>
                <button class="btn-admin btn-sm" onclick="document.getElementById('giftForm').classList.toggle('hidden')">+ Nueva</button>
            </div>
            <form id="giftForm" class="hidden" method="post" action="<?= url('/admin/bonos/regalo') ?>" style="background:var(--bg);padding:14px;border-radius:8px;margin-bottom:16px;">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-group"><label>Importe (L.)</label><input type="number" step="0.01" name="amount" required></div>
                    <div class="form-group"><label>Comprador</label><input type="text" name="buyer_name" required></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Email comprador</label><input type="email" name="buyer_email"></div>
                    <div class="form-group"><label>Destinatario</label><input type="text" name="recipient_name"></div>
                    <div class="form-group"><label>Email destinatario</label><input type="email" name="recipient_email"></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Mensaje</label><textarea name="message" rows="2"></textarea></div>
                </div>
                <button class="btn-admin btn-block">Emitir tarjeta</button>
            </form>

            <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Código</th><th>Importe</th><th>Saldo</th><th>Destinatario</th><th>Caduca</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($giftCards as $gc): ?>
                    <tr>
                        <td><code><?= e($gc['code']) ?></code></td>
                        <td><?= money($gc['initial_amount']) ?></td>
                        <td><strong><?= money($gc['balance']) ?></strong></td>
                        <td><?= e($gc['recipient_name'] ?? $gc['buyer_name']) ?></td>
                        <td><?= dt($gc['expires_at'], 'd/m/Y') ?></td>
                        <td><span class="badge badge-<?= $gc['status'] === 'activa' ? 'success' : 'secondary' ?>"><?= e($gc['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h3>🎟️ Bonos disponibles</h3>
                <button class="btn-admin btn-sm" onclick="document.getElementById('packForm').classList.toggle('hidden')">+ Nuevo</button>
            </div>
            <form id="packForm" class="hidden" method="post" action="<?= url('/admin/bonos/bono') ?>" style="background:var(--bg);padding:14px;border-radius:8px;margin-bottom:16px;">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-group" style="grid-column:1/-1;"><label>Nombre del bono</label><input type="text" name="name" required></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><textarea name="description" rows="2"></textarea></div>
                    <div class="form-group"><label>Sesiones</label><input type="number" name="sessions_total" required value="10"></div>
                    <div class="form-group"><label>Precio (L.)</label><input type="number" step="0.01" name="price" required></div>
                    <div class="form-group"><label>Validez (meses)</label><input type="number" name="valid_months" value="6"></div>
                </div>
                <button class="btn-admin btn-block">Crear bono</button>
            </form>

            <?php foreach ($packs as $p): ?>
                <div style="padding:14px 0;border-bottom:1px solid var(--border);">
                    <strong><?= e($p['name']) ?></strong>
                    <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:.9rem;color:var(--muted);">
                        <span><?= (int)$p['sessions_total'] ?> sesiones · <?= money($p['price']) ?></span>
                        <span>Vendidos: <?= (int)$p['sold'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <h3>Bonos asignados a clientes</h3>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Código</th><th>Cliente</th><th>Bono</th><th>Sesiones</th><th>Compra</th><th>Caduca</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach ($customerPacks as $cp): ?>
            <tr>
                <td><code><?= e($cp['code']) ?></code></td>
                <td><?= e(trim($cp['first_name'].' '.$cp['last_name'])) ?></td>
                <td><?= e($cp['pack_name']) ?></td>
                <td><?= (int)$cp['sessions_used'] ?> / <?= (int)$cp['sessions_total'] ?></td>
                <td><?= dt($cp['purchased_at'], 'd/m/Y') ?></td>
                <td><?= dt($cp['expires_at'], 'd/m/Y') ?></td>
                <td><span class="badge badge-<?= $cp['status'] === 'activo' ? 'success' : 'secondary' ?>"><?= e($cp['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<style>.hidden { display: none; }</style>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
