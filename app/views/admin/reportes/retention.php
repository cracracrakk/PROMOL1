<?php $pageTitle = 'Retención de clientes'; require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$base = (int)$row['base'];
$r30 = (int)$row['r30']; $r60 = (int)$row['r60']; $r90 = (int)$row['r90'];
$p30 = $base ? round(($r30 / $base) * 100) : 0;
$p60 = $base ? round(($r60 / $base) * 100) : 0;
$p90 = $base ? round(($r90 / $base) * 100) : 0;
?>

<div class="page-header"><div class="breadcrumb">Cohorte de hace 90-120 días</div></div>

<div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Clientes en cohorte</div><div class="kpi-value"><?= $base ?></div></div>
    <div class="kpi"><div class="kpi-label">Volvieron en 30 días</div><div class="kpi-value"><?= $p30 ?>%</div><div class="kpi-change"><?= $r30 ?> clientes</div></div>
    <div class="kpi"><div class="kpi-label">Volvieron en 60 días</div><div class="kpi-value"><?= $p60 ?>%</div><div class="kpi-change"><?= $r60 ?> clientes</div></div>
    <div class="kpi"><div class="kpi-label">Volvieron en 90 días</div><div class="kpi-value"><?= $p90 ?>%</div><div class="kpi-change"><?= $r90 ?> clientes</div></div>
</div>

<div class="card">
    <h3>¿Qué significa esto?</h3>
    <p style="color:var(--muted);margin-top:14px;line-height:1.7;">
        Tomamos a los clientes que vinieron hace entre 90 y 120 días, y vemos cuántos han vuelto en distintos plazos.
        Si tu retención a 60 días es <strong>≥ 40%</strong>, vas bien. Por debajo del 25% indica fuga: tu cron de win-back ya está activado.
    </p>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
