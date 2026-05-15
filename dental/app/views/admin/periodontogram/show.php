<?php /** @var array $patient */ /** @var array $exam */
$teeth = $exam['teeth'] ?? [];
$rows = [
    ['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'],
    ['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'],
];
?>
<div class="page-header">
    <div>
        <h1>Periodontograma — <?= e(format_date($exam['exam_date'])) ?></h1>
        <p class="subtitle">Paciente: <?= e($patient['first_name'].' '.$patient['last_name']) ?> · Examinador: <?= e($exam['examiner_name'] ?: '—') ?></p>
    </div>
    <a href="<?= url('/admin/pacientes/'.$patient['id'].'/periodontograma') ?>" class="btn">← Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted">Haz clic en una pieza para registrar profundidad de bolsa, recesión, sangrado, placa y movilidad.</p>
        <div class="perio-grid" id="perio-root" data-exam-id="<?= (int)$exam['id'] ?>">
            <?php foreach ($rows as $row): ?>
            <div class="perio-row">
                <?php foreach ($row as $code):
                    $t = $teeth[$code] ?? [];
                    $hasBleed = !empty($t['bleeding']);
                    $hasPlaque = !empty($t['plaque']);
                    $maxPd = max(array_filter([
                        $t['pd_vm'] ?? 0, $t['pd_vc'] ?? 0, $t['pd_vd'] ?? 0,
                        $t['pd_lm'] ?? 0, $t['pd_lc'] ?? 0, $t['pd_ld'] ?? 0,
                    ]) ?: [0]);
                    $cls = $maxPd >= 6 ? 'severe' : ($maxPd >= 4 ? 'moderate' : ($maxPd >= 3 ? 'mild' : ''));
                ?>
                    <div class="perio-tooth <?= e($cls) ?>" data-code="<?= e($code) ?>" data-tooth='<?= e(json_encode($t)) ?>'>
                        <div class="code"><?= e($code) ?></div>
                        <div class="markers">
                            <?php if ($hasBleed): ?><span class="m bleed" title="Sangrado">●</span><?php endif; ?>
                            <?php if ($hasPlaque): ?><span class="m plaque" title="Placa">▲</span><?php endif; ?>
                        </div>
                        <div class="pd">
                            <?php foreach (['pd_vm','pd_vc','pd_vd'] as $f): ?>
                                <span><?= $t[$f] ?? '-' ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="perio-legend mt-2">
            <div><span class="sw" style="background:#fee2e2;"></span> Severa (≥6mm)</div>
            <div><span class="sw" style="background:#fef3c7;"></span> Moderada (4-5mm)</div>
            <div><span class="sw" style="background:#dbeafe;"></span> Leve (3mm)</div>
            <div><span class="m bleed">●</span> Sangrado</div>
            <div><span class="m plaque">▲</span> Placa</div>
        </div>
    </div>
</div>

<div id="perio-form" class="card mt-3" style="display:none;">
    <div class="card-header"><h2>Pieza <span id="perio-code"></span></h2></div>
    <div class="card-body">
        <h4>Profundidad de bolsa (mm)</h4>
        <div class="grid grid-3 mb-2">
            <div><strong>Vestibular</strong>
                <div class="flex gap-1">
                    <input type="number" min="0" max="15" id="pd_vm" placeholder="Mesial" style="width:60px;">
                    <input type="number" min="0" max="15" id="pd_vc" placeholder="Centro" style="width:60px;">
                    <input type="number" min="0" max="15" id="pd_vd" placeholder="Distal" style="width:60px;">
                </div>
            </div>
            <div><strong>Lingual/Palatina</strong>
                <div class="flex gap-1">
                    <input type="number" min="0" max="15" id="pd_lm" placeholder="Mesial" style="width:60px;">
                    <input type="number" min="0" max="15" id="pd_lc" placeholder="Centro" style="width:60px;">
                    <input type="number" min="0" max="15" id="pd_ld" placeholder="Distal" style="width:60px;">
                </div>
            </div>
            <div><strong>Movilidad / Furca</strong>
                <div class="flex gap-1">
                    <input type="number" min="0" max="3" id="mobility" placeholder="Mov" style="width:60px;">
                    <input type="number" min="0" max="3" id="furcation" placeholder="Furca" style="width:60px;">
                </div>
            </div>
        </div>
        <h4>Recesión (mm)</h4>
        <div class="grid grid-2 mb-2">
            <div><strong>Vestibular</strong>
                <div class="flex gap-1">
                    <input type="number" min="0" max="15" id="rec_vm" placeholder="M" style="width:60px;">
                    <input type="number" min="0" max="15" id="rec_vc" placeholder="C" style="width:60px;">
                    <input type="number" min="0" max="15" id="rec_vd" placeholder="D" style="width:60px;">
                </div>
            </div>
            <div><strong>Lingual</strong>
                <div class="flex gap-1">
                    <input type="number" min="0" max="15" id="rec_lm" placeholder="M" style="width:60px;">
                    <input type="number" min="0" max="15" id="rec_lc" placeholder="C" style="width:60px;">
                    <input type="number" min="0" max="15" id="rec_ld" placeholder="D" style="width:60px;">
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <label class="checkbox"><input type="checkbox" id="bleeding"> Sangrado</label>
            <label class="checkbox"><input type="checkbox" id="plaque"> Placa</label>
            <label class="checkbox"><input type="checkbox" id="suppuration"> Supuración</label>
        </div>
        <button class="btn btn-primary mt-2" id="save-perio">Guardar</button>
    </div>
