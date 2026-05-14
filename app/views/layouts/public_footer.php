</main>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4><?= e(brand_name()) ?></h4>
                <p><?= e(setting('spa_tagline', 'Tu santuario de bienestar')) ?></p>
                <p style="margin-top:12px;font-size:.9rem;"><?= nl2br(e(setting('spa_address', ''))) ?></p>
            </div>
            <div>
                <h4>Contacto</h4>
                <p>📞 <?= e(setting('spa_phone', '')) ?></p>
                <p>📧 <?= e(setting('spa_email', '')) ?></p>
                <?php if ($ig = setting('spa_instagram', '')): ?>
                    <p>📷 <?= e($ig) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <h4>Horario</h4>
                <p><?= nl2br(e(setting('spa_hours', 'Lun-Vie 10:00 - 20:00'))) ?></p>
            </div>
            <div>
                <h4>Enlaces</h4>
                <p><a href="<?= url('/servicios') ?>">Servicios</a></p>
                <p><a href="<?= url('/regalo') ?>">Tarjetas Regalo</a></p>
                <p><a href="<?= url('/reservar') ?>">Reservar Cita</a></p>
                <p><a href="<?= url('/mi-cuenta') ?>">Mi cuenta</a></p>
                <p><a href="<?= url('/login') ?>">Acceso Personal</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= e(brand_name()) ?>. Todos los derechos reservados.</p>
            <p style="margin-top:8px;font-size:.8rem;opacity:.7;">
                <?= e(setting('sar_business_name', '')) ?>
                <?php if ($rtn = setting('sar_rtn', '')): ?> · RTN <?= e($rtn) ?><?php endif; ?>
            </p>
        </div>
    </div>
</footer>

<?php if ($wa = setting('spa_whatsapp', '')): ?>
<a href="https://wa.me/<?= e(preg_replace('/\D/', '', $wa)) ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
    <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.4c1.4.8 3 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18c-1.6 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3C4.4 15 4 13.6 4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8-8 8zm4.5-5.8c-.2-.1-1.4-.7-1.6-.8s-.4-.1-.5.1-.6.8-.7.9c-.1.2-.3.2-.5.1-.7-.3-1.5-.8-2.3-1.8-.3-.4-.7-1-.7-1.2 0-.2 0-.3.1-.4l.3-.3c.1-.1.1-.2.2-.3 0-.1 0-.2 0-.3 0-.1-.5-1.2-.7-1.6-.2-.4-.4-.4-.5-.4-.1 0-.3 0-.4 0-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2 0 1.2.8 2.3 1 2.5.1.2 1.7 2.6 4.1 3.6 1.5.6 2 .7 2.7.6.4-.1 1.3-.5 1.5-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.1-.4-.2z"/></svg>
</a>
<?php endif; ?>

<script src="<?= asset('js/public.js') ?>"></script>
<script>if ('serviceWorker' in navigator) navigator.serviceWorker.register('<?= url('/sw.js') ?>');</script>
</body>
</html>
