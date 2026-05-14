<?php $pageTitle = 'Configuración del sistema'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<form method="post" action="<?= url('/admin/sistema/guardar') ?>" enctype="multipart/form-data" class="card">
    <?= csrf_field() ?>

    <div class="form-section">
        <h3>Identidad del negocio</h3>
        <div class="form-grid">
            <div class="form-group"><label>Nombre del spa</label><input type="text" name="spa_name" value="<?= e(setting('spa_name', '')) ?>"></div>
            <div class="form-group"><label>Eslogan</label><input type="text" name="spa_tagline" value="<?= e(setting('spa_tagline', '')) ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><textarea name="spa_description" rows="3"><?= e(setting('spa_description', '')) ?></textarea></div>
            <div class="form-group">
                <label>Logo (PNG, JPG, SVG)</label>
                <input type="file" name="logo" accept="image/*">
                <?php if (brand_logo()): ?><img src="<?= brand_logo() ?>" style="height:40px;margin-top:8px;"><?php endif; ?>
            </div>
            <div class="form-group"><label>URL pública</label><input type="text" name="app_url" value="<?= e(setting('app_url', '')) ?>" placeholder="https://midominio.com"></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Colores y moneda</h3>
        <div class="form-grid">
            <div class="form-group"><label>Color primario</label><input type="color" name="color_primary" value="<?= e(setting('color_primary', '#6b8a7a')) ?>"></div>
            <div class="form-group"><label>Color primario oscuro</label><input type="color" name="color_primary_dark" value="<?= e(setting('color_primary_dark', '#4a6356')) ?>"></div>
            <div class="form-group"><label>Color de acento</label><input type="color" name="color_accent" value="<?= e(setting('color_accent', '#c9a96e')) ?>"></div>
            <div class="form-group"><label>Símbolo moneda</label><input type="text" name="currency_symbol" value="<?= e(setting('currency_symbol', 'L.')) ?>"></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Contacto y horario</h3>
        <div class="form-grid">
            <div class="form-group" style="grid-column:1/-1;"><label>Dirección</label><textarea name="spa_address" rows="2"><?= e(setting('spa_address', '')) ?></textarea></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="spa_phone" value="<?= e(setting('spa_phone', '')) ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="spa_email" value="<?= e(setting('spa_email', '')) ?>"></div>
            <div class="form-group"><label>WhatsApp (con código país)</label><input type="text" name="spa_whatsapp" value="<?= e(setting('spa_whatsapp', '')) ?>" placeholder="50499998888"></div>
            <div class="form-group"><label>Instagram</label><input type="text" name="spa_instagram" value="<?= e(setting('spa_instagram', '')) ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Horario (una línea por día)</label><textarea name="spa_hours" rows="3"><?= e(setting('spa_hours', '')) ?></textarea></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Página de inicio (Hero)</h3>
        <div class="form-grid">
            <div class="form-group"><label>Título principal</label><input type="text" name="hero_title" value="<?= e(setting('hero_title', '')) ?>"></div>
            <div class="form-group"><label>Subtítulo</label><input type="text" name="hero_subtitle" value="<?= e(setting('hero_subtitle', '')) ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Texto "Sobre nosotros"</label><textarea name="about_text" rows="3"><?= e(setting('about_text', '')) ?></textarea></div>
        </div>
    </div>

    <div class="form-section">
        <h3>🇭🇳 Datos fiscales SAR Honduras</h3>
        <p class="form-help">Estos datos aparecerán en todas las facturas emitidas.</p>
        <div class="form-grid">
            <div class="form-group" style="grid-column:1/-1;"><label>Razón social</label><input type="text" name="sar_business_name" value="<?= e(setting('sar_business_name', '')) ?>"></div>
            <div class="form-group"><label>Nombre comercial</label><input type="text" name="sar_trade_name" value="<?= e(setting('sar_trade_name', '')) ?>"></div>
            <div class="form-group"><label>RTN</label><input type="text" name="sar_rtn" value="<?= e(setting('sar_rtn', '')) ?>" placeholder="08019999999999"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Dirección fiscal</label><textarea name="sar_address" rows="2"><?= e(setting('sar_address', '')) ?></textarea></div>
            <div class="form-group"><label>Teléfono fiscal</label><input type="text" name="sar_phone" value="<?= e(setting('sar_phone', '')) ?>"></div>
            <div class="form-group"><label>Email fiscal</label><input type="email" name="sar_email" value="<?= e(setting('sar_email', '')) ?>"></div>
            <div class="form-group">
                <label>Régimen</label>
                <select name="sar_regimen">
                    <option value="General" <?= setting('sar_regimen') === 'General' ? 'selected' : '' ?>>General</option>
                    <option value="Simplificado" <?= setting('sar_regimen') === 'Simplificado' ? 'selected' : '' ?>>Simplificado</option>
                    <option value="Exonerado" <?= setting('sar_regimen') === 'Exonerado' ? 'selected' : '' ?>>Exonerado</option>
                </select>
            </div>
            <div class="form-group"><label>Resolución SAR</label><input type="text" name="sar_resolucion" value="<?= e(setting('sar_resolucion', '')) ?>"></div>
        </div>
        <p class="form-help" style="margin-top:10px;">
            ⚠️ Las autorizaciones CAI (códigos y rangos) se gestionan en <a href="<?= url('/admin/sistema/sar') ?>">SAR / CAI</a>.
        </p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-admin">Guardar configuración</button>
    </div>
</form>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
