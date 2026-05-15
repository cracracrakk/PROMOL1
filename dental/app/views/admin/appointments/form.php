<?php /** @var array $dentists */ /** @var array $rooms */ /** @var array $treatments */ ?>
<div class="page-header">
    <h1>Nueva cita</h1>
    <a href="<?= url('/admin/citas') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= url('/admin/citas/nueva') ?>">
    <?= csrf_field() ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group">
                    <label>Paciente *</label>
                    <input type="text" id="patient-search" placeholder="Buscar paciente por nombre, código o documento...">
                    <input type="hidden" name="patient_id" id="patient-id" value="<?= e((int)input('patient_id', 0)) ?>" required>
                    <div id="patient-results" style="display:none;background:#fff;border:1px solid var(--gray-300);border-radius:6px;margin-top:4px;max-height:200px;overflow-y:auto;"></div>
                    <div id="patient-selected" class="text-muted" style="font-size:0.85rem;margin-top:0.25rem;"></div>
                </div>
                <div class="form-group">
                    <label>Odontólogo *</label>
                    <select name="dentist_id" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($dentists as $d): ?>
                            <option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?><?= $d['specialty'] ? ' ('.e($d['specialty']).')' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Inicio *</label>
                    <input type="datetime-local" name="starts_at" required value="<?= e(date('Y-m-d\\TH:i', strtotime('+1 hour'))) ?>">
                </div>
                <div class="form-group">
                    <label>Fin *</label>
                    <input type="datetime-local" name="ends_at" required value="<?= e(date('Y-m-d\\TH:i', strtotime('+1 hour 30 minutes'))) ?>">
                </div>
                <div class="form-group">
                    <label>Tratamiento (motivo)</label>
                    <select name="treatment_id">
                        <option value="">— Sin definir —</option>
                        <?php foreach ($treatments as $t): ?>
                            <option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sillón</label>
                    <select name="room_id">
                        <option value="">— Cualquier —</option>
                        <?php foreach ($rooms as $r): ?>
                            <option value="<?= (int)$r['id'] ?>"><?= e($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label>Motivo / Notas</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="<?= url('/admin/citas') ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn btn-primary">Crear cita</button>
    </div>
</form>

<script>
const search = document.getElementById('patient-search');
const results = document.getElementById('patient-results');
const idInput = document.getElementById('patient-id');
const selected = document.getElementById('patient-selected');

let timer;
search?.addEventListener('input', () => {
    clearTimeout(timer);
    const q = search.value.trim();
    if (q.length < 2) { results.style.display = 'none'; return; }
    timer = setTimeout(async () => {
        const data = await apiFetch('/api/patients?q=' + encodeURIComponent(q));
        results.innerHTML = '';
        data.data.slice(0, 8).forEach(p => {
            const item = document.createElement('div');
            item.style.cssText = 'padding:0.5rem 0.75rem;cursor:pointer;border-bottom:1px solid #eee;';
            item.innerHTML = '<strong>' + p.last_name + ', ' + p.first_name + '</strong> <span style="color:#888;font-size:0.8rem;">(' + p.code + ')</span>';
            item.onclick = () => {
                idInput.value = p.id;
                selected.innerHTML = '✓ Seleccionado: <strong>' + p.first_name + ' ' + p.last_name + '</strong>';
                results.style.display = 'none';
                search.value = '';
            };
            results.appendChild(item);
        });
        results.style.display = data.data.length ? 'block' : 'none';
    }, 250);
});

// Si viene patient_id por GET, cargar nombre
(async () => {
    const preId = idInput.value;
    if (preId && preId !== '0') {
        try {
            const p = await apiFetch('/api/patients/' + preId);
            selected.innerHTML = '✓ <strong>' + p.first_name + ' ' + p.last_name + '</strong> (' + p.code + ')';
        } catch (e) {}
    }
})();
</script>
