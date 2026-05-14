<?php $pageTitle = 'Dashboard'; require dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<?php if (!empty($sarAlerts)): ?>
    <?php foreach ($sarAlerts as $alert): ?>
        <div class="alert alert-warning">⚠️ <?= e($alert) ?> <a href="<?= url('/admin/sistema/sar') ?>">Gestionar CAI</a></div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi">
        <div class="kpi-label">Citas Hoy</div>
        <div class="kpi-value"><?= (int)$kpis['today_appointments'] ?></div>
        <div class="kpi-icon">📅</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Ingresos Hoy</div>
        <div class="kpi-value"><?= money($kpis['today_income']) ?></div>
        <div class="kpi-icon">💰</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Ingresos del Mes</div>
        <div class="kpi-value"><?= money($kpis['month_income']) ?></div>
        <?php if ($growth != 0): ?>
            <div class="kpi-change <?= $growth >= 0 ? 'up' : 'down' ?>">
                <?= $growth >= 0 ? '▲' : '▼' ?> <?= number_format(abs($growth), 1) ?>% vs. mes anterior
            </div>
        <?php endif; ?>
        <div class="kpi-icon">📈</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Citas Pendientes</div>
        <div class="kpi-value"><?= (int)$kpis['pending_appointments'] ?></div>
        <div class="kpi-icon">⏳</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Clientes Totales</div>
        <div class="kpi-value"><?= (int)$kpis['total_customers'] ?></div>
        <div class="kpi-icon">👥</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Stock Bajo</div>
        <div class="kpi-value" style="<?= $kpis['low_stock'] > 0 ? 'color:#dc3545;' : '' ?>"><?= (int)$kpis['low_stock'] ?></div>
        <div class="kpi-icon">📦</div>
    </div>
</div>

<div class="card-grid">
    <!-- Gráfica ingresos 14 días -->
    <div class="card">
        <div class="card-header">
            <h2>Ingresos últimos 14 días</h2>
        </div>
        <canvas id="incomeChart" height="100"></canvas>
    </div>

    <!-- Top servicios del mes -->
    <div class="card">
        <div class="card-header">
            <h3>Top servicios del mes</h3>
        </div>
        <?php if (empty($topServices)): ?>
            <p style="color:var(--muted);">Sin datos este mes aún.</p>
        <?php else: ?>
            <?php foreach ($topServices as $ts):
                $max = $topServices[0]['count_apps'];
                $pct = $max > 0 ? (($ts['count_apps'] / $max) * 100) : 0;
            ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;font-size:.9rem;margin-bottom:4px;">
                        <span><?= e($ts['name']) ?></span>
                        <strong><?= (int)$ts['count_apps'] ?></strong>
                    </div>
                    <div style="height:6px;background:var(--bg);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:<?= $pct ?>%;background:var(--brand);"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Citas de hoy -->
<div class="card">
    <div class="card-header">
        <h2>Agenda de hoy</h2>
        <a href="<?= url('/admin/citas/agenda') ?>" class="btn-admin btn-sm btn-outline">Ver agenda completa</a>
    </div>
    <?php if (empty($todayAppointments)): ?>
        <p style="color:var(--muted);text-align:center;padding:30px;">No hay citas para hoy.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table class="table">
            <thead><tr>
                <th>Hora</th><th>Cliente</th><th>Servicio</th><th>Terapeuta</th><th>Estado</th><th></th>
            </tr></thead>
            <tbody>
            <?php foreach ($todayAppointments as $a):
                $statusClass = [
                    'pendiente'  => 'badge-pending',
                    'confirmada' => 'badge-confirmed',
                    'en_curso'   => 'badge-info',
                    'completada' => 'badge-completed',
                    'cancelada'  => 'badge-cancelled',
                    'no_show'    => 'badge-danger',
                ][$a['status']] ?? 'badge-secondary';
            ?>
                <tr>
                    <td><strong><?= dt($a['starts_at'], 'H:i') ?></strong> – <?= dt($a['ends_at'], 'H:i') ?></td>
                    <td><?= e(trim($a['first_name'] . ' ' . $a['last_name'])) ?><br><small style="color:var(--muted);"><?= e($a['phone']) ?></small></td>
                    <td><?= e($a['service_name']) ?></td>
                    <td><?= e($a['therapist_name'] ?? '—') ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= e(str_replace('_', ' ', $a['status'])) ?></span></td>
                    <td><a href="<?= url('/admin/citas/' . $a['id'] . '/editar') ?>" class="btn-admin btn-sm btn-outline">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($lowStockProducts)): ?>
<div class="card">
    <div class="card-header">
        <h3>⚠️ Productos con stock bajo</h3>
        <a href="<?= url('/admin/inventario') ?>" class="btn-admin btn-sm btn-outline">Ver inventario</a>
    </div>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Producto</th><th>SKU</th><th>Stock actual</th><th>Mínimo</th></tr></thead>
        <tbody>
        <?php foreach ($lowStockProducts as $p): ?>
            <tr>
                <td><strong><?= e($p['name']) ?></strong></td>
                <td><code><?= e($p['sku']) ?></code></td>
                <td><span class="badge badge-danger"><?= (int)$p['stock'] ?> <?= e($p['unit']) ?></span></td>
                <td><?= (int)$p['stock_min'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const incomeData = <?= json_encode($incomeChart) ?>;
new Chart(document.getElementById('incomeChart'), {
    type: 'line',
    data: {
        labels: incomeData.map(d => new Date(d.date).toLocaleDateString('es', {day:'numeric', month:'short'})),
        datasets: [{
            label: 'Ingresos',
            data: incomeData.map(d => d.total),
            borderColor: '<?= e(setting('color_primary', '#6b8a7a')) ?>',
            backgroundColor: 'rgba(107, 138, 122, 0.15)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            pointBackgroundColor: '<?= e(setting('color_accent', '#c9a96e')) ?>',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php require dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