</div>

<style>
.perio-grid { display:flex; flex-direction:column; gap:6px; padding:1rem; background:var(--gray-50); border-radius:8px; }
.perio-row { display:flex; gap:3px; justify-content:center; }
.perio-tooth { width:54px; min-height:80px; background:#fff; border:1px solid var(--gray-300); border-radius:6px;
    padding:4px; display:flex; flex-direction:column; align-items:center; cursor:pointer; font-size:0.7rem; }
.perio-tooth:hover { border-color:var(--primary); box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.perio-tooth.mild     { background:#dbeafe; }
.perio-tooth.moderate { background:#fef3c7; }
.perio-tooth.severe   { background:#fee2e2; }
.perio-tooth .code { font-weight:700; color:var(--gray-700); }
.perio-tooth .pd { display:flex; gap:2px; margin-top:3px; }
.perio-tooth .pd span { background:#fff; padding:1px 3px; border-radius:3px; min-width:14px; text-align:center;
    border:1px solid var(--gray-200); font-size:0.65rem; }
.perio-tooth .markers { display:flex; gap:2px; height:14px; }
.m.bleed  { color:var(--danger); font-size:0.7rem; }
.m.plaque { color:var(--warning); font-size:0.7rem; }
.perio-legend { display:flex; gap:1rem; font-size:0.85rem; flex-wrap:wrap; }
.perio-legend .sw { display:inline-block; width:14px; height:14px; border-radius:3px; margin-right:4px; vertical-align:middle; border:1px solid var(--gray-300); }
</style>

<script>
const examId = <?= (int)$exam['id'] ?>;
let selectedTooth = null;
const form = document.getElementById('perio-form');
const codeDisp = document.getElementById('perio-code');
const fields = ['pd_vm','pd_vc','pd_vd','pd_lm','pd_lc','pd_ld','rec_vm','rec_vc','rec_vd','rec_lm','rec_lc','rec_ld','mobility','furcation'];
const flags = ['bleeding','plaque','suppuration'];

document.querySelectorAll('.perio-tooth').forEach(el => {
    el.addEventListener('click', () => {
        selectedTooth = el;
        const data = JSON.parse(el.dataset.tooth || '{}');
        codeDisp.textContent = el.dataset.code;
        fields.forEach(f => document.getElementById(f).value = data[f] ?? '');
        flags.forEach(f => document.getElementById(f).checked = !!data[f]);
        form.style.display = 'block';
        form.scrollIntoView({behavior:'smooth'});
    });
});

document.getElementById('save-perio').addEventListener('click', async () => {
    if (!selectedTooth) return;
    const payload = { tooth_code: selectedTooth.dataset.code };
    fields.forEach(f => { const v = document.getElementById(f).value; if (v !== '') payload[f] = parseInt(v); });
    flags.forEach(f => payload[f] = document.getElementById(f).checked ? 1 : 0);
    try {
        await apiFetch('/admin/periodontograma/' + examId + '/tooth', { method:'POST', body: payload });
        selectedTooth.dataset.tooth = JSON.stringify(payload);
        const maxPd = Math.max(payload.pd_vm||0, payload.pd_vc||0, payload.pd_vd||0,
                                payload.pd_lm||0, payload.pd_lc||0, payload.pd_ld||0);
        selectedTooth.classList.remove('mild','moderate','severe');
        if (maxPd >= 6) selectedTooth.classList.add('severe');
        else if (maxPd >= 4) selectedTooth.classList.add('moderate');
        else if (maxPd >= 3) selectedTooth.classList.add('mild');
        alert('Guardado.');
    } catch (e) { alert('Error: ' + e.message); }
});
</script>
