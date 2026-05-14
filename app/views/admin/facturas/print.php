<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= e($invoice['number']) ?> · <?= e(brand_name()) ?></title>
    <style>
        @page { size: A4; margin: 12mm; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 12px; line-height: 1.4; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 14px; margin-bottom: 16px; }
        .emisor h1 { font-size: 20px; margin: 0; }
        .emisor p { margin: 2px 0; }
        .docinfo { text-align: right; }
        .docinfo h2 { font-size: 16px; margin: 0; color: #444; }
        .docinfo .number { font-size: 18px; font-weight: bold; margin: 6px 0; }
        .cliente { background: #f5f5f5; padding: 10px; margin-bottom: 16px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #333; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .totals { width: 50%; margin-left: auto; }
        .totals td { padding: 4px 8px; }
        .totals tr.total td { border-top: 2px solid #000; font-size: 14px; font-weight: bold; padding-top: 8px; }
        .letras { margin-top: 12px; padding: 8px; background: #f5f5f5; font-size: 11px; }
        .cai { font-size: 10px; margin-top: 16px; padding-top: 10px; border-top: 1px dashed #999; }
        .legal { text-align: center; margin-top: 20px; font-style: italic; font-size: 10px; }
        .original { position: absolute; top: 60px; right: 20px; transform: rotate(15deg); border: 2px solid #999; padding: 4px 16px; font-size: 14px; color: #999; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="original">ORIGINAL</div>

    <div class="header">
        <div class="emisor">
            <?php if (brand_logo()): ?><img src="<?= brand_logo() ?>" style="height:50px;"><?php endif; ?>
            <h1><?= e(setting('sar_business_name', brand_name())) ?></h1>
            <?php if (setting('sar_trade_name')): ?><p><em><?= e(setting('sar_trade_name')) ?></em></p><?php endif; ?>
            <p>RTN: <strong><?= e(setting('sar_rtn', '')) ?></strong></p>
            <p><?= e(setting('sar_address', '')) ?></p>
            <p>Tel: <?= e(setting('sar_phone', '')) ?> · <?= e(setting('sar_email', '')) ?></p>
            <p>Régimen: <?= e(setting('sar_regimen', 'General')) ?></p>
        </div>
        <div class="docinfo">
            <h2><?= e(strtoupper(str_replace('_', ' DE ', $invoice['document_type']))) ?></h2>
            <p class="number"><?= e($invoice['number']) ?></p>
            <p><strong>Fecha:</strong> <?= dt($invoice['issue_date'], 'd/m/Y') ?></p>
            <p><strong>Forma de pago:</strong> <?= e(ucfirst($invoice['payment_method'])) ?></p>
        </div>
    </div>

    <div class="cliente">
        <strong>CLIENTE</strong>
        <p>Nombre: <strong><?= e($invoice['customer_name']) ?></strong></p>
        <?php if ($invoice['customer_rtn']): ?><p>RTN: <strong><?= e($invoice['customer_rtn']) ?></strong></p><?php endif; ?>
        <?php if ($invoice['customer_address']): ?><p>Dirección: <?= e($invoice['customer_address']) ?></p><?php endif; ?>
    </div>

    <table>
        <thead><tr>
            <th style="width:50%;">Descripción</th>
            <th style="width:8%;">Cant.</th>
            <th style="width:14%;">P. Unitario</th>
            <th style="width:8%;">ISV%</th>
            <th style="width:20%;text-align:right;">Total</th>
        </tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= e($it['description']) ?></td>
                <td><?= (float)$it['quantity'] ?></td>
                <td><?= money($it['unit_price']) ?></td>
                <td><?= (float)$it['tax_rate'] ?>%</td>
                <td style="text-align:right;"><?= money($it['total']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal:</td><td style="text-align:right;"><?= money($invoice['subtotal']) ?></td></tr>
        <?php if ($invoice['discount'] > 0): ?>
        <tr><td>Descuento:</td><td style="text-align:right;">-<?= money($invoice['discount']) ?></td></tr>
        <?php endif; ?>
        <tr><td>Importe exento:</td><td style="text-align:right;"><?= money($invoice['importe_exento']) ?></td></tr>
        <tr><td>Importe gravado 15%:</td><td style="text-align:right;"><?= money($invoice['importe_gravado_15']) ?></td></tr>
        <tr><td>Importe gravado 18%:</td><td style="text-align:right;"><?= money($invoice['importe_gravado_18']) ?></td></tr>
        <tr><td>ISV 15%:</td><td style="text-align:right;"><?= money($invoice['isv_15']) ?></td></tr>
        <tr><td>ISV 18%:</td><td style="text-align:right;"><?= money($invoice['isv_18']) ?></td></tr>
        <tr class="total"><td>TOTAL:</td><td style="text-align:right;"><?= money($invoice['total']) ?></td></tr>
    </table>

    <div class="letras">
        <strong>Valor en letras:</strong> <?= e($invoice['total_letras']) ?>
    </div>

    <?php if ($invoice['notes']): ?>
        <div style="margin-top:10px;font-size:11px;"><strong>Notas:</strong> <?= e($invoice['notes']) ?></div>
    <?php endif; ?>

    <div class="cai">
        <strong>CAI:</strong> <?= e($invoice['cai']) ?><br>
        <strong>Rango autorizado:</strong> <?= e($invoice['rango_autorizado']) ?><br>
        <strong>Fecha límite de emisión:</strong> <?= dt($invoice['fecha_limite_emision'], 'd/m/Y') ?>
    </div>

    <p class="legal">
        "La factura es beneficio de todos, exíjala"<br>
        Resolución SAR: <?= e(setting('sar_resolucion', '')) ?>
    </p>

    <div class="no-print" style="text-align:center;margin-top:30px;">
        <button onclick="window.print()" style="padding:10px 24px;background:#6b8a7a;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:14px;">Imprimir / Guardar PDF</button>
    </div>
</body>
</html>
