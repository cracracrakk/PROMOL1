<?php /** @var string $date */ /** @var array $appointments */ /** @var array $dentists */ ?>
<div class="page-header">
    <div>
        <h1>Agenda</h1>
        <p class="subtitle"><?= e(format_date($date, 'l, d \\d\\e F Y')) ?> · <?= count($appointments) ?> citas</p>
    </div>
    <div class="flex gap-1">
        <a href="<?= url('/admin/citas/nueva') ?>" class="btn btn-primary">+ Nueva cita</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="flex gap-2 items-center">
            <a href="?date=<?= date('Y-m-d', strtotime($date.' -1 day')) ?>" class="btn">←</a>
            <input type="date" name="date" value="<?= e($date) ?>" onchange="this.form.submit()">
            <a href="?date=<?= date('Y-m-d', strtotime($date.' +1 day')) ?>" class="btn">→</a>
            <a href="?date=<?= date('Y-m-d') ?>" class="btn">Hoy</a>
        </form>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr><th>Hora</th><th>Paciente</th><th>Odontólogo</th><th>Tratamiento</th><th>Sillón</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (!$appointments): ?>
            <tr><td colspan="7" class="text-center text-muted" style="padding:2.5rem;">No hay citas para este día.<br>
                <a href="<?= url('/admin/citas/nueva') ?>" class="btn btn-primary mt-2">+ Crear cita</a></td></tr>
        <?php endif; foreach ($appointments as $a): ?>
            <tr>
                <td>
                    <strong><?= e(date('H:i', strtotime($a['starts_at']))) ?></strong>
                    <div class="text-muted" style="font-size:0.78rem;">→ <?= e(date('H:i', strtotime($a['ends_at']))) ?></div>
                </td>
                <td>
                    <a href="<?= url('/admin/pacientes/'.$a['patient_id']) ?>"><strong><?= e($a['patient_name']) ?></strong></a>
                </td>
                <td><?= e($a['dentist_name']) ?></td>
                <td><?= e($a['treatment_name'] ?: $a['reason'] ?: '—') ?></td>
                <td>
                    <?php if ($a['room_name']): ?>
                        <span class="badge" style="background:<?= e($a['room_color']) ?>20;color:<?= e($a['room_color']) ?>;border:1px solid <?= e($a['room_color']) ?>;">
                            <?= e($a['room_name']) ?>
                        </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <?php $st = $a['status'];
                          $cls = ['confirmed'=>'badge-success','scheduled'=>'badge-info','completed'=>'badge-muted',
                                  'cancelled'=>'badge-danger','no_show'=>'badge-warning','in_progress'=>'badge-warning'][$st] ?? 'badge-muted'; ?>
                    <span class="badge <?= $cls ?>"><?= e($st) ?></span>
                </td>
                <td>
                    <form method="post" action="<?= url('/admin/citas/'.$a['id'].'/estado') ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <select name="status" onchange="this.form.submit()" style="font-size:0.78rem;padding:0.25rem;">
                            <option>cambiar...</option>
                            <option value="confirmed">Confirmar</option>
                            <option value="in_progress">En curso</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelar</option>
                            <option value="no_show">No se presentó</option>
                        </select>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
