<?php /** @var array $notes */ ?>
<h1>Mis tratamientos</h1>
<div class="card">
    <div class="card-body">
    <?php if (!$notes): ?>
        <p class="text-muted text-center">No hay registros clínicos aún.</p>
    <?php else: foreach ($notes as $n): ?>
        <div style="padding:1rem;border-left:3px solid var(--primary);background:var(--gray-50);border-radius:8px;margin-bottom:1rem;">
            <div class="flex justify-between mb-1">
                <strong><?= e(format_date($n['visit_date'], 'd \\d\\e F Y')) ?></strong>
                <span class="text-muted"><?= e($n['dentist_name']) ?></span>
            </div>
            <?php if ($n['diagnosis']): ?><p><strong>Diagnóstico:</strong> <?= nl2br(e($n['diagnosis'])) ?></p><?php endif; ?>
            <?php if ($n['treatment_done']): ?><p><strong>Tratamiento:</strong> <?= nl2br(e($n['treatment_done'])) ?></p><?php endif; ?>
            <?php if ($n['prescription']): ?><p><strong>Prescripción:</strong> <?= nl2br(e($n['prescription'])) ?></p><?php endif; ?>
        </div>
    <?php endforeach; endif; ?>
    </div>
</div>
