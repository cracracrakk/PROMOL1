<?php $pageTitle = 'Documento ' . $invoice['number']; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div><div class="breadcrumb"><a href="<?= url('/admin/facturas') ?>">← Facturas</a></div></div>
    <div style="display:flex;gap:8px;">
        <a href="<?= url('/admin/facturas/'.$invoice['id'].'/imprimir') ?>" target="_blank" class="btn-admin btn-outline">Imprimir / PDF</a>
        <?php if ($invoice['status'] !== 'anulada'): ?>
            <form method="post" action="<?= url('/admin/facturas/'.$invoice['id'].'/estado') ?>" data-confirm="¿Anular este documento?" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="anulada">
                <button type="submit" class="btn-admin btn-danger">Anular</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:30px;">
        <div>
            <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;"><?= e(strtoupper(str_replace('_',' ',$invoice['document_type']))) ?></h2>
            <p style="font-size:1.4rem;font-weight:600;margin-top:6px;"><?= e($invoice['number']) ?></p>
            <p style="color:var(--muted);font-size:.9rem;margin-top:8px;">CAI: <?= e($invoice['cai']) ?></p>
            <p style="color:var(--muted);font-size:.9rem;">Rango: <?= e($invoice['rango_autorizado']) ?></p>
            <p style="color:var(--muted);font-size:.9rem;">Fecha límite emisión: <?= dt($invoice['fecha_limite_emision'], 'd/m/Y') ?></p>
        </div>
        <div style="text-align:right;">
            <p><strong>Fecha:</strong> <?= dt($invoice['issue_date'], 'd/m/Y') ?></p>
            <p><strong>Estado:</strong> <span class="badge badge-<?= $invoice['status'] === 'pagada' ? 'success' : ($invoice['status'] === 'anulada' ? 'danger' : 'secondary') ?>"><?= e($invoice['status']) ?></span></p>
            <p><strong>Pago:</strong> <?= e($invoice['payment_method']) ?></p>
        </div>
    </div>

    <div class="form-grid" style="grid-template-columns:1fr 1fr;background:var(--bg);padding:18px;border-radius:8px;margin-bottom:20px;">
        <div>
            <strong>Emisor:</strong>
            <p><?= e(setting('sar_business_name', '')) ?></p>
            <p>RTN: <?= e(setting('sar_rtn', '')) ?></p>
            <p><?= nl2br(e(setting('sar_address', ''))) ?></p>
        </div>
        <div>
            <strong>Cliente:</strong>
            <p><?= e($invoice['customer_name']) ?></p>
            <?php if ($invoice['customer_rtn']): ?><p>RTN: <?= e($invoice['customer_rtn']) ?></p><?php endif; ?>
            <?php if ($invoice['customer_address']): ?><p><?= e($invoice['customer_address']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Descripción</th><th>Cant.</th><th>P. Unit.</th><th>ISV</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= e($it['description']) ?> <small style="color:var(--muted);">(<?= e($it['item_type']) ?>)</small></td>
                <td><?= (float)$it['quantity'] ?></td>
                <td><?= money($it['unit_price']) ?></td>
                <td><?= (float)$it['tax_rate'] ?>%</td>
                <td><strong><?= money($it['total']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <div style="max-width:400px;margin:30px 0 0 auto;padding:20px;background:var(--bg);border-radius:10px;">
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Subtotal:</span><span><?= money($invoice['subtotal']) ?></span></div>
        <?php if ($invoice['discount'] > 0): ?>
        <div style="display:flex;justify-content:space-between;padding:4px 0;color:#dc3545;"><span>Descuento:</span><span>-<?= money($invoice['discount']) ?></span></div>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Importe exento:</span><span><?= money($invoice['importe_exento']) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Gravado 15%:</span><span><?= money($invoice['importe_gravado_15']) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Gravado 18%:</span><span><?= money($invoice['importe_gravado_18']) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>ISV 15%:</span><span><?= money($invoice['isv_15']) ?></span></div>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>ISV 18%:</span><span><?= money($invoice['isv_18']) ?></span></div>
        <hr style="margin:8px 0;">
        <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:1.3rem;"><strong>Total:</strong><strong style="color:var(--accent);"><?= money($invoice['total']) ?></strong></div>
        <?php if ($invoice['tip'] > 0): ?>
        <div style="display:flex;justify-content:space-between;padding:4px 0;"><span>Propina:</span><span><?= money($invoice['tip']) ?></span></div>
        <?php endif; ?>
    </div>

    <p style="margin-top:20px;font-size:.9rem;color:var(--muted);"><strong>Total en letras:</strong> <?= e($invoice['total_letras']) ?></p>

    <?php if ($invoice['notes']): ?>
        <div style="margin-top:20px;padding:14px;background:var(--bg);border-radius:8px;"><strong>Notas:</strong> <?= e($invoice['notes']) ?></div>
    <?php endif; ?>

    <p style="margin-top:30px;text-align:center;font-style:italic;color:var(--muted);font-size:.85rem;">
        La factura es beneficio de todos, exíjala.
    </p>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
