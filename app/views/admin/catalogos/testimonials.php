<?php $pageTitle = 'Testimonios'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Reseñas que aparecen en la web pública</div>
    <button class="btn-admin" onclick="document.getElementById('tForm').classList.toggle('hidden')">+ Nuevo</button>
</div>

<div id="tForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/testimonios/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Autor *</label><input type="text" name="author" required></div>
            <div class="form-group">
                <label>Rating</label>
                <select name="rating"><?php for ($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= str_repeat('★',$i) ?></option><?php endfor; ?></select>
            </div>
            <div class="form-group" style="grid-column:1/-1;"><label>Comentario *</label><textarea name="comment" required rows="3"></textarea></div>
            <div class="form-group"><label><input type="checkbox" name="published" checked> Publicado</label></div>
        </div>
        <button class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <?php foreach ($items as $t): ?>
        <div style="padding:14px 0;border-bottom:1px solid var(--border);">
            <div style="color:var(--accent);"><?= str_repeat('★', (int)$t['rating']) ?></div>
            <p style="margin:6px 0;font-style:italic;">"<?= e($t['comment']) ?>"</p>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <strong><?= e($t['author']) ?></strong>
                <div>
                    <?= $t['published'] ? '<span class="badge badge-success">Publicado</span>' : '<span class="badge badge-secondary">Oculto</span>' ?>
                    <form method="post" action="<?= url('/admin/catalogos/testimonios/'.$t['id'].'/eliminar') ?>" data-confirm="¿Eliminar?" style="display:inline;margin-left:8px;">
                        <?= csrf_field() ?>
                        <button class="btn-icon danger">✕</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>.hidden{display:none}</style>
<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
