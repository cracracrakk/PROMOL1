<?php /** @var array $patient */ /** @var array $notes */ ?>
<div class="page-header">
    <div>
        <h1>Historia clínica</h1>
        <p class="subtitle"><?= e($patient['first_name'].' '.$patient['last_name']) ?> · <?= e($patient['code']) ?></p>
    </div>
    <a href="<?= url('/admin/pacientes/'.$patient['id']) ?>" class="btn">← Volver</a>
</div>

<div class="grid" style="grid-template-columns: 1fr 2fr; gap:1rem;">
    <div class="card">
        <div class="card-header"><h2>Nueva nota</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/pacientes/'.$patient['id'].'/historia') ?>">
                <?= csrf_field() ?>
                <div class="form-group"><label>Fecha</label>
                    <input type="date" name="visit_date" value="<?= date('Y-m-d') ?>"></div>
                <div class="form-group"><label>Motivo de consulta</label>
                    <textarea name="chief_complaint" rows="2"></textarea></div>
                <div class="form-group"><label>Diagnóstico</label>
                    <textarea name="diagnosis" rows="2"></textarea></div>
                <div class="form-group"><label>Tratamiento realizado</label>
                    <textarea name="treatment_done" rows="2"></textarea></div>
                <div class="form-group"><label>Prescripción</label>
                    <textarea name="prescription" rows="2"></textarea></div>
                <div class="form-group"><label>Próxima visita</label>
                    <textarea name="next_visit" rows="1"></textarea></div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Guardar nota</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Notas anteriores</h2></div>
        <div class="card-body" style="max-height:700px;overflow-y:auto;">
            <?php if (!$notes): ?>
                <p class="text-muted text-center">Aún no hay notas clínicas.</p>
            <?php else: foreach ($notes as $n): ?>
                <div style="padding:1rem;border-left:3px solid var(--primary);background:var(--gray-50);border-radius:8px;margin-bottom:1rem;">
                    <div class="flex justify-between mb-1">
                        <strong><?= e(format_date($n['visit_date'], 'd \\d\\e F Y')) ?></strong>
                        <span class="text-muted"><?= e($n['dentist_name']) ?></span>
                    </div>
                    <?php if ($n['chief_complaint']): ?><p><strong>Motivo:</strong> <?= nl2br(e($n['chief_complaint'])) ?></p><?php endif; ?>
                    <?php if ($n['diagnosis']): ?><p><strong>Diagnóstico:</strong> <?= nl2br(e($n['diagnosis'])) ?></p><?php endif; ?>
                    <?php if ($n['treatment_done']): ?><p><strong>Tratamiento:</strong> <?= nl2br(e($n['treatment_done'])) ?></p><?php endif; ?>
                    <?php if ($n['prescription']): ?><p><strong>Prescripción:</strong> <?= nl2br(e($n['prescription'])) ?></p><?php endif; ?>
                    <?php if ($n['next_visit']): ?><p><strong>Próxima visita:</strong> <?= nl2br(e($n['next_visit'])) ?></p><?php endif; ?>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
