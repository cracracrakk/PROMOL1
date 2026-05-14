<?php require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="hero" data-aos="fade">
    <div class="container">
        <small class="hero-eyebrow"><?= e(setting('spa_tagline', 'Bienestar · Armonía · Equilibrio')) ?></small>
        <h1><?= e(setting('hero_title', 'Renueva cuerpo y mente')) ?></h1>
        <p><?= e(setting('hero_subtitle', 'Tratamientos personalizados que despiertan tus sentidos en un entorno de pura serenidad.')) ?></p>
        <div class="hero-cta">
            <a href="<?= url('/reservar') ?>" class="btn">Reservar cita</a>
            <a href="<?= url('/servicios') ?>" class="btn btn-outline">Ver servicios</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature"><div class="feature-icon">🌿</div><h3>Productos Naturales</h3><p>Solo utilizamos productos premium de origen natural y orgánico.</p></div>
            <div class="feature"><div class="feature-icon">👐</div><h3>Profesionales Certificados</h3><p>Nuestro equipo cuenta con años de experiencia y formación continua.</p></div>
            <div class="feature"><div class="feature-icon">🧖</div><h3>Espacio Privado</h3><p>Cabinas individuales diseñadas para tu máxima comodidad y privacidad.</p></div>
            <div class="feature"><div class="feature-icon">💆</div><h3>Tratamientos a Medida</h3><p>Cada sesión se adapta a tus necesidades y preferencias personales.</p></div>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <h2 class="section-title"><small>Nuestros Tratamientos</small>Servicios Destacados</h2>
        <div class="services-grid">
            <?php foreach ($featuredServices ?? [] as $s): ?>
                <article class="service-card">
                    <?php if (!empty($s['image'])): ?>
                        <img src="<?= asset('uploads/' . $s['image']) ?>" alt="<?= e($s['name']) ?>" class="service-image">
                    <?php else: ?>
                        <div class="service-image-placeholder">✨</div>
                    <?php endif; ?>
                    <h3><?= e($s['name']) ?></h3>
                    <p><?= e($s['description']) ?></p>
                    <div class="service-meta">
                        <span><?= (int)$s['duration_minutes'] ?> min</span>
                        <span class="service-price"><?= money($s['price']) ?></span>
                    </div>
                    <a href="<?= url('/reservar?service=' . $s['id']) ?>" class="btn btn-outline btn-sm">Reservar</a>
                </article>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:40px;">
            <a href="<?= url('/servicios') ?>" class="btn">Ver todos los servicios</a>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="testimonials">
    <div class="container">
        <h2 class="section-title"><small>Lo que dicen</small>Nuestros Clientes</h2>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
                <blockquote class="testimonial">
                    <div class="stars"><?= str_repeat('★', (int)$t['rating']) ?><?= str_repeat('☆', 5 - (int)$t['rating']) ?></div>
                    <p>“<?= e($t['comment']) ?>”</p>
                    <cite>— <?= e($t['author']) ?></cite>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <h2>Regala bienestar</h2>
        <p>Sorprende a alguien especial con una tarjeta regalo personalizada.</p>
        <a href="<?= url('/regalo') ?>" class="btn btn-accent">Comprar Tarjeta Regalo</a>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
