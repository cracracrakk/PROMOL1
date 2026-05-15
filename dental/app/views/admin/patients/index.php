<?php /** @var string $q */ /** @var array $result */ ?>
<div class="page-header">
    <div>
        <h1>Pacientes</h1>
        <p class="subtitle"><?= (int)$result['total'] ?> registrados</p>
    </div>
    <a href="<?= url('/admin/pacientes/nuevo') ?>" class="btn btn-primary">+ Nuevo paciente</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="search-bar">
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, código, documento, teléfono...">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if ($q): ?><a href="<?= url('/admin/pacientes') ?>" class="btn">Limpiar</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Código</th><th>Nombre</th><th>Documento</th><th>Teléfono</th><th>Edad</th><th>Email</th><th></th></tr>
            </thead>
            <tbody>
                <?php if (!$result['data']): ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding:2rem;">Sin resultados</td></tr>
                <?php endif; foreach ($result['data'] as $p): ?>
                    <tr>
                        <td><a href="<?= url('/admin/pacientes/'.$p['id']) ?>"><?= e($p['code']) ?></a></td>
                        <td>
                            <strong><?= e($p['last_name'].', '.$p['first_name']) ?></strong>
                            <?php if (!$p['is_active']): ?><span class="badge badge-muted">inactivo</span><?php endif; ?>
                        </td>
                        <td><?= e($p['document_number'] ?: '—') ?></td>
                        <td><?= e($p['mobile'] ?: $p['phone'] ?: '—') ?></td>
                        <td><?= e(age_from($p['birth_date']) ?: '—') ?></td>
                        <td class="text-muted"><?= e($p['email'] ?: '—') ?></td>
                        <td>
                            <a href="<?= url('/admin/pacientes/'.$p['id']) ?>" class="btn btn-sm">Ver</a>
                            <a href="<?= url('/admin/pacientes/'.$p['id'].'/odontograma') ?>" class="btn btn-sm">Odontograma</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($result['last_page'] > 1): ?>
<div class="mt-2 flex gap-1 justify-between items-center">
    <span class="text-muted">Página <?= (int)$result['page'] ?> de <?= (int)$result['last_page'] ?></span>
    <div class="flex gap-1">
        <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
            <a href="?<?= http_build_query(['q'=>$q,'page'=>$i]) ?>"
               class="btn btn-sm <?= $i === $result['page'] ? 'btn-primary' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>
