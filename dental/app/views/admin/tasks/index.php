<?php /** @var array $tasks */ /** @var array $users */ /** @var string $filter */ ?>
<div class="page-header">
    <div><h1>Tareas</h1>
        <p class="subtitle"><?= count($tasks) ?> tareas</p></div>
</div>
<div class="card mb-3"><div class="card-body flex gap-1">
    <a href="?filter=mine"    class="btn btn-sm <?= $filter==='mine'?'btn-primary':'' ?>">Mías</a>
    <a href="?filter=pending" class="btn btn-sm <?= $filter==='pending'?'btn-primary':'' ?>">Pendientes</a>
    <a href="?filter=all"     class="btn btn-sm <?= $filter==='all'?'btn-primary':'' ?>">Todas</a>
</div></div>

<div class="grid grid-2">
    <div>
        <?php foreach ($tasks as $t): ?>
            <div class="card mb-2" style="border-left:4px solid <?= $t['priority']==='high'?'var(--danger)':($t['priority']==='medium'?'var(--warning)':'var(--gray-300)') ?>;">
                <div class="card-body">
                    <div class="flex justify-between">
                        <strong><?= e($t['title']) ?></strong>
                        <span class="badge <?= ['open'=>'badge-info','in_progress'=>'badge-warning','done'=>'badge-success','cancelled'=>'badge-muted'][$t['status']] ?>"><?= e($t['status']) ?></span>
                    </div>
                    <?php if ($t['description']): ?><div class="text-muted mt-1"><?= e($t['description']) ?></div><?php endif; ?>
                    <div class="text-muted mt-1" style="font-size:0.82rem;">
                        <?php if ($t['due_date']): ?>📅 <?= e(format_date($t['due_date'])) ?><?php endif; ?>
                        <?php if ($t['assigned_name']): ?> · 👤 <?= e($t['assigned_name']) ?><?php endif; ?>
                        <?php if ($t['patient_name']): ?> · 🦷 <?= e($t['patient_name']) ?><?php endif; ?>
                    </div>
                    <div class="flex gap-1 mt-2">
                        <?php if ($t['status'] !== 'done'): ?>
                        <form method="post" action="<?= url('/admin/tareas/'.$t['id'].'/estado') ?>" style="display:inline;">
                            <?= csrf_field() ?><input type="hidden" name="status" value="done">
                            <button class="btn btn-sm btn-success">✓ Completar</button>
                        </form>
                        <?php endif; ?>
                        <form method="post" action="<?= url('/admin/tareas/'.$t['id'].'/eliminar') ?>" data-confirm="¿Eliminar?" style="display:inline;">
                            <?= csrf_field() ?><button class="btn btn-sm btn-danger">×</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$tasks): ?>
            <p class="text-muted text-center" style="padding:2rem;">Sin tareas</p>
        <?php endif; ?>
    </div>

    <div class="card" style="height:fit-content;">
        <div class="card-header"><h2>+ Nueva tarea</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/tareas/nueva') ?>">
                <?= csrf_field() ?>
                <div class="form-group"><label>Título *</label>
                    <input type="text" name="title" required></div>
                <div class="form-group"><label>Descripción</label>
                    <textarea name="description" rows="2"></textarea></div>
                <div class="grid grid-2">
                    <div class="form-group"><label>Asignar a</label>
                        <select name="assigned_to">
                            <option value="">—</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= (int)$u['id'] ?>"><?= e($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    <div class="form-group"><label>Prioridad</label>
                        <select name="priority">
                            <option value="low">Baja</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                        </select></div>
                </div>
                <div class="form-group"><label>Fecha límite</label>
                    <input type="date" name="due_date"></div>
                <button class="btn btn-primary" style="width:100%;">Crear tarea</button>
            </form>
        </div>
    </div>
</div>
