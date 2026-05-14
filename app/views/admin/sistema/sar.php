<?php $pageTitle = 'Autorizaciones SAR / CAI'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Códigos de autorización CAI emitidos por SAR Honduras</div>
    <button class="btn-admin" onclick="document.getElementById('caiForm').classList.toggle('hidden')">+ Nueva autorización</button>
</div>

<div class="card hidden" id="caiForm">
    <form method="post" action="<?= url('/admin/sistema/sar/guardar') ?>">
        <?= csrf_field() ?>
        <h3>Registrar autorización CAI</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Tipo de documento *</label>
                <select name="document_type" required>
                    <option value="factura">Factura</option>
                    <option value="recibo">Recibo</option>
                    <option value="nota_credito">Nota de Crédito</option>
                    <option value="nota_debito">Nota de Débito</option>
                </select>
            </div>
            <div class="form-group" style="grid-column:1/-1;"><label>CAI *</label><input type="text" name="cai" required placeholder="A1B2C3-D4E5F6-..." style="font-family:monospace;"></div>
            <div class="form-group"><label>Resolución SAR</label><input type="text" name="resolucion"></div>
            <div class="form-group"><label>Establecimiento *</label><input type="text" name="establecimiento" required value="000" maxlength="3"></div>
            <div class="form-group"><label>Punto de emisión *</label><input type="text" name="punto_emision" required value="001" maxlength="3"></div>
            <div class="form-group"><label>Tipo documento *</label><input type="text" name="tipo_documento" required value="01" maxlength="2"></div>
            <div class="form-group"><label>Rango inicial *</label><input type="number" name="rango_inicial" required value="1"></div>
            <div class="form-group"><label>Rango final *</label><input type="number" name="rango_final" required value="10000"></div>
            <div class="form-group"><label>Próximo número (al cargar)</label><input type="number" name="next_number"></div>
            <div class="form-group"><label>Fecha límite emisión *</label><input type="date" name="fecha_limite" required></div>
            <div class="form-group"><label><input type="checkbox" name="active" checked> Autorización activa</label></div>
        </div>
        <button type="submit" class="btn-admin">Guardar autorización</button>
    </form>
</div>

<div class="card">
    <h3>Autorizaciones registradas</h3>
    <div class="table-wrap">
    <table class="table">
        <thead><tr>
            <th>Tipo</th><th>CAI</th><th>Rango</th><th>Consumidos</th><th>Próximo</th><th>Caduca</th><th>Estado</th>
        </tr></thead>
        <tbody>
        <?php foreach ($auths as $a):
            $used = (int)$a['next_number'] - (int)$a['rango_inicial'];
            $total = (int)$a['rango_final'] - (int)$a['rango_inicial'] + 1;
            $pct = $total > 0 ? ($used / $total) * 100 : 0;
            $expSoon = strtotime($a['fecha_limite']) - time() < 30*86400;
        ?>
            <tr>
                <td><strong><?= e($a['document_type']) ?></strong></td>
                <td><code style="font-size:.75rem;"><?= e($a['cai']) ?></code></td>
                <td><?= sprintf('%s-%s-%s-%08d', $a['establecimiento'], $a['punto_emision'], $a['tipo_documento'], $a['rango_inicial']) ?><br>
                    <small>al <?= sprintf('%08d', $a['rango_final']) ?></small>
                </td>
                <td><?= $used ?> / <?= $total ?>
                    <div style="height:4px;background:var(--bg);border-radius:2px;margin-top:4px;overflow:hidden;">
                        <div style="height:100%;width:<?= $pct ?>%;background:<?= $pct > 80 ? '#dc3545' : '#28a745' ?>;"></div>
                    </div>
                </td>
                <td><strong><?= sprintf('%08d', $a['next_number']) ?></strong></td>
                <td><?= dt($a['fecha_limite'], 'd/m/Y') ?><?= $expSoon ? ' <span class="badge badge-warning">próximo</span>' : '' ?></td>
                <td><?= $a['active'] ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($auths)): ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted);">No hay autorizaciones registradas. <strong>Debes añadir una para poder facturar.</strong></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<style>.hidden { display: none; }</style>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
