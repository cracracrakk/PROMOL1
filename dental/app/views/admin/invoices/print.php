<?php /** @var array $invoice */ ?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><title>Factura <?= e($invoice['code']) ?></title>
<style>
body { font-family: 'Inter', sans-serif; color:#222; margin:0; padding:2cm; }
.head { display:flex; justify-content:space-between; margin-bottom:2rem; }
.brand { font-size:1.5rem; font-weight:800; }
table { width:100%; border-collapse:collapse; margin:1.5rem 0; }
th,td { padding:0.5rem; border-bottom:1px solid #eee; text-align:left; }
.text-right { text-align:right; }
.total { font-size:1.3rem; font-weight:800; }
@media print { @page { size: A4; margin: 1cm; } }
</style></head><body>
<div class="head">
    <div>
        <div class="brand"><?= e(setting('clinic_name', 'DentalCore')) ?></div>
        <div><?= e(setting('clinic_address', '')) ?></div>
        <div><?= e(setting('clinic_phone', '')) ?> · <?= e(setting('clinic_email', '')) ?></div>
        <div>RTN: <?= e(setting('clinic_rtn', '')) ?></div>
    </div>
    <div class="text-right">
        <h2 style="margin:0;">FACTURA</h2>
        <div><strong><?= e($invoice['code']) ?></strong></div>
        <div>Fecha: <?= e(format_date($invoice['issue_date'])) ?></div>
        <?php if ($invoice['due_date']): ?><div>Vencimiento: <?= e(format_date($invoice['due_date'])) ?></div><?php endif; ?>
    </div>
</div>

<div style="background:#f5f5f5;padding:1rem;border-radius:6px;margin-bottom:1rem;">
    <strong>Cliente:</strong> <?= e($invoice['patient_name']) ?> (<?= e($invoice['patient_code']) ?>)<br>
    <strong>Documento:</strong> <?= e($invoice['patient_doc'] ?: '—') ?><br>
    <strong>Dirección:</strong> <?= e($invoice['patient_address'] ?: '—') ?><br>
    <strong>Email:</strong> <?= e($invoice['patient_email'] ?: '—') ?>
</div>

<table>
    <thead><tr><th>Descripción</th><th>Pieza</th><th>Cant.</th><th>P. Unit.</th><th class="text-right">Total</th></tr></thead>
    <tbody>
        <?php foreach ($invoice['items'] as $it): ?>
        <tr>
            <td><?= e($it['description']) ?></td>
            <td><?= e($it['tooth_code'] ?: '—') ?></td>
            <td><?= (int)$it['quantity'] ?></td>
            <td><?= money((float)$it['unit_price']) ?></td>
            <td class="text-right"><?= money((float)$it['line_total']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><td colspan="4" class="text-right">Subtotal</td><td class="text-right"><?= money((float)$invoice['subtotal']) ?></td></tr>
        <?php if ((float)$invoice['discount'] > 0): ?>
        <tr><td colspan="4" class="text-right">Descuento</td><td class="text-right">- <?= money((float)$invoice['discount']) ?></td></tr>
        <?php endif; ?>
        <tr><td colspan="4" class="text-right">Impuesto (<?= e($invoice['tax_rate']) ?>%)</td><td class="text-right"><?= money((float)$invoice['tax_amount']) ?></td></tr>
        <tr class="total"><td colspan="4" class="text-right">TOTAL</td><td class="text-right"><?= money((float)$invoice['total']) ?></td></tr>
    </tfoot>
</table>

<?php if ($invoice['notes']): ?>
<div style="margin-top:2rem;font-size:0.9rem;color:#555;"><strong>Notas:</strong> <?= nl2br(e($invoice['notes'])) ?></div>
<?php endif; ?>

<script>window.onload = () => window.print();</script>
</body></html>
