<?php /** @var array $data */ /** @var int $months */ ?>
<div class="page-header">
    <div><h1>Pacientes para recall</h1>
        <p class="subtitle">Pacientes sin visitas en los últimos <?= (int)$months ?> meses</p></div>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="flex gap-1 items-center">
            Meses sin venir: <input type="number" name="months" value="<?= $months ?>" min="1" max="36">
            <button class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>
<div class="card">
    <table>
        <thead><tr><th>Código</th><th>Paciente</th><th>Última visita</th><th>Contacto</th><th></th></tr></thead>
        <tbody>
            <?php if (!$data): ?><tr><td colspan="5" class="text-center text-muted" style="padding:1.5rem;">✓ Todos los pacientes están al día</td></tr><?php endif; ?>
            <?php foreach ($data as $p): ?>
                <tr>
                    <td><?= e($p['code']) ?></td>
                    <td><?= e($p['first_name'].' '.$p['last_name']) ?></td>
                    <td><?= e(format_date($p['last_visit'])) ?: '—' ?></td>
                    <td>
                        <?php if ($p['mobile']): ?>📱 <?= e($p['mobile']) ?> <?php endif; ?>
                        <?php if ($p['email']): ?>📧 <?= e($p['email']) ?><?php endif; ?>
                    </td>
                    <td><a href="<?= url('/admin/pacientes/'.$p['id']) ?>" class="btn btn-sm">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
