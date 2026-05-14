        </div><!-- /.content -->
    </div><!-- /.main-content -->
</div><!-- /.admin-layout -->

<div class="toast-container" id="toastContainer"></div>

<script>
// Toggle sidebar móvil
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
// Cerrar dropdown al hacer click fuera
document.addEventListener('click', (e) => {
    if (!e.target.closest('.user-menu')) {
        document.getElementById('userDropdown')?.classList.remove('open');
    }
});
// Tema claro/oscuro
document.getElementById('themeToggle')?.addEventListener('click', () => {
    const html = document.documentElement;
    const cur = html.getAttribute('data-theme');
    const next = cur === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    fetch('/admin/api/tema?t=' + next, { credentials: 'same-origin' });
});
// Toasts
function toast(message, type='info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = message;
    c.appendChild(t);
    setTimeout(() => t.style.opacity = 0, 3500);
    setTimeout(() => t.remove(), 4000);
}
</script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
