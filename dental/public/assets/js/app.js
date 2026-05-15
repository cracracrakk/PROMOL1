// DentalCore - JS principal

// Service worker (PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Confirmaciones para formularios destructivos
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm(form.dataset.confirm)) e.preventDefault();
        });
    });

    // Toggle de la sidebar en móvil (placeholder)
    const burger = document.getElementById('burger');
    if (burger) {
        burger.addEventListener('click', () => {
            document.querySelector('.sidebar')?.classList.toggle('open');
        });
    }
});

// Helper para llamadas a la API con CSRF
window.apiFetch = async function (url, options = {}) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const headers = options.headers || {};
    if (csrfMeta) headers['X-CSRF-Token'] = csrfMeta.content;
    headers['Accept'] = 'application/json';
    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }
    options.headers = headers;
    const res = await fetch(url, options);
    if (!res.ok) {
        const err = await res.json().catch(() => ({ error: 'Error desconocido' }));
        throw new Error(err.error || ('HTTP ' + res.status));
    }
    return res.json();
};
