<?php /** @var array $rx */ ?>
<!doctype html><html lang="es"><head>
<meta charset="utf-8"><title>Receta <?= e($rx['code']) ?></title>
<style>
body { font-family: Arial,sans-serif; padding: 2cm; color: #222; }
.head { display:flex; justify-content:space-between; align-items:flex-start; }
.brand { font-size:1.4rem; font-weight:800; color: #0ea5e9; }
.dr-info { text-align:right; font-size:0.9rem; }
.dr-info strong { font-size:1.1rem; color:#222; }
table { width:100%; border-collapse: collapse; margin: 1.5rem 0; }
th { background: #f5f5f5; padding: 0.5rem; text-align:left; }
td { padding: 0.5rem; border-bottom:1px solid #eee; }
.sig { margin-top:3rem; text-align:center; }
.sig-line { display:inline-block; border-top:1px solid #222; padding-top:6px; min-width:300px; }
@media print { @page { size: A4; margin: 1cm; } }
</style></head>
<body>
<div class="head">
    <div>
        <div class="brand">℞ <?= e(setting('clinic_name','DentalCore')) ?></div>
        <div><?= e(setting('clinic_address','')) ?></div>
        <div><?= e(setting('clinic_phone','')) ?></div>
    </div>
    <div class="dr-info">
        <strong>Dr/Dra. <?= e($rx['dentist_name']) ?></strong><br>
        <?= e($rx['dentist_specialty'] ?: '') ?><br>
        <?php if ($rx['dentist_license']): ?>Col. <?= e($rx['dentist_license']) ?><br><?php endif; ?>
    </div>
</div>

<h2 style="margin-top:1.5rem;">RECETA MÉDICA</h2>
<p><strong>Código:</strong> <?= e($rx['code']) ?> &nbsp;|&nbsp; <strong>Fecha:</strong> <?= e(format_date($rx['issue_date'])) ?></p>

<div style="background:#f9f9f9;padding:1rem;border-radius:6px;">
    <strong>Paciente:</strong> <?= e($rx['patient_name']) ?> (<?= e($rx['patient_code']) ?>)<br>
    <?php if ($rx['document_number']): ?><strong>Documento:</strong> <?= e($rx['document_number']) ?><br><?php endif; ?>
    <?php if ($rx['birth_date']): ?><strong>Edad:</strong> <?= age_from($rx['birth_date']) ?> años<?php endif; ?>
</div>

<?php if ($rx['diagnosis']): ?>
<p><strong>Diagnóstico:</strong> <?= e($rx['diagnosis']) ?></p>
<?php endif; ?>

<h3>℞ Indicaciones</h3>
<table>
    <thead><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th><th>Instrucciones</th></tr></thead>
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
<p><strong>Notas:</strong> <?= nl2br(e($rx['notes'])) ?></p>
<?php endif; ?>

<div class="sig">
    <div class="sig-line">
        Dr/Dra. <?= e($rx['dentist_name']) ?><br>
        <small><?= e($rx['dentist_specialty'] ?: '') ?></small>
    </div>
</div>

<script>window.onload = () => window.print();</script>
</body></html>
