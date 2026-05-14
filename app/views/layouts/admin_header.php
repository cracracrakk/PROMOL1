<?php
// Layout admin - requiere autenticación
Auth::requireLogin();
$user = Auth::user();
$pageTitle = $pageTitle ?? 'Panel';
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?= e($_SESSION['theme'] ?? 'light') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e(brand_name()) ?></title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
    <style>
        :root {
            --brand: <?= e(setting('color_primary', '#6b8a7a')) ?>;
            --brand-dark: <?= e(setting('color_primary_dark', '#4a6356')) ?>;
            --accent: <?= e(setting('color_accent', '#c9a96e')) ?>;
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <?php if (brand_logo()): ?>
                <img src="<?= brand_logo() ?>" alt="">
            <?php endif; ?>
            <span><?= e(brand_name()) ?></span>
        </div>

        <div class="sidebar-section">Operaciones</div>
        <ul class="sidebar-menu">
            <li><a href="<?= url('/admin') ?>" class="<?= activeNav('/admin/dashboard') ?: (rtrim($_SERVER['REQUEST_URI'], '/') === '/admin' ? 'active' : '') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10"/></svg>
                Dashboard
            </a></li>
            <li><a href="<?= url('/admin/citas/agenda') ?>" class="<?= activeNav('/admin/citas') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Agenda
            </a></li>
            <li><a href="<?= url('/admin/pos') ?>" class="<?= activeNav('/admin/pos') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                POS / Caja rápida
            </a></li>
            <li><a href="<?= url('/admin/clientes') ?>" class="<?= activeNav('/admin/clientes') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Clientes
            </a></li>
            <li><a href="<?= url('/admin/servicios') ?>" class="<?= activeNav('/admin/servicios') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7l3-7z"/></svg>
                Servicios
            </a></li>
            <li><a href="<?= url('/admin/inventario') ?>" class="<?= activeNav('/admin/inventario') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/></svg>
                Inventario
            </a></li>
            <li><a href="<?= url('/admin/facturas') ?>" class="<?= activeNav('/admin/facturas') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                Facturación
            </a></li>
            <li><a href="<?= url('/admin/bonos') ?>" class="<?= activeNav('/admin/bonos') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                Bonos y Regalos
            </a></li>
            <li><a href="<?= url('/admin/caja') ?>" class="<?= activeNav('/admin/caja') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>
                Caja Diaria
            </a></li>
            <li><a href="<?= url('/admin/reportes') ?>" class="<?= activeNav('/admin/reportes') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 9l-5 5-4-4-3 3"/></svg>
                Reportes
            </a></li>

            <?php if (Auth::role() === 'admin'): ?>
            <div class="sidebar-section">Sistema</div>
            <li><a href="<?= url('/admin/sistema') ?>" class="<?= activeNav('/admin/sistema') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Configuración
            </a></li>
            <li><a href="<?= url('/admin/sistema/usuarios') ?>" class="<?= activeNav('/admin/sistema/usuarios') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Usuarios y Roles
            </a></li>
            <li><a href="<?= url('/admin/sistema/sar') ?>" class="<?= activeNav('/admin/sistema/sar') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                SAR / CAI
            </a></li>
            <li><a href="<?= url('/admin/sistema/auditoria') ?>" class="<?= activeNav('/admin/sistema/auditoria') ?>">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><circle cx="11.5" cy="14.5" r="2.5"/><path d="M13.27 16.27L15 18"/></svg>
                Auditoría
            </a></li>
            <?php endif; ?>
        </ul>

        <div style="height:40px;"></div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="topbar-btn" id="sidebarToggle" aria-label="Menú">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:22px;height:22px;"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
                <h1 class="topbar-title"><?= e($pageTitle) ?></h1>
            </div>
            <div class="topbar-actions">
                <button class="topbar-btn" id="themeToggle" aria-label="Tema" title="Cambiar tema">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
                <div class="user-menu">
                    <div class="user-avatar" onclick="document.getElementById('userDropdown').classList.toggle('open')">
                        <?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div style="padding: 10px 14px; border-bottom: 1px solid var(--border); margin-bottom: 6px;">
                            <strong><?= e($user['name']) ?></strong>
                            <div style="font-size:.78rem;color:var(--muted);"><?= e(ucfirst($user['role'])) ?></div>
                        </div>
                        <a href="<?= url('/admin/sistema/perfil') ?>">Mi perfil</a>
                        <a href="<?= url('/logout') ?>">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('info')): ?>
            <div class="alert alert-info"><?= e($msg) ?></div>
        <?php endif; ?>
