<?php $pageTitle = 'Mi cuenta'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero" style="padding:50px 24px;">
    <div class="container">
        <h1>Hola, <?= e($customer['first_name']) ?> 👋</h1>
        <p>Bienvenido a tu área privada.</p>
    </div>
</section>

<section style="padding:40px 0;">
    <div class="container">
        <div style="display:flex;gap:16px;margin-bottom:30px;flex-wrap:wrap;">
            <a href="<?= url('/mi-cuenta/inicio') ?>" class="btn btn-sm">Inicio</a>
            <a href="<?= url('/mi-cuenta/perfil') ?>" class="btn btn-outline btn-sm">Mi perfil</a>
            <a href="<?= url('/reservar') ?>" class="btn btn-accent btn-sm">+ Nueva cita</a>
            <a href="<?= url('/mi-cuenta/salir') ?>" class="btn btn-outline btn-sm" style="margin-left:auto;">Salir</a>
        </div>

        <?php if (!empty($customer['referral_code'])): ?>
        <div class="card" style="background:linear-gradient(135deg,var(--accent),#a88a52);color:#fff;padding:30px;border-radius:12px;margin-bottom:30px;">
            <h2 style="color:#fff;margin-bottom:8px;">🎁 Invita y gana</h2>
            <p style="opacity:.95;margin-bottom:14px;">Comparte tu código con amigos. Cuando lo usen, ambos recibís 10% de descuento.</p>
            <div style="background:rgba(255,255,255,.2);padding:14px;border-radius:8px;text-align:center;font-family:monospace;font-size:1.4rem;letter-spacing:3px;">
                <?= e($customer['referral_code']) ?>
            </div>
        </div>
        <?php endif; ?>

        <h2 class="section-title-left">Próximas citas</h2>
        <?php if (empty($upcoming)): ?>
            <p style="color:var(--muted);">No tienes citas próximas. <a href="<?= url('/reservar') ?>">Reservar ahora →</a></p>
        <?php else: ?>
            <div class="services-grid">
                <?php foreach ($upcoming as $a): ?>
                    <div class="service-card">
                        <h3><?= e($a['service_name']) ?></h3>
                        <p><strong><?= dt($a['starts_at'], 'd/m/Y') ?></strong> a las <?= dt($a['starts_at'], 'H:i') ?></p>
                        <p>Estado: <span class="badge badge-<?= $a['status'] === 'confirmada' ? 'success' : 'pending' ?>"><?= e($a['status']) ?></span></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($packs)): ?>
        <h2 class="section-title-left" style="margin-top:50px;">Tus bonos</h2>
        <div class="services-grid">
            <?php foreach ($packs as $p): ?>
                <div class="service-card" style="border-left:4px solid var(--accent);">
                    <h3><?= e($p['pack_name']) ?></h3>
                    <p>Sesiones usadas: <strong><?= (int)$p['sessions_used'] ?> / <?= (int)$p['sessions_total'] ?></strong></p>
                    <p style="color:var(--muted);font-size:.85rem;">Caduca: <?= dt($p['expires_at'], 'd/m/Y') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h2 class="section-title-left" style="margin-top:50px;">Historial</h2>
        <?php if (empty($history)): ?>
            <p style="color:var(--muted);">Aún no tienes historial de visitas.</p>
        <?php else: ?>
            <div style="background:white;border-radius:12px;padding:24px;box-shadow:var(--shadow);">
            <?php foreach ($history as $h): ?>
                <div style="padding:14px 0;border-bottom:1px solid var(--brand-light);">
                    <strong><?= e($h['service_name']) ?></strong>
                    <span style="float:right;color:var(--muted);font-size:.9rem;"><?= dt($h['starts_at'], 'd/m/Y') ?></span>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
