<?php $pageTitle = 'Mapa de calor de horas'; require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$days = ['','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$max = 0; foreach ($grid as $row) foreach ($row as $v) $max = max($max, $v);
?>

<div class="page-header"><div class="breadcrumb">Últimos 90 días</div></div>

<div class="card">
    <p style="color:var(--muted);margin-bottom:20px;">Cuanto más oscura la celda, más citas en esa franja. Identifica horas valle para promociones específicas.</p>
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr>
            <th style="padding:8px;text-align:left;">Día</th>
            <?php for ($h = 8; $h <= 21; $h++): ?>
                <th style="padding:8px;font-size:.8rem;color:var(--muted);"><?= sprintf('%02d:00', $h) ?></th>
            <?php endfor; ?>
        </tr></thead>
        <tbody>
        <?php for ($d = 2; $d <= 7; $d++): ?>
            <tr>
                <td style="padding:8px;font-weight:600;"><?= $days[$d] ?></td>
                <?php for ($h = 8; $h <= 21; $h++):
                    $v = $grid[$d][$h] ?? 0;
                    $alpha = $max > 0 ? ($v / $max) : 0;
                    $bg = "rgba(107,138,122," . round($alpha, 2) . ")";
                ?>
                    <td style="padding:14px 6px;text-align:center;background:<?= $bg ?>;color:<?= $alpha > 0.5 ? '#fff' : 'inherit' ?>;border:1px solid var(--border);font-weight:600;">
                        <?= $v > 0 ? $v : '·' ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
        <!-- Domingo -->
        <tr>
            <td style="padding:8px;font-weight:600;"><?= $days[1] ?></td>
            <?php for ($h = 8; $h <= 21; $h++):
                $v = $grid[1][$h] ?? 0;
                $alpha = $max > 0 ? ($v / $max) : 0;
                $bg = "rgba(107,138,122," . round($alpha, 2) . ")";
            ?>
                <td style="padding:14px 6px;text-align:center;background:<?= $bg ?>;color:<?= $alpha > 0.5 ? '#fff' : 'inherit' ?>;border:1px solid var(--border);">
                    <?= $v > 0 ? $v : '·' ?>
                </td>
            <?php endfor; ?>
        </tr>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
