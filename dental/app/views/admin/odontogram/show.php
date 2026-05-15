<?php
/** @var array $patient */ /** @var array $odontogram */ /** @var array $history */ /** @var array $statuses */
$_scripts = ['/assets/js/odontogram.js'];
$labels = [
    'healthy'=>'Sana','caries'=>'Caries','filled'=>'Restaurada','crown'=>'Corona',
    'root_canal'=>'Endodoncia','extracted'=>'Extraída','missing'=>'Ausente',
    'implant'=>'Implante','bridge'=>'Puente','sealant'=>'Sellante',
    'fractured'=>'Fracturada','to_extract'=>'Por extraer',
];
?>
<div class="page-header">
    <div>
        <h1>Odontograma — <?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
        <p class="subtitle">Ficha <?= e($patient['code']) ?> · Notación FDI</p>
    </div>
    <a href="<?= url('/admin/pacientes/'.$patient['id']) ?>" class="btn">← Volver al paciente</a>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap:1rem;">
    <div class="card">
        <div class="card-header"><h2>Dentición permanente</h2></div>
        <div class="card-body">
            <div class="odontogram" id="odontogram-root" data-patient-id="<?= (int)$patient['id'] ?>">
                <?php foreach ($odontogram['permanent'] as $row): ?>
                    <div class="odontogram-row">
                        <?php foreach ($row as $tooth): ?>
                            <div class="tooth status-<?= e($tooth['status']) ?>"
                                 data-code="<?= e($tooth['tooth_code']) ?>"
                                 data-status="<?= e($tooth['status']) ?>"
                                 data-dentition="permanent"
                                 data-notes="<?= e($tooth['notes'] ?? '') ?>"
                                 title="<?= e($labels[$tooth['status']] ?? $tooth['status']) ?>">
                                <span class="code"><?= e($tooth['tooth_code']) ?></span>
                                <span class="ico">⌬</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <details class="mt-3">
                <summary style="cursor:pointer;font-weight:600;">Dentición decidua (infantil)</summary>
                <div class="odontogram mt-2">
                    <?php foreach ($odontogram['deciduous'] as $row): ?>
                        <div class="odontogram-row">
                            <?php foreach ($row as $tooth): ?>
                                <div class="tooth status-<?= e($tooth['status']) ?>"
                                     data-code="<?= e($tooth['tooth_code']) ?>"
                                     data-status="<?= e($tooth['status']) ?>"
                                     data-dentition="deciduous"
                                     data-notes="<?= e($tooth['notes'] ?? '') ?>">
                                    <span class="code"><?= e($tooth['tooth_code']) ?></span>
                                    <span class="ico">⌬</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </details>

            <div class="odontogram-legend">
                <?php foreach ($labels as $k => $v): ?>
                    <div class="leg-item">
                        <span class="swatch tooth status-<?= e($k) ?>" style="width:18px;height:18px;border-radius:4px;padding:0;"></span>
                        <?= e($v) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="card" id="tooth-detail" style="display:none;">
            <div class="card-header"><h2>Pieza <span id="tooth-detail-code"></span></h2></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Estado clínico</label>
                    <select id="tooth-status-select">
                        <?php foreach ($labels as $k => $v): ?>
                            <option value="<?= e($k) ?>"><?= e($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nota</label>
                    <textarea id="tooth-note-input" rows="3" placeholder="Observaciones..."></textarea>
                </div>
                <button id="save-tooth" class="btn btn-primary" style="width:100%;">Guardar cambios</button>
            </div>
        </div>

        <p class="text-muted text-center mt-2" id="hint" style="font-size:0.85rem;">
            Haz clic en una pieza para editarla.
        </p>

        <div class="card mt-3">
            <div class="card-header"><h2>Histórico de cambios</h2></div>
            <div class="card-body" style="max-height:400px;overflow-y:auto;font-size:0.82rem;">
                <?php if (!$history): ?>
                    <p class="text-muted text-center">Sin cambios registrados</p>
                <?php else: foreach ($history as $h): ?>
                    <div style="padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                        <strong>Pieza <?= e($h['tooth_code']) ?>:</strong>
                        <?= e($h['previous_status'] ?: '—') ?> → <strong><?= e($h['new_status']) ?></strong><br>
                        <span class="text-muted"><?= e(format_date($h['changed_at'], 'd/m/Y H:i')) ?> · <?= e($h['changed_by_name'] ?: 'sistema') ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
