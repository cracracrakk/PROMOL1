<?php $pageTitle = 'Newsletter'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header"><div class="breadcrumb">Envío masivo de comunicaciones</div></div>

<div class="card-grid">
    <form method="post" action="<?= url('/admin/newsletter/enviar') ?>" class="card">
        <?= csrf_field() ?>
        <h3>Nuevo envío</h3>
        <div class="form-group">
            <label>Segmento</label>
            <select name="segment">
                <option value="all">Todos los clientes con marketing aceptado</option>
                <option value="vip">Solo VIP</option>
                <option value="recent">Activos (últimos 30 días)</option>
                <option value="inactive">Inactivos (60+ días)</option>
            </select>
        </div>
        <div class="form-group"><label>Asunto *</label><input type="text" name="subject" required></div>
        <div class="form-group">
            <label>Mensaje *</label>
            <textarea name="body" rows="10" required placeholder="Hola {{name}}, ..."></textarea>
            <p class="form-help">Variables: <code>{{name}}</code> · <code>{{firstname}}</code></p>
        </div>
        <button type="submit" class="btn-admin btn-accent" onclick="return confirm('¿Confirmar envío?')">📧 Enviar newsletter</button>
    </form>

    <div class="card">
        <h3>Historial</h3>
        <?php foreach ($history as $h): ?>
            <div style="padding:12px 0;border-bottom:1px solid var(--border);">
                <strong><?= e($h['subject']) ?></strong>
                <div style="display:flex;justify-content:space-between;color:var(--muted);font-size:.85rem;margin-top:4px;">
                    <span>📨 <?= (int)$h['sent'] ?> / <?= (int)$h['recipients'] ?></span>
                    <span><?= dt($h['created_at'], 'd/m H:i') ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($history)): ?>
            <p style="color:var(--muted);text-align:center;padding:30px;">Sin envíos aún.</p>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
