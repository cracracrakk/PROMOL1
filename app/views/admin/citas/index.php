<?php $pageTitle = 'Citas'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div><div class="breadcrumb">Listado de citas</div></div>
    <div style="display:flex;gap:10px;">
        <a href="<?= url('/admin/citas/agenda') ?>" class="btn-admin btn-outline">Vista calendario</a>
        <a href="<?= url('/admin/citas/nueva') ?>" class="btn-admin">+ Nueva cita</a>
    </div>
</div>

<div class="filters">
    <input type="text" class="search-bar" placeholder="Buscar por cliente, servicio..." data-filter-table="citasTable">
    <select onchange="window.location='?status=' + this.value">
        <option value="">Todos los estados</option>
        <?php foreach (['pendiente','confirmada','en_curso','completada','cancelada','no_show'] as $st): ?>
            <option value="<?= $st ?>" <?= $filter === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table" id="citasTable">
        <thead><tr>
            <th>Fecha/Hora</th><th>Cliente</th><th>Servicio</th><th>Terapeuta</th><th>Estado</th><th>Origen</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($appointments as $a):
            $statusClass = ['pendiente'=>'badge-pending','confirmada'=>'badge-confirmed','en_curso'=>'badge-info','completada'=>'badge-completed','cancelada'=>'badge-cancelled','no_show'=>'badge-danger'][$a['status']] ?? 'badge-secondary';
        ?>
            <tr>
                <td><?= dt($a['starts_at'], 'd/m/Y H:i') ?></td>
                <td><strong><?= e(trim($a['first_name'] . ' ' . $a['last_name'])) ?></strong><br><small style="color:var(--muted);"><?= e($a['phone']) ?></small></td>
                <td><?= e($a['service_name']) ?></td>
                <td><?= e($a['therapist_name'] ?? '—') ?></td>
                <td><span class="badge <?= $statusClass ?>"><?= e(str_replace('_',' ',$a['status'])) ?></span></td>
                <td><small><?= e($a['source']) ?></small></td>
                <td>
                    <a href="<?= url('/admin/citas/' . $a['id'] . '/editar') ?>" class="btn-icon" title="Editar">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
