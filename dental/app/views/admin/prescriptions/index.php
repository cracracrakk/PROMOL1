<?php /** @var array $patient */ /** @var array $list */ ?>
<div class="page-header">
    <div><h1>Recetas — <?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
        <p class="subtitle"><?= count($list) ?> recetas emitidas</p></div>
    <div class="flex gap-1">
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/recetas/nueva') ?>" class="btn btn-primary">+ Nueva receta</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id']) ?>" class="btn">← Volver</a>
    </div>
</div>

<div class="card">
    <table>
        <thead><tr><th>Código</th><th>Fecha</th><th>Odontólogo</th><th>Items</th><th></th></tr></thead>
        <tbody>
            <?php if (!$list): ?><tr><td colspan="5" class="text-center text-muted" style="padding:2rem;">Sin recetas</td></tr><?php endif; ?>
            <?php foreach ($list as $r): ?>
                <tr>
                    <td><?= e($r['code']) ?></td>
                    <td><?= e(format_date($r['issue_date'])) ?></td>
                    <td><?= e($r['dentist_name']) ?></td>
                    <td><?= (int)$r['items_count'] ?></td>
                    <td>
                        <a href="<?= url('/admin/recetas/'.$r['id']) ?>" class="btn btn-sm">Ver</a>
                        <a href="<?= url('/admin/recetas/'.$r['id'].'/imprimir') ?>" target="_blank" class="btn btn-sm">Imprimir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
