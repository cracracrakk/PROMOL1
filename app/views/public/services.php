<?php $pageTitle = 'Servicios'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="page-hero">
    <div class="container">
        <h1>Nuestros Servicios</h1>
        <p>Descubre nuestra carta completa de tratamientos diseñados para tu bienestar.</p>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <?php foreach ($categories as $cat): ?>
            <div class="category-block">
                <h2 class="category-title"><?= e($cat['name']) ?></h2>
                <?php if (!empty($cat['description'])): ?>
                    <p class="category-desc"><?= e($cat['description']) ?></p>
                <?php endif; ?>
                <div class="services-grid">
                    <?php foreach ($servicesByCat[$cat['id']] ?? [] as $s): ?>
                        <article class="service-card">
                            <h3><?= e($s['name']) ?></h3>
                            <p><?= e($s['description']) ?></p>
                            <div class="service-meta">
                                <span><?= (int)$s['duration_minutes'] ?> min</span>
                                <span class="service-price"><?= money($s['price']) ?></span>
                            </div>
                            <?php if ($s['bookable_online']): ?>
                                <a href="<?= url('/reservar?service=' . $s['id']) ?>" class="btn btn-outline btn-sm">Reservar</a>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
