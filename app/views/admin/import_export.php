<?php $pageTitle = 'Importar / Exportar'; require dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<div class="page-header"><div class="breadcrumb">Backup y migración de datos</div></div>

<div class="card-grid">
    <div class="card">
        <h3>👥 Clientes</h3>
        <p style="color:var(--muted);margin:14px 0;">Exporta tu base de clientes a CSV o importa desde otro sistema.</p>
        <a href="<?= url('/admin/import-export/clientes/exportar') ?>" class="btn-admin btn-block">📥 Exportar todos</a>
        <form method="post" action="<?= url('/admin/import-export/clientes/importar') ?>" enctype="multipart/form-data" style="margin-top:14px;">
            <?= csrf_field() ?>
            <input type="file" name="csv" accept=".csv" required style="margin-bottom:8px;">
            <button class="btn-admin btn-outline btn-block">Importar CSV</button>
        </form>
    </div>

    <div class="card">
        <h3>📦 Productos</h3>
        <p style="color:var(--muted);margin:14px 0;">Exporta o importa el catálogo del inventario.</p>
        <a href="<?= url('/admin/import-export/productos/exportar') ?>" class="btn-admin btn-block">📥 Exportar todos</a>
        <form method="post" action="<?= url('/admin/import-export/productos/importar') ?>" enctype="multipart/form-data" style="margin-top:14px;">
            <?= csrf_field() ?>
            <input type="file" name="csv" accept=".csv" required style="margin-bottom:8px;">
            <button class="btn-admin btn-outline btn-block">Importar CSV</button>
        </form>
    </div>
</div>

<div class="card">
    <h3>Formato CSV esperado</h3>
    <p style="color:var(--muted);">Separador: <code>;</code> · Codificación: UTF-8 · Cabecera obligatoria en primera fila.</p>
    <p><strong>Clientes:</strong> <code>nombre;apellidos;email;telefono;rtn;fecha_nacimiento;direccion;ciudad;vip;marketing;notas</code></p>
    <p><strong>Productos:</strong> <code>sku;nombre;descripcion;precio_coste;precio_venta;isv;stock;stock_min;unidad</code></p>
</div>

<?php require dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
