<?php $pageTitle = 'Galería'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Imágenes para la web pública</div>
</div>

<div class="card">
    <form method="post" action="<?= url('/admin/catalogos/galeria/subir') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Imagen *</label><input type="file" name="image" accept="image/*" required></div>
            <div class="form-group"><label>Pie de foto</label><input type="text" name="caption"></div>
            <div class="form-group"><label>Orden</label><input type="number" name="sort_order" value="0"></div>
        </div>
        <button class="btn-admin">Subir imagen</button>
    </form>
</div>

<div class="card">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
        <?php foreach ($items as $g): ?>
            <div style="background:var(--bg);border-radius:8px;overflow:hidden;">
                <img src="<?= asset('uploads/' . $g['image']) ?>" style="width:100%;height:160px;object-fit:cover;display:block;">
                <div style="padding:10px;">
                    <small style="display:block;color:var(--muted);"><?= e($g['caption'] ?? '') ?></small>
                    <form method="post" action="<?= url('/admin/catalogos/galeria/'.$g['id'].'/eliminar') ?>" data-confirm="¿Eliminar imagen?" style="margin-top:6px;">
                        <?= csrf_field() ?>
                        <button class="btn-icon danger">✕ Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
