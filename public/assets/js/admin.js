// JS Panel Admin
document.addEventListener('DOMContentLoaded', function () {
    // Confirmar acciones destructivas
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm(form.dataset.confirm || '¿Estás seguro?')) e.preventDefault();
        });
    });

    // Búsqueda rápida en tablas con filtro
    document.querySelectorAll('input[data-filter-table]').forEach(input => {
        input.addEventListener('input', () => {
            const tableId = input.dataset.filterTable;
            const term = input.value.toLowerCase();
            document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    });

    // Atajos de teclado
    document.addEventListener('keydown', e => {
        // Ctrl/Cmd + K = búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('input[data-search],input.search-bar')?.focus();
        }
    });
});
