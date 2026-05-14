<?php $pageTitle = 'Agenda'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div>
        <h1 style="display:none;">Agenda</h1>
        <div class="breadcrumb">Vista de calendario con todas las citas</div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="<?= url('/admin/citas') ?>" class="btn-admin btn-outline">Vista lista</a>
        <a href="<?= url('/admin/citas/nueva') ?>" class="btn-admin">+ Nueva cita</a>
    </div>
</div>

<div class="card">
    <div id="calendar"></div>
</div>

<div class="card" style="margin-top:20px;">
    <h3 style="margin-bottom:12px;">Leyenda</h3>
    <div style="display:flex;gap:18px;flex-wrap:wrap;font-size:.88rem;">
        <span><span style="display:inline-block;width:14px;height:14px;background:#ffc107;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>Pendiente</span>
        <span><span style="display:inline-block;width:14px;height:14px;background:#17a2b8;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>Confirmada</span>
        <span><span style="display:inline-block;width:14px;height:14px;background:#28a745;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>Completada</span>
        <span><span style="display:inline-block;width:14px;height:14px;background:#dc3545;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>Cancelada</span>
        <span><span style="display:inline-block;width:14px;height:14px;background:#6c757d;border-radius:3px;vertical-align:middle;margin-right:6px;"></span>No-show</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'es',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        height: 'auto',
        nowIndicator: true,
        weekends: true,
        editable: true,
        eventSources: [{
            url: '<?= url('/admin/api/citas') ?>',
            method: 'GET'
        }],
        eventClick: function(info) {
            window.location.href = '<?= url('/admin/citas/') ?>' + info.event.id + '/editar';
        },
        dateClick: function(info) {
            const d = info.dateStr.split('T');
            const date = d[0];
            const time = d[1] ? d[1].substring(0,5) : '10:00';
            window.location.href = '<?= url('/admin/citas/nueva') ?>?date=' + date + '&time=' + time;
        }
    });
    calendar.render();
});
</script>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
