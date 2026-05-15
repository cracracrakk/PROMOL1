<?php /** @var array $patient */ /** @var array $documents */ /** @var ?string $type */ ?>
<div class="page-header">
    <div>
        <h1>Documentos — <?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
        <p class="subtitle">Rayos X, fotos y archivos del paciente</p>
    </div>
    <a href="<?= url('/admin/pacientes/'.$patient['id']) ?>" class="btn">← Volver</a>
</div>

<div class="card mb-3">
    <div class="card-body flex gap-1">
        <a href="?" class="btn btn-sm <?= !$type ? 'btn-primary' : '' ?>">Todos</a>
        <a href="?type=xray"     class="btn btn-sm <?= $type === 'xray' ? 'btn-primary' : '' ?>">Rayos X</a>
        <a href="?type=photo"    class="btn btn-sm <?= $type === 'photo' ? 'btn-primary' : '' ?>">Fotos</a>
        <a href="?type=consent"  class="btn btn-sm <?= $type === 'consent' ? 'btn-primary' : '' ?>">Consentimientos</a>
        <a href="?type=document" class="btn btn-sm <?= $type === 'document' ? 'btn-primary' : '' ?>">Otros</a>
    </div>
</div>

<div class="grid grid-2 mb-3">
    <div class="card">
        <div class="card-header"><h2>Subir nuevo archivo</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/pacientes/'.$patient['id'].'/documentos/subir') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group"><label>Tipo</label>
                    <select name="type">
                        <option value="xray">Radiografía</option>
                        <option value="photo">Foto intraoral</option>
                        <option value="consent">Consentimiento</option>
                        <option value="document">Otro documento</option>
                    </select></div>
                <div class="form-group"><label>Título</label>
                    <input type="text" name="title" required placeholder="Ej: Panorámica inicial"></div>
                <div class="form-group"><label>Pieza relacionada (opcional)</label>
                    <input type="text" name="tooth_code" placeholder="Ej: 16"></div>
                <div class="form-group"><label>Fecha</label>
                    <input type="date" name="taken_at" value="<?= date('Y-m-d') ?>"></div>
                <div class="form-group"><label>Descripción</label>
                    <textarea name="description" rows="2"></textarea></div>
                <div class="form-group"><label>Archivo (JPG, PNG, PDF — máx 10MB)</label>
                    <input type="file" name="file" required accept="image/*,application/pdf"></div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Subir</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Archivos (<?= count($documents) ?>)</h2></div>
        <div class="card-body" style="max-height:700px;overflow-y:auto;">
            <?php if (!$documents): ?>
                <p class="text-muted text-center">Sin documentos aún.</p>
            <?php else: foreach ($documents as $d): ?>
                <div style="display:flex;gap:12px;padding:0.75rem;border-bottom:1px solid var(--gray-100);">
                    <?php if (str_starts_with($d['mime'] ?? '', 'image/')): ?>
                        <a href="<?= url('/admin/documentos/'.$d['id'].'/ver') ?>" target="_blank">
                            <img src="<?= url('/admin/documentos/'.$d['id'].'/ver') ?>"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:6px;">
                        </a>
                    <?php else: ?>
                        <div style="width:80px;height:80px;background:var(--gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:2rem;">📄</div>
                    <?php endif; ?>
                    <div style="flex:1;">
                        <strong><?= e($d['title']) ?></strong>
                        <span class="badge badge-muted"><?= e($d['type']) ?></span>
                        <?php if ($d['tooth_code']): ?><span class="badge badge-info">Pieza <?= e($d['tooth_code']) ?></span><?php endif; ?>
                        <div class="text-muted" style="font-size:0.8rem;">
                            <?= e(format_date($d['taken_at'] ?: $d['created_at'])) ?> · <?= e($d['uploaded_by_name'] ?: '—') ?> · <?= number_format(($d['size_bytes'] ?? 0)/1024, 0) ?> KB
                        </div>
                        <?php if ($d['description']): ?><div class="text-muted" style="font-size:0.85rem;"><?= e($d['description']) ?></div><?php endif; ?>
                        <div class="flex gap-1 mt-1">
                            <a href="<?= url('/admin/documentos/'.$d['id'].'/ver') ?>" target="_blank" class="btn btn-sm">Ver</a>
                            <form method="post" action="<?= url('/admin/documentos/'.$d['id'].'/eliminar') ?>"
                                  style="display:inline;" data-confirm="¿Eliminar este documento?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-danger">×</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
