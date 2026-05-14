<?php
$pageTitle = trim($customer['first_name'].' '.$customer['last_name']);
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
?>

<div class="page-header">
    <div>
        <div class="breadcrumb"><a href="<?= url('/admin/clientes') ?>">← Clientes</a></div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="<?= url('/admin/clientes/'.$customer['id'].'/editar') ?>" class="btn-admin btn-outline">Editar</a>
        <a href="<?= url('/admin/citas/nueva?customer='.$customer['id']) ?>" class="btn-admin">+ Cita</a>
    </div>
</div>

<div class="card-grid">
    <div>
        <div class="card">
            <h3 style="margin-bottom:14px;">Información</h3>
            <p><strong>📞</strong> <?= e($customer['phone']) ?></p>
            <p><strong>📧</strong> <?= e($customer['email']) ?></p>
            <p><strong>🎂</strong> <?= e($customer['birthdate'] ?? '—') ?></p>
            <p><strong>RTN:</strong> <?= e($customer['tax_id'] ?? '—') ?></p>
            <?php if ($customer['address']): ?><p><strong>📍</strong> <?= e($customer['address']) ?>, <?= e($customer['city']) ?></p><?php endif; ?>
            <p style="margin-top:14px;">
                <?php if ($customer['vip']): ?><span class="badge badge-warning">VIP</span> <?php endif; ?>
                <?php if ($customer['no_show_count'] > 0): ?><span class="badge badge-danger">No-shows: <?= (int)$customer['no_show_count'] ?></span> <?php endif; ?>
                <span class="badge badge-secondary"><?= (int)$customer['loyalty_points'] ?> pts</span>
            </p>
        </div>

        <?php if (!empty($customer['allergies']) || !empty($customer['medical_conditions']) || $customer['pregnant']): ?>
        <div class="card" style="border-left:4px solid #dc3545;">
            <h3>⚠️ Ficha clínica</h3>
            <?php if ($customer['allergies']): ?><p><strong>Alergias:</strong> <?= e($customer['allergies']) ?></p><?php endif; ?>
            <?php if ($customer['medical_conditions']): ?><p><strong>Condiciones:</strong> <?= e($customer['medical_conditions']) ?></p><?php endif; ?>
            <?php if ($customer['medications']): ?><p><strong>Medicación:</strong> <?= e($customer['medications']) ?></p><?php endif; ?>
            <?php if ($customer['pregnant']): ?><p><strong>🤰 Embarazo:</strong> <?= (int)$customer['pregnancy_weeks'] ?> semanas</p><?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <h3>Resumen</h3>
            <p><strong>Total gastado:</strong> <?= money($totalSpent) ?></p>
            <p><strong>Visitas:</strong> <?= count($appointments) ?></p>
        </div>

        <?php $wallet = Database::fetch('SELECT * FROM customer_wallets WHERE customer_id = ?', [$customer['id']]); ?>
        <div class="card" style="background:linear-gradient(135deg,var(--accent),#a88a52);color:#fff;">
            <h3 style="color:#fff;">💳 Saldo del cliente</h3>
            <p style="font-size:2rem;font-family:'Playfair Display',serif;margin:10px 0;"><?= money($wallet['balance'] ?? 0) ?></p>
            <form method="post" action="<?= url('/admin/wallet/recargar') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                <input type="number" name="amount" step="0.01" placeholder="Importe a recargar" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;padding:8px;border-radius:6px;width:100%;margin-bottom:8px;">
                <input type="text" name="reason" placeholder="Motivo (opcional)" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;padding:8px;border-radius:6px;width:100%;margin-bottom:8px;">
                <button type="submit" class="btn-admin" style="background:#fff;color:var(--accent);width:100%;">+ Recargar saldo</button>
            </form>
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Historial de citas</h3>
            <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Fecha</th><th>Servicio</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= dt($a['starts_at'], 'd/m/Y H:i') ?></td>
                        <td><?= e($a['service_name']) ?></td>
                        <td><span class="badge badge-secondary"><?= e($a['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);">Sin citas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

        <div class="card">
            <h3>Historial de facturas</h3>
            <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Número</th><th>Fecha</th><th>Total</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td><?= e($inv['number']) ?></td>
                        <td><?= dt($inv['issue_date'], 'd/m/Y') ?></td>
                        <td><?= money($inv['total']) ?></td>
                        <td><a href="<?= url('/admin/facturas/'.$inv['id']) ?>" class="btn-admin btn-sm btn-outline">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);">Sin facturas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
