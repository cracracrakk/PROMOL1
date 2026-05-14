<?php $pageTitle = 'Sobre Nosotros'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="page-hero">
    <div class="container">
        <h1>Sobre Nosotros</h1>
        <p><?= e(setting('about_subtitle', 'Un equipo apasionado por el bienestar.')) ?></p>
    </div>
</section>

<section>
    <div class="container">
        <div class="about-grid">
            <div>
                <h2 class="section-title-left">Nuestra Filosofía</h2>
                <p><?= nl2br(e(setting('about_text', 'Creemos que el bienestar es un derecho, no un lujo. Por eso cada visita es una experiencia personalizada que combina técnicas ancestrales con tecnología moderna para devolverte el equilibrio.'))) ?></p>
            </div>
            <div class="about-image">🌿</div>
        </div>
    </div>
</section>

<?php if (!empty($team)): ?>
<section class="team-section">
    <div class="container">
        <h2 class="section-title"><small>Nuestro Equipo</small>Profesionales a tu servicio</h2>
        <div class="team-grid">
            <?php foreach ($team as $member): ?>
                <div class="team-card">
                    <div class="team-avatar" style="background:<?= e($member['color']) ?>20;color:<?= e($member['color']) ?>;">
                        <?= e(mb_substr($member['name'], 0, 1)) ?>
                    </div>
                    <h3><?= e($member['name']) ?></h3>
                    <p class="team-role"><?= e(ucfirst($member['role'])) ?></p>
                    <?php if (!empty($member['bio'])): ?>
                        <p><?= e($member['bio']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
