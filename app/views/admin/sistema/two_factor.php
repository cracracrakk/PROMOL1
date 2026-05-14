<?php $pageTitle = 'Configurar 2FA'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>
<div class="page-header">
    <div class="breadcrumb"><a href="<?= url('/admin/sistema') ?>">← Volver</a></div>
</div>

<div class="card-grid">
    <div class="card">
        <h3>🔒 Autenticación en dos pasos</h3>
        <ol style="margin:20px 0 30px;line-height:2;">
            <li>Descarga <strong>Google Authenticator</strong>, <strong>Authy</strong> o <strong>Microsoft Authenticator</strong></li>
            <li>Abre la app y escanea el código QR</li>
            <li>Introduce el código de 6 dígitos que aparece</li>
        </ol>
        <form method="post" action="<?= url('/admin/sistema/2fa/verificar') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Código de 6 dígitos *</label>
                <input type="text" name="code" required maxlength="6" pattern="[0-9]{6}"
                       inputmode="numeric" placeholder="000000"
                       style="font-size:1.5rem;text-align:center;letter-spacing:6px;font-family:monospace;">
            </div>
            <button type="submit" class="btn-admin">Activar 2FA</button>
        </form>
    </div>

    <div class="card" style="text-align:center;">
        <h3>Escanea con tu app</h3>
        <img src="<?= e($qr) ?>" alt="QR Code" style="max-width:220px;margin:20px auto;">
        <p style="font-size:.85rem;color:var(--muted);">¿No puedes escanear? Introduce esta clave manualmente:</p>
        <code style="display:block;background:var(--bg);padding:12px;border-radius:6px;font-size:1rem;letter-spacing:2px;"><?= e($secret) ?></code>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
