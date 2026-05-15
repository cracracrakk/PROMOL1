<?php /** @var string $action */ /** @var array $data */ ?>
<section style="max-width:520px;margin:5rem auto;text-align:center;padding:0 1.5rem;">
    <?php if ($action === 'confirm'): ?>
        <div style="font-size:5rem;color:var(--success);">✓</div>
        <h1>¡Cita confirmada!</h1>
        <p class="text-muted">Gracias <?= e($data['patient_name']) ?>, tu cita del
            <strong><?= e(format_date($data['starts_at'], 'd/m/Y H:i')) ?></strong>
            con <?= e($data['dentist_name']) ?> ha quedado confirmada.</p>
    <?php else: ?>
        <div style="font-size:5rem;color:var(--danger);">✕</div>
        <h1>Cita cancelada</h1>
        <p class="text-muted">Tu cita ha sido cancelada. Si fue por error, contáctanos para reprogramar.</p>
    <?php endif; ?>
    <a href="<?= url('/') ?>" class="btn btn-primary mt-3">Volver al inicio</a>
</section>
