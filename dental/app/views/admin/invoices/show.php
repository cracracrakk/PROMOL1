<?php /** @var array $invoice */ ?>
<div class="page-header">
    <div>
        <h1>Factura <?= e($invoice['code']) ?></h1>
        <p class="subtitle"><?= e($invoice['patient_name']) ?> · Emitida <?= e(format_date($invoice['issue_date'])) ?></p>
    </div>
    <div class="flex gap-1">
        <a href="<?= url('/admin/facturas/'.$invoice['id'].'/imprimir') ?>" target="_blank" class="btn">Imprimir</a>
        <a href="<?= url('/admin/facturas') ?>" class="btn">← Volver</a>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr;gap:1rem;">
    <div class="card">
        <div class="card-header"><h2>Detalle</h2>
            <?php
            $cls = ['paid'=>'badge-success','partial'=>'badge-warning','cancelled'=>'badge-danger',
                    'issued'=>'badge-info','draft'=>'badge-muted'][$invoice['status']] ?? 'badge-muted'; ?>
            <span class="badge <?= $cls ?>"><?= e($invoice['status']) ?></span>
        </div>
        <table>
            <thead><tr><th>Descripción</th><th>Pieza</th><th>Cant</th><th>Precio</th><th class="text-right">Total</th></tr></thead>
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
                <tr style="font-size:1.1rem;"><td colspan="4" class="text-right"><strong>TOTAL</strong></td><td class="text-right"><strong><?= money((float)$invoice['total']) ?></strong></td></tr>
                <tr><td colspan="4" class="text-right">Pagado</td><td class="text-right"><?= money((float)$invoice['paid']) ?></td></tr>
                <tr style="font-size:1.1rem;color:var(--danger);"><td colspan="4" class="text-right"><strong>SALDO</strong></td><td class="text-right"><strong><?= money((float)$invoice['balance']) ?></strong></td></tr>
            </tfoot>
        </table>
    </div>

    <div>
        <div class="card mb-3">
            <div class="card-header"><h2>Registrar pago</h2></div>
            <div class="card-body">
                <?php if ((float)$invoice['balance'] > 0): ?>
                <form method="post" action="<?= url('/admin/facturas/'.$invoice['id'].'/pago') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group"><label>Monto</label>
                        <input type="number" step="0.01" name="amount" max="<?= e($invoice['balance']) ?>"
                               value="<?= e($invoice['balance']) ?>" required></div>
                    <div class="form-group"><label>Método</label>
                        <select name="method">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="check">Cheque</option>
                            <option value="wallet">Billetera</option>
                            <option value="other">Otro</option>
                        </select></div>
                    <div class="form-group"><label>Referencia</label>
                        <input type="text" name="reference"></div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Registrar pago</button>
                </form>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:1rem;">✓ Factura totalmente pagada</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2>Pagos registrados</h2></div>
            <div class="card-body" style="font-size:0.85rem;">
                <?php if (!$invoice['payments']): ?>
                    <p class="text-muted text-center">Sin pagos</p>
                <?php else: foreach ($invoice['payments'] as $pm): ?>
                    <div style="padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                        <div class="flex justify-between">
                            <strong><?= money((float)$pm['amount']) ?></strong>
                            <span class="text-muted"><?= e(format_date($pm['paid_at'], 'd/m/Y')) ?></span>
                        </div>
                        <div class="text-muted"><?= e($pm['method']) ?> <?= $pm['reference'] ? '· ref: '.e($pm['reference']) : '' ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
