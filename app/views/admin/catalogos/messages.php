<?php $pageTitle = 'Mensajes de contacto'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Mensajes recibidos desde el formulario web</div>
</div>

<div class="card">
    <?php if (empty($messages)): ?>
        <p style="color:var(--muted);text-align:center;padding:40px;">No hay mensajes.</p>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
        <div style="padding:16px;border-bottom:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <strong><?= e($m['name']) ?></strong>
                <small style="color:var(--muted);"><?= dt($m['created_at']) ?></small>
            </div>
            <div style="color:var(--muted);font-size:.9rem;">📧 <?= e($m['email']) ?> · 📞 <?= e($m['phone']) ?></div>
            <p style="margin-top:10px;"><?= nl2br(e($m['message'])) ?></p>
            <div style="margin-top:10px;">
                <a href="mailto:<?= e($m['email']) ?>" class="btn-admin btn-sm btn-outline">Responder</a>
                <form method="post" action="<?= url('/admin/catalogos/mensajes/'.$m['id'].'/eliminar') ?>" data-confirm="¿Eliminar mensaje?" style="display:inline;">
                    <?= csrf_field() ?>
                    <button class="btn-admin btn-sm btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
