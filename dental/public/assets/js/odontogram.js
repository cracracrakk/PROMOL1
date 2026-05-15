// DentalCore - Odontograma interactivo
(function () {
    const root = document.getElementById('odontogram-root');
    if (!root) return;

    const patientId = root.dataset.patientId;
    const statusSelect = document.getElementById('tooth-status-select');
    const noteInput    = document.getElementById('tooth-note-input');
    const saveBtn      = document.getElementById('save-tooth');
    const detail       = document.getElementById('tooth-detail');
    const detailCode   = document.getElementById('tooth-detail-code');
    let selectedTooth  = null;

    const STATUS_LABELS = {
        healthy: 'Sana', caries: 'Caries', filled: 'Restaurada',
        crown: 'Corona', root_canal: 'Endodoncia', extracted: 'Extraída',
        missing: 'Ausente', implant: 'Implante', bridge: 'Puente',
        sealant: 'Sellante', fractured: 'Fracturada', to_extract: 'Por extraer',
    };

    root.querySelectorAll('.tooth').forEach(t => {
        t.addEventListener('click', () => {
            root.querySelectorAll('.tooth.selected').forEach(x => x.classList.remove('selected'));
            t.classList.add('selected');
            selectedTooth = t;
            detail.style.display = 'block';
            detailCode.textContent = t.dataset.code;
            statusSelect.value = t.dataset.status || 'healthy';
            noteInput.value = t.dataset.notes || '';
        });
    });

    saveBtn?.addEventListener('click', async () => {
        if (!selectedTooth) return;
        const data = {
            teeth: [{
                tooth_code: selectedTooth.dataset.code,
                dentition:  selectedTooth.dataset.dentition || 'permanent',
                status:     statusSelect.value,
                notes:      noteInput.value || null,
            }]
        };
        saveBtn.disabled = true;
        saveBtn.textContent = 'Guardando...';
        try {
            await window.apiFetch(`/admin/pacientes/${patientId}/odontograma`, {
                method: 'POST', body: data,
            });
            // Actualiza visualmente
            Object.keys(STATUS_LABELS).forEach(s => selectedTooth.classList.remove('status-' + s));
            selectedTooth.classList.add('status-' + statusSelect.value);
            selectedTooth.dataset.status = statusSelect.value;
            selectedTooth.dataset.notes = noteInput.value;
            saveBtn.textContent = '✓ Guardado';
            setTimeout(() => { saveBtn.textContent = 'Guardar cambios'; }, 1500);
        } catch (e) {
            alert('Error: ' + e.message);
            saveBtn.textContent = 'Guardar cambios';
        } finally {
            saveBtn.disabled = false;
        }
    });
})();
