<?php $pageTitle = 'Inteligencia de negocio'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Reportes avanzados</div>
</div>

<div class="kpi-grid">
    <a href="<?= url('/admin/reportes/heatmap') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">🔥 Mapa de calor</div>
        <div style="font-size:1.1rem;margin-top:8px;">Horas más ocupadas y huecos vacíos</div>
    </a>
    <a href="<?= url('/admin/reportes/retencion') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">🔁 Retención</div>
        <div style="font-size:1.1rem;margin-top:8px;">% clientes que vuelven en 30/60/90 días</div>
    </a>
    <a href="<?= url('/admin/reportes/en-riesgo') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">⚠️ En riesgo de fuga</div>
        <div style="font-size:1.1rem;margin-top:8px;">Clientes que no vienen hace 60+ días</div>
    </a>
    <a href="<?= url('/admin/reportes/forecast') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">📈 Forecast</div>
        <div style="font-size:1.1rem;margin-top:8px;">Ingresos previstos próximos 30 días</div>
    </a>
    <a href="<?= url('/admin/reportes/terapeutas') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">👥 Comparativa terapeutas</div>
        <div style="font-size:1.1rem;margin-top:8px;">Sesiones, ingresos y NPS por persona</div>
    </a>
    <a href="<?= url('/admin/reportes') ?>" class="kpi" style="text-decoration:none;color:inherit;">
        <div class="kpi-label">🧾 Libro de ventas SAR</div>
        <div style="font-size:1.1rem;margin-top:8px;">Detalle fiscal exportable</div>
    </a>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
