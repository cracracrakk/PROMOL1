<?php $pageTitle = 'Forecast de ingresos'; require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$total = 0; foreach ($rows as $r) $total += $r['expected'];
?>

<div class="page-header"><div class="breadcrumb">Ingresos previstos próximos 30 días</div></div>

<div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Total previsto</div><div class="kpi-value"><?= money($total) ?></div></div>
    <div class="kpi"><div class="kpi-label">Días con citas</div><div class="kpi-value"><?= count($rows) ?></div></div>
    <div class="kpi"><div class="kpi-label">Promedio diario</div><div class="kpi-value"><?= money(count($rows) ? $total / count($rows) : 0) ?></div></div>
</div>

<div class="card">
    <h3>Detalle por día</h3>
    <canvas id="forecastChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const data = <?= json_encode($rows) ?>;
new Chart(document.getElementById('forecastChart'), {
    type: 'bar',
    data: {
        labels: data.map(d => new Date(d.day).toLocaleDateString('es', {day:'numeric',month:'short'})),
        datasets: [{
            label: 'Previsto',
            data: data.map(d => parseFloat(d.expected)),
            backgroundColor: 'rgba(201, 169, 110, 0.7)',
            borderColor: '#c9a96e',
            borderWidth: 1,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
