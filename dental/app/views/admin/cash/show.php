<?php /** @var array $session */ /** @var array $movements */ ?>
<div class="page-header">
    <div><h1>Sesión de caja</h1>
        <p class="subtitle"><?= e(format_date($session['opened_at'], 'd/m/Y H:i')) ?> — <?= e(format_date($session['closed_at'] ?? '', 'd/m/Y H:i') ?: 'En curso') ?></p>
    </div>
    <a href="<?= url('/admin/caja') ?>" class="btn">← Volver</a>
</div>
<div class="grid grid-2 mb-3">
    <div class="stat"><div class="label">Apertura</div><div class="value"><?= money((float)$session['opening_amount']) ?></div></div>
    <div class="stat"><div class="label">Cierre</div><div class="value"><?= money((float)($session['closing_amount'] ?? 0)) ?></div></div>
</div>
<div class="card"><div class="card-body"><?= e($session['notes'] ?: '—') ?></div></div>
