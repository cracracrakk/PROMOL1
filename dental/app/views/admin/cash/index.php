<?php /** @var ?array $current */ /** @var array $history */ /** @var array $expenses */ /** @var ?array $movements */ /** @var float $expected */ ?>
<div class="page-header">
    <div><h1>Caja diaria</h1>
        <p class="subtitle"><?= $current ? '🟢 Caja abierta desde ' . format_date($current['opened_at'], 'd/m H:i') : '🔴 Sin caja abierta' ?></p></div>
</div>

<?php if (!$current): ?>
<div class="card mb-3">
    <div class="card-header"><h2>Abrir caja</h2></div>
    <div class="card-body">
        <form method="post" action="<?= url('/admin/caja/abrir') ?>" class="grid grid-3 items-center">
            <?= csrf_field() ?>
            <div class="form-group"><label>Monto inicial en efectivo</label>
                <input type="number" step="0.01" name="opening_amount" value="0" required></div>
            <div class="form-group"><label>Notas</label>
                <input type="text" name="notes"></div>
            <div style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-primary">Abrir caja</button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>

<div class="grid grid-4 mb-3">
    <div class="stat">
        <div class="label">Apertura</div>
        <div class="value"><?= money((float)$current['opening_amount']) ?></div>
    </div>
    <div class="stat">
        <div class="label">Pagos en efectivo</div>
        <div class="value"><?= money((float)array_sum(array_column($movements['payments'] ?? [], 'amount'))) ?></div>
        <div class="meta"><?= count($movements['payments'] ?? []) ?> movimientos</div>
    </div>
    <div class="stat">
        <div class="label">Gastos</div>
        <div class="value"><?= money((float)array_sum(array_column($movements['expenses'] ?? [], 'amount'))) ?></div>
        <div class="meta"><?= count($movements['expenses'] ?? []) ?> registros</div>
    </div>
    <div class="stat" style="border:2px solid var(--primary);">
        <div class="label">Esperado en caja</div>
        <div class="value"><?= money($expected) ?></div>
    </div>
</div>

<div class="grid grid-2 mb-3">
    <div class="card">
        <div class="card-header"><h2>Cerrar caja</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/caja/cerrar') ?>">
                <?= csrf_field() ?>
                <div class="form-group"><label>Conteo de efectivo (real)</label>
                    <input type="number" step="0.01" name="closing_amount" required></div>
                <div class="form-group"><label>Notas del cierre</label>
                    <textarea name="notes" rows="2"></textarea></div>
                <button type="submit" class="btn btn-danger" style="width:100%;"
                        data-confirm="¿Confirmas el cierre de caja? No podrás reabrir esta sesión.">
                    Cerrar caja
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Registrar gasto</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/gastos/nuevo') ?>">
                <?= csrf_field() ?>
                <div class="form-group"><label>Descripción</label>
                    <input type="text" name="description" required></div>
                <div class="grid grid-2">
                    <div class="form-group"><label>Categoría</label>
                        <input type="text" name="category" list="exp-cats">
                        <datalist id="exp-cats">
                            <option>Insumos</option><option>Servicios</option>
                            <option>Laboratorio</option><option>Salarios</option><option>Limpieza</option>
                        </datalist></div>
                    <div class="form-group"><label>Método</label>
                        <select name="method">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </select></div>
                </div>
                <div class="form-group"><label>Monto</label>
                    <input type="number" step="0.01" name="amount" required></div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Registrar gasto</button>
            </form>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h2>Movimientos de la sesión</h2></div>
    <table>
        <thead><tr><th>Hora</th><th>Tipo</th><th>Descripción</th><th>Método</th><th class="text-right">Monto</th></tr></thead>
        <tbody>
            <?php
            $all = array_merge($movements['payments'] ?? [], $movements['expenses'] ?? []);
            usort($all, fn($a,$b) => strcmp($b['at'], $a['at']));
            if (!$all): ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:1.5rem;">Sin movimientos</td></tr>
            <?php endif; foreach ($all as $m): ?>
                <tr style="<?= $m['kind'] === 'expense' ? 'background:#fef2f2;' : '' ?>">
                    <td><?= e(format_date($m['at'], 'H:i')) ?></td>
                    <td><?= $m['kind'] === 'payment' ? '↓ Ingreso' : '↑ Gasto' ?></td>
                    <td><?= e($m['description']) ?></td>
                    <td><?= e($m['method']) ?></td>
                    <td class="text-right" style="color:<?= $m['kind'] === 'expense' ? 'var(--danger)' : 'var(--success)' ?>;">
                        <?= $m['kind'] === 'expense' ? '-' : '+' ?><?= money((float)$m['amount']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<div class="card">
    <div class="card-header"><h2>Historial de cierres</h2></div>
    <table>
        <thead><tr><th>Fecha</th><th>Apertura</th><th>Cierre</th><th>Esperado</th><th>Contado</th><th>Diferencia</th><th>Estado</th></tr></thead>
        <tbody>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= e(format_date($h['opened_at'], 'd/m/Y')) ?></td>
                    <td><?= e(format_date($h['opened_at'], 'H:i')) ?></td>
                    <td><?= e(format_date($h['closed_at'], 'H:i')) ?: '—' ?></td>
                    <td><?= $h['expected_cash'] !== null ? money((float)$h['expected_cash']) : '—' ?></td>
                    <td><?= $h['closing_amount'] !== null ? money((float)$h['closing_amount']) : '—' ?></td>
                    <td style="color:<?= ($h['difference'] ?? 0) < 0 ? 'var(--danger)' : 'var(--success)' ?>;">
                        <?= $h['difference'] !== null ? money((float)$h['difference']) : '—' ?>
                    </td>
                    <td><?= $h['closed_at'] ? '<span class="badge badge-success">cerrada</span>' : '<span class="badge badge-warning">abierta</span>' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
