<?php /** @var array $patient */ ?>
<div class="page-header">
    <h1>Nueva receta — <?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
    <a href="<?= url('/admin/pacientes/'.$patient['id'].'/recetas') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= url('/admin/pacientes/'.$patient['id'].'/recetas/nueva') ?>">
    <?= csrf_field() ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="form-group">
                <label>Diagnóstico</label>
                <input type="text" name="diagnosis" placeholder="Ej: Infección dental">
            </div>
            <div class="form-group">
                <label>Notas / indicaciones generales</label>
                <textarea name="notes" rows="2"></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Medicamentos</h2>
            <button type="button" id="add-drug" class="btn btn-sm">+ Agregar</button>
        </div>
        <div class="card-body" id="drug-list"></div>
    </div>

    <div class="flex justify-between">
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/recetas') ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn btn-primary">Emitir receta</button>
    </div>
</form>

<script>
const list = document.getElementById('drug-list');
const COMMON_DRUGS = [
    'Amoxicilina 500mg','Ibuprofeno 600mg','Paracetamol 500mg','Diclofenaco 50mg',
    'Clorhexidina 0.12% (enjuague)','Metronidazol 500mg','Naproxeno 500mg',
    'Cefalexina 500mg','Ketorolaco 10mg','Dexametasona 4mg'
];

function addRow() {
    const idx = list.children.length;
    const div = document.createElement('div');
    div.className = 'grid';
    div.style.cssText = 'grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:8px;margin-bottom:8px;align-items:end;';
    div.innerHTML = `
        <div class="form-group" style="margin:0;"><label>Medicamento ${idx+1}</label>
            <input type="text" name="drug[]" list="drugs-list" required placeholder="Ej: Amoxicilina 500mg">
        </div>
        <div class="form-group" style="margin:0;"><label>Dosis</label><input type="text" name="dosage[]" placeholder="1 tableta"></div>
        <div class="form-group" style="margin:0;"><label>Frecuencia</label><input type="text" name="frequency[]" placeholder="cada 8h"></div>
        <div class="form-group" style="margin:0;"><label>Duración</label><input type="text" name="duration[]" placeholder="7 días"></div>
        <div class="form-group" style="margin:0;"><label>Instrucciones</label><input type="text" name="instructions[]" placeholder="después de comer"></div>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(div);
}

const dl = document.createElement('datalist');
dl.id = 'drugs-list';
COMMON_DRUGS.forEach(d => { const o = document.createElement('option'); o.value = d; dl.appendChild(o); });
document.body.appendChild(dl);

document.getElementById('add-drug').addEventListener('click', addRow);
addRow();
</script>
