<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><title>Paso 2 · Datos fiscales</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head><body class="login-body">
<div class="login-wrapper" style="max-width:640px;">
    <div class="login-card">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="color:#c9a96e;letter-spacing:3px;font-size:.8rem;">PASO 2 DE 3</div>
            <h1 style="font-family:'Playfair Display',serif;color:#4a6356;font-size:1.8rem;">🇭🇳 Datos fiscales SAR</h1>
            <p style="color:#7a8a82;font-size:.9rem;">Estos datos aparecerán en tus facturas. Podrás editarlos después.</p>
        </div>
        <form method="post" action="<?= url('/setup/paso-2') ?>">
            <?= csrf_field() ?>
            <div class="form-group"><label>Razón social *</label><input type="text" name="sar_business_name" required></div>
            <div class="form-group"><label>RTN (14 dígitos) *</label><input type="text" name="sar_rtn" required maxlength="14" pattern="[0-9]{14}" placeholder="08019999999999"></div>
            <div class="form-group"><label>Dirección fiscal</label><textarea name="sar_address" rows="2"></textarea></div>
            <div class="form-group">
                <label>Régimen</label>
                <select name="sar_regimen">
                    <option value="General">General</option>
                    <option value="Simplificado">Simplificado</option>
                    <option value="Exonerado">Exonerado</option>
                </select>
            </div>
            <p style="color:#7a8a82;font-size:.85rem;margin:14px 0;">💡 La autorización CAI se registra después en Sistema → SAR / CAI</p>
            <button type="submit" class="btn-admin btn-block">Continuar →</button>
        </form>
    </div>
</div></body></html>
