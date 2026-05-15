<?php /** @var array $patient */ /** @var array $exams */ ?>
<div class="page-header">
    <div>
        <h1>Periodontograma — <?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
        <p class="subtitle">Examen periodontal · <?= count($exams) ?> registros</p>
    </div>
    <div class="flex gap-1">
        <form method="post" action="<?= url('/admin/pacientes/'.$patient['id'].'/periodontograma/nuevo') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="exam_date" value="<?= date('Y-m-d') ?>">
            <button type="submit" class="btn btn-primary">+ Nuevo examen</button>
        </form>
        <a href="<?= url('/admin/pacientes/'.$patient['id']) ?>" class="btn">← Volver</a>
    </div>
</div>

<div class="card">
    <table>
        <thead><tr><th>Fecha</th><th>Examinador</th><th>Notas</th><th></th></tr></thead>
        <tbody>
            <?php if (!$exams): ?><tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Sin exámenes. Crea el primero para empezar.</td></tr><?php endif; ?>
            <?php foreach ($exams as $ex): ?>
                <tr>
                    <td><?= e(format_date($ex['exam_date'])) ?></td>
                    <td><?= e($ex['examiner_name'] ?: '—') ?></td>
                    <td class="text-muted"><?= e(mb_strimwidth($ex['notes'] ?? '', 0, 80, '…')) ?></td>
                    <td><a href="<?= url('/admin/periodontograma/'.$ex['id']) ?>" class="btn btn-sm">Ver / editar</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
