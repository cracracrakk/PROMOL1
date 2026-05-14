<?php $pageTitle = 'Tu opinión'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero">
    <div class="container">
        <h1>Tu opinión cuenta</h1>
        <p>¿Cómo calificarías tu experiencia?</p>
    </div>
</section>
<section>
    <div class="container">
        <form method="post" action="<?= url('/cita/encuesta/' . $token) ?>" class="form-card">
            <?= csrf_field() ?>
            <h3>¿Recomendarías este spa a un amigo?</h3>
            <p style="color:var(--muted);margin-bottom:20px;">Del 0 (nada probable) al 10 (muy probable)</p>
            <div class="nps-scale">
                <?php for ($i = 0; $i <= 10; $i++): ?>
                    <label class="nps-option">
                        <input type="radio" name="nps" value="<?= $i ?>" required>
                        <span><?= $i ?></span>
                    </label>
                <?php endfor; ?>
            </div>
            <div class="form-group" style="margin-top:30px;">
                <label>¿Algo más que nos quieras decir? (opcional)</label>
                <textarea name="comment" rows="4"></textarea>
            </div>
            <button type="submit" class="btn">Enviar valoración</button>
        </form>
    </div>
</section>
<style>
.nps-scale { display: grid; grid-template-columns: repeat(11, 1fr); gap: 6px; }
.nps-option { display: block; cursor: pointer; }
.nps-option input { position: absolute; opacity: 0; }
.nps-option span { display: block; padding: 14px 0; text-align: center; border: 2px solid var(--brand-light); border-radius: 8px; transition: all .2s; font-weight: 600; }
.nps-option:has(input:checked) span { background: var(--accent); color: white; border-color: var(--accent); }
.nps-option:hover span { background: var(--brand-light); }
@media (max-width:600px) { .nps-scale { grid-template-columns: repeat(6, 1fr); } }
</style>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
