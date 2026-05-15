<?php /** @var string $token */ /** @var array $data */ /** @var bool $submitted */ ?>
<section style="max-width:520px;margin:5rem auto;padding:0 1.5rem;">
    <?php if ($submitted): ?>
        <h1>Ya recibimos tu opinión</h1>
        <p class="text-muted">Gracias por compartirla.</p>
        <a href="<?= url('/') ?>" class="btn">← Inicio</a>
    <?php else: ?>
        <h1>Tu opinión nos importa</h1>
        <p>Hola <strong><?= e($data['patient_name']) ?></strong>, ¿qué tal estuvo tu visita?</p>
        <form method="post" action="<?= url('/cita/encuesta/'.$token) ?>" class="card card-body mt-2">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>¿Cómo calificarías tu experiencia? (0-10)</label>
                <input type="range" name="score" min="0" max="10" value="9" oninput="this.nextElementSibling.value=this.value">
                <output style="font-size:1.5rem;font-weight:700;">9</output>
            </div>
            <div class="form-group">
                <label>Comentarios (opcional)</label>
                <textarea name="comment" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar opinión</button>
        </form>
    <?php endif; ?>
</section>
