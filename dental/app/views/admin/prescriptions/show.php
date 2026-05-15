<?php /** @var array $rx */ ?>
<div class="page-header">
    <div><h1>Receta <?= e($rx['code']) ?></h1>
        <p class="subtitle"><?= e($rx['patient_name']) ?> · <?= e(format_date($rx['issue_date'])) ?></p></div>
    <a href="<?= url('/admin/recetas/'.$rx['id'].'/imprimir') ?>" target="_blank" class="btn btn-primary">Imprimir</a>
</div>
<div class="card">
    <div class="card-body">
        <?php if ($rx['diagnosis']): ?>
            <p><strong>Diagnóstico:</strong> <?= e($rx['diagnosis']) ?></p>
        <?php endif; ?>
        <h3>Medicamentos</h3>
        <table>
            <thead><tr><th>Fármaco</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th><th>Instrucciones</th></tr></thead>
            <tbody>
                <?php foreach ($rx['items'] as $it): ?>
                    <tr>
                        <td><strong><?= e($it['drug']) ?></strong></td>
                        <td><?= e($it['dosage']) ?></td>
                        <td><?= e($it['frequency']) ?></td>
                        <td><?= e($it['duration']) ?></td>
                        <td><?= e($it['instructions']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($rx['notes']): ?>
            <p class="mt-2"><strong>Notas:</strong> <?= nl2br(e($rx['notes'])) ?></p>
        <?php endif; ?>
    </div>
</div>
