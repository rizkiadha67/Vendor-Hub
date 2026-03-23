<?php
/**
 * Template Name: NiagaHUB Filament Dashboard
 */

// Must be logged in
if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( site_url( '/dashboard' ) ) );
    exit;
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$is_vendor    = vh_is_vendor();
$is_buyer     = vh_is_buyer();
$active_tab   = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';

$company_name = get_user_meta( $user_id, 'vh_company_name', true ) ?: $current_user->display_name;
$location     = get_user_meta( $user_id, 'vh_location', true) ?: 'Indonesia';
$is_verified  = get_user_meta( $user_id, 'vh_verified', true );

$unread_count = count( get_posts( [
    'post_type'      => 'vh_message',
    'meta_key'       => '_vh_msg_to',
    'meta_value'     => $user_id,
    'posts_per_page' => -1,
] ) );

// Output standalone page — skip get_header()/get_footer()
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $company_name ); ?> — NiagaHUB Dashboard</title>
    <link rel="stylesheet" href="<?php echo includes_url('css/dashicons.min.css'); ?>">
    <?php if ($active_tab === 'vendor-ads' || $active_tab === 'adm-ads'): ?>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo get_option('vh_midtrans_client_key'); ?>"></script>
    <?php endif; ?>
    <?php wp_enqueue_media(); wp_head(); ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0 !important; padding: 0 !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f7fa;
            color: #1e293b;
        }

        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e8ecf0;
        }

        /* Hide theme elements */
        #wpadminbar, .nh-top-bar, .nh-header, #masthead, .site-header, footer.site-footer, #colophon { display: none !important; }

        /* =================== LAYOUT =================== */
        .fi-app {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: #f5f7fa;
        }

        /* =================== SIDEBAR =================== */
        .fi-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 50;
        }

        .fi-sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .fi-brand-avatar {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: white; font-size: 18px;
            flex-shrink: 0;
        }

        .fi-brand-name { color: var(--text-main); font-weight: 700; font-size: 15px; letter-spacing: -0.01em; }
        .fi-brand-role { color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; margin-top: 1px; }

        .fi-nav { padding: 1rem 0.75rem; flex: 1; }

        .fi-nav-group {
            font-size: 10px; font-weight: 700;
            color: #94a3b8; text-transform: uppercase; letter-spacing: 0.12em;
            padding: 1.25rem 0.75rem 0.4rem;
        }

        .fi-nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: var(--text-muted); font-size: 13.5px; font-weight: 500;
            text-decoration: none; transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .fi-nav-item:hover { background: #f1f5f9; color: var(--text-main); }
        .fi-nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
        .fi-nav-item .dashicons { font-size: 17px; width: 17px; height: 17px; flex-shrink: 0; }

        .fi-nav-badge {
            margin-left: auto; background: var(--primary); color: white;
            border-radius: 999px; font-size: 10px; font-weight: 700;
            padding: 1px 7px; min-width: 20px; text-align: center;
        }

        .fi-nav-item.danger { color: #ef4444; }
        .fi-nav-item.danger:hover { background: #fff1f2; color: #dc2626; }

        .fi-sidebar-footer { padding: 1rem 0.75rem; border-top: 1px solid var(--border-color); }

        /* =================== MAIN =================== */
        .fi-main {
            flex: 1; display: flex; flex-direction: column;
            min-width: 0; overflow-y: auto;
            background: #f5f7fa;
        }

        /* =================== TOPBAR =================== */
        .fi-topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky; top: 0; z-index: 40;
        }

        .fi-topbar-title { font-weight: 700; font-size: 17px; color: var(--text-main); }
        
        .fi-mobile-toggle {
            display: none;
            background: none; border: none; padding: 8px; cursor: pointer;
            color: var(--text-main); font-size: 22px;
        }

        .fi-topbar-actions { display: flex; align-items: center; gap: 0.75rem; }

        .fi-topbar-link {
            font-size: 13px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 6px; font-weight: 500;
            padding: 7px 12px; border-radius: 8px; transition: all 0.15s;
            border: 1px solid var(--border-color); background: white;
        }
        .fi-topbar-link:hover { background: #f8fafc; color: var(--text-main); }

        /* =================== CONTENT =================== */
        .fi-content { padding: 1.75rem; max-width: 1260px; margin: 0 auto; width: 100%; }

        /* =================== MOBILE RESPONSIVENESS =================== */
        @media (max-width: 1024px) {
            .fi-sidebar {
                position: fixed;
                left: 0; top: 0; bottom: 0;
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,0.08);
            }
            .fi-sidebar.open { transform: translateX(0); }
            .fi-mobile-toggle { display: block; }
            .fi-topbar { padding: 0 1.25rem; }
            .fi-sidebar-overlay { 
                display: none; position: fixed; inset: 0; 
                background: rgba(15, 23, 42, 0.3); 
                z-index: 45; 
            }
            .fi-sidebar-overlay.open { display: block; }
        }

        /* =================== WIDGETS =================== */
        .fi-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .fi-stat-card {
            background: white; border-radius: 12px;
            padding: 20px 22px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: relative; overflow: hidden;
        }

        .fi-stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 3px; height: 100%;
        }
        .fi-stat-card.orange::before { background: #f97316; }
        .fi-stat-card.blue::before   { background: #2563eb; }
        .fi-stat-card.green::before  { background: #16a34a; }

        .fi-stat-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
        .fi-stat-value { font-size: 2.2rem; font-weight: 800; color: var(--text-main); line-height: 1; margin-bottom: 8px; }
        .fi-stat-sub { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 4px; }

        /* =================== SECTION =================== */
        .fi-section {
            background: white; border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .fi-section-header {
            padding: 16px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }

        .fi-section-title { font-size: 14px; font-weight: 700; color: var(--text-main); margin: 0; }

        /* =================== TABLE =================== */
        table.fi-table { width: 100%; border-collapse: collapse; }
        table.fi-table thead tr { background: #f8fafc; }
        table.fi-table thead th {
            padding: 10px 20px; font-size: 11px; font-weight: 700;
            color: #64748b; text-transform: uppercase; letter-spacing: 0.06em;
            text-align: left;
        }
        table.fi-table tbody tr { border-top: 1px solid #f1f5f9; }
        table.fi-table tbody td { padding: 13px 20px; font-size: 13.5px; color: #374151; }
        table.fi-table tbody tr:hover { background: #fafbfc; }

        /* =================== BADGE =================== */
        .fi-badge { padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .fi-badge.green  { background: #dcfce7; color: #15803d; }
        .fi-badge.orange { background: #ffedd5; color: #c2410c; }
        .fi-badge.blue   { background: #dbeafe; color: #1d4ed8; }
        .fi-badge.red    { background: #fee2e2; color: #dc2626; }

        /* =================== FORMS =================== */
        .fi-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; }
        .fi-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .fi-input {
            width: 100%; padding: 9px 12px;
            border: 1px solid #d1d5db; border-radius: 8px;
            font-size: 13.5px; color: #111827; background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .fi-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        textarea.fi-input { resize: vertical; }

        /* =================== BUTTONS =================== */
        .fi-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: none;
            text-decoration: none; transition: all 0.15s;
        }
        .fi-btn-primary   { background: var(--primary); color: white; }
        .fi-btn-primary:hover { background: #1d4ed8; color: white; }
        .fi-btn-secondary { background: white; color: #374151; border: 1px solid #d1d5db; }
        .fi-btn-secondary:hover { background: #f9fafb; }

        /* =================== QUICK ACTIONS =================== */
        .fi-action-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .fi-action-card {
            background: white; border: 1px solid var(--border-color); border-radius: 10px;
            padding: 16px 18px; text-decoration: none; color: inherit;
            display: flex; align-items: center; gap: 12px;
            transition: box-shadow 0.15s, transform 0.15s;
        }
        .fi-action-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.07); transform: translateY(-2px); border-color: #cbd5e1; }
        .fi-action-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .fi-action-icon .dashicons { font-size: 20px; width: 20px; height: 20px; }
        .fi-action-icon.orange { background: #fff7ed; color: #f97316; }
        .fi-action-icon.blue   { background: #eff6ff; color: #2563eb; }
        .fi-action-icon.green  { background: #f0fdf4; color: #16a34a; }
        .fi-action-icon.purple { background: #f5f3ff; color: #7c3aed; }
        .fi-action-title { font-weight: 600; font-size: 13.5px; margin-bottom: 1px; color: var(--text-main); }
        .fi-action-sub { font-size: 12px; color: #94a3b8; }

        /* =================== EMPTY STATE =================== */
        .fi-empty { padding: 50px; text-align: center; }
        .fi-empty .dashicons { font-size: 2.5rem; width: 2.5rem; height: 2.5rem; opacity: 0.15; margin-bottom: 12px; }
        .fi-empty p { color: #94a3b8; font-size: 13px; margin: 0; }

        /* =================== MSG =================== */
        #fi-msg-area { min-height: 24px; font-size: 13.5px; font-weight: 600; margin-bottom: 14px; }
    </style>
</head>
<body>
<?php wp_body_open(); ?>

<div class="fi-sidebar-overlay"></div>

<div class="fi-app">

    <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
    <aside class="fi-sidebar">
        <div class="fi-sidebar-brand">
            <div class="fi-brand-avatar"><?php echo strtoupper( substr( $company_name, 0, 1 ) ); ?></div>
            <div>
                <div class="fi-brand-name"><?php echo esc_html( $company_name ); ?></div>
                <div class="fi-brand-role">
                    <?php 
                    if(current_user_can('administrator')) echo 'Super Admin';
                    elseif($is_vendor) echo 'Vendor Admin';
                    else echo 'Buyer Portal';
                    ?>
                </div>
            </div>
        </div>

        <nav class="fi-nav">
            <a href="<?php echo site_url('/dashboard'); ?>" class="fi-nav-item <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-dashboard"></span>
                <span>Beranda</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'inbox', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'inbox' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-email-alt"></span>
                <span>Pesan</span>
                <?php if ( $unread_count > 0 ): ?><span class="fi-nav-badge"><?php echo $unread_count; ?></span><?php endif; ?>
            </a>

            <?php if ( $is_vendor || $is_buyer || current_user_can('administrator') ): ?>
            <div class="fi-nav-group">Kelola</div>

            <?php if ( $is_vendor ): ?>
                <a href="<?php echo add_query_arg('tab', 'products', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo in_array($active_tab, ['products', 'add-product', 'edit-product']) ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-cart"></span>
                    <span>Produk Saya</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'tenders', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'tenders' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-clipboard"></span>
                    <span>Proyek Tender</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'leads', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'leads' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-megaphone"></span>
                    <span>Permintaan Masuk</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'vendor-ads', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'vendor-ads' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-megaphone"></span>
                    <span>Promosi & Iklan</span>
                </a>
<?php endif; ?>

            <?php if ( $is_buyer ): ?>
                <a href="<?php echo add_query_arg('tab', 'tenders', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'tenders' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-clipboard"></span>
                    <span>Tender Saya</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'add-tender', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'add-tender' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <span>Buat Tender</span>
                </a>
            <?php endif; ?>

            <?php if ( current_user_can('administrator') ): ?>
            <div class="fi-nav-group">NiagaHUB Admin</div>
            
            <a href="<?php echo add_query_arg('tab', 'adm-users', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-users' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-users"></span>
                <span>Kelola User</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-vendors', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-vendors' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-store"></span>
                <span>Vendor List</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-buyers', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-buyers' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-businessman"></span>
                <span>Buyer List</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-tenders', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-tenders' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-clipboard"></span>
                <span>Semua Tender</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-products', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-products' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-cart"></span>
                <span>Semua Produk</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-ads', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-ads' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-megaphone"></span>
                <span>Kelola Iklan</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'adm-settings', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'adm-settings' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-settings"></span>
                <span>Pengaturan Sistem</span>
            </a>
            <a href="<?php echo admin_url(); ?>" class="fi-nav-item danger">
                <span class="dashicons dashicons-wordpress-alt"></span>
                <span>WP Admin Panel</span>
            </a>
            <?php endif; ?>

            <?php endif; ?>

            <div class="fi-nav-group">Akun</div>

            <a href="<?php echo add_query_arg('tab', 'settings', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'settings' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-settings"></span>
                <span>Pengaturan</span>
            </a>
        </nav>

        <div class="fi-sidebar-footer">
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="fi-nav-item danger">
                <span class="dashicons dashicons-exit"></span>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- ═══════════════════ MAIN ═══════════════════ -->
    <main class="fi-main">

        <!-- TOPBAR -->
        <div class="fi-topbar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="fi-mobile-toggle" id="fi-menu-btn"><span class="dashicons dashicons-menu"></span></button>
                <span class="fi-topbar-title">
                    <?php
                    switch ( $active_tab ) {
                        case 'inbox':        echo 'Kotak Masuk'; break;
                        case 'products':     echo 'Katalog Produk'; break;
                        case 'add-product':  echo 'Tambah Produk Baru'; break;
                        case 'edit-product': echo 'Edit Produk'; break;
                        case 'tenders':      echo $is_buyer ? 'Tender Saya' : 'Proyek Tender'; break;
                        case 'add-tender':   echo 'Buat Tender Baru'; break;
                        case 'leads':        echo 'Daftar Permintaan Penawaran (RFQ)'; break;
                        case 'vendor-ads':   echo 'Pengelolaan Iklan & Promosi'; break;
                        case 'settings':     echo 'Pengaturan Akun'; break;
                        case 'adm-users':    echo 'Kelola Semua Pengguna'; break;
                        case 'adm-vendors':  echo 'Daftar Vendor NiagaHUB'; break;
                        case 'adm-buyers':   echo 'Daftar Buyer NiagaHUB'; break;
                        case 'adm-tenders':  echo 'Monitoring Semua Tender'; break;
                        case 'adm-products': echo 'Monitoring Semua Produk'; break;
                        case 'adm-ads':      echo 'Pengelolaan Iklan & Banner'; break;
                        case 'adm-settings': echo 'Pengaturan Sistem & Limit'; break;
                        default:             echo 'Beranda'; break;
                    }
                    ?>
                </span>
            </div>
            <div class="fi-topbar-actions">
                <?php if (class_exists('VH_Notifications')) { VH_Notifications::render_navbar_bell(); } ?>

                <a href="<?php echo home_url(); ?>" class="fi-topbar-link" target="_blank">
                    <span class="dashicons dashicons-external"></span>
                    <span class="desktop-only">Lihat Situs</span>
                </a>
                <?php if ( current_user_can('administrator') ): ?>
                <a href="<?php echo admin_url(); ?>" class="fi-topbar-link desktop-only">
                    <span class="dashicons dashicons-wordpress"></span>
                    WP Admin
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="fi-content">

            <?php if ( $active_tab === 'inbox' ) : ?>
                <div class="fi-section"><?php echo do_shortcode('[vh_inbox]'); ?></div>

            <?php elseif ( $active_tab === 'products' && $is_vendor ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title">Produk Anda</h2>
                        <a href="<?php echo add_query_arg('tab','add-product',site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary">
                            <span class="dashicons dashicons-plus" style="font-size:16px;width:16px;height:16px;"></span> Tambah Produk
                        </a>
                    </div>
                    <table class="fi-table">
                        <thead><tr>
                            <th style="width: 80px;">Foto</th><th>Produk</th><th>Harga</th><th>MOQ</th><th>Aksi</th>
                        </tr></thead>
                        <tbody>
                        <?php
                        $prods = get_posts(['post_type'=>'vh_product','author'=>$user_id,'posts_per_page'=>40, 'post_status' => 'any']);
                        if ($prods): foreach ($prods as $p):
                            $price = get_post_meta($p->ID,'_vh_price',true);
                            $moq   = get_post_meta($p->ID,'_vh_moq',true);
                            $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail') ?: 'https://via.placeholder.com/150?text=No+Image';
                        ?>
                        <tr id="product-row-<?php echo $p->ID; ?>">
                            <td><img src="<?php echo esc_url($thumb); ?>" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #f1f5f9;"></td>
                            <td><strong><?php echo esc_html(get_the_title($p->ID)); ?></strong><br><small style="color: #94a3b8;"><?php echo get_the_excerpt($p->ID); ?></small></td>
                            <td><?php echo $price ? 'Rp '.number_format($price,0,',','.') : '—'; ?></td>
                            <td><?php echo $moq ?: '1'; ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="<?php echo add_query_arg(['tab'=>'edit-product', 'id'=>$p->ID], site_url('/dashboard')); ?>" class="fi-btn fi-btn-secondary" style="padding: 6px 10px; font-size: 12px;"><span class="dashicons dashicons-edit" style="font-size: 14px; width: 14px; height: 14px;"></span></a>
                                    <button type="button" class="fi-btn fi-delete-product" data-id="<?php echo $p->ID; ?>" style="padding: 6px 10px; font-size: 12px; background: #fff1f2; color: #e11d48; border-color: #fecdd3;"><span class="dashicons dashicons-trash" style="font-size: 14px; width: 14px; height: 14px;"></span></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" style="padding:40px;text-align:center;color:#9ca3af;">Belum ada produk.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ( $active_tab === 'give-rating' && $is_buyer ) : 
                $tender_id = isset($_GET['tender_id']) ? intval($_GET['tender_id']) : 0;
                $winner_id = get_post_meta($tender_id, '_vh_tender_winner_id', true);
                if ($tender_id && $winner_id):
            ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title">Berikan Ulasan untuk: <?php echo get_userdata($winner_id)->display_name; ?></h2>
                    </div>
                    <div style="padding:0 24px 24px;">
                        <?php echo VH_Rating::render_rating_form(['vendor_id' => $winner_id, 'tender_id' => $tender_id]); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php elseif ( ( $active_tab === 'add-product' || $active_tab === 'edit-product' ) && $is_vendor ) : 
                $edit_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
                $product = $edit_id ? get_post($edit_id) : null;
                
                // Limit check for NEW product
                if (!$edit_id) {
                    $prod_limit = get_option('vh_limit_products', 1);
                    $my_prod_count = count(get_posts(['post_type'=>'vh_product', 'author'=>$user_id, 'posts_per_page'=>-1]));
                    if ($my_prod_count >= $prod_limit && get_user_meta($user_id, 'vh_verified', true) === '1'):
                ?>
                    <div class="fi-section" style="padding:40px; text-align:center;">
                        <div style="font-size:48px; color:#94a3b8; margin-bottom:16px;"><span class="dashicons dashicons-lock"></span></div>
                        <h2 style="font-size:1.5rem; font-weight:700; color:#1e293b; margin-bottom:8px;">Batas Produk Tercapai</h2>
                        <p style="color:#64748b; margin-bottom:24px;">Anda telah mengunggah <?php echo $my_prod_count; ?> dari maksimal <?php echo $prod_limit; ?> produk gratis.<br>Silakan upgrade akun atau hubungi Admin untuk menambah limit.</p>
                        <a href="<?php echo add_query_arg('tab', 'settings', site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary">Cek Paket Membership</a>
                    </div>
                <?php return; endif; }

                // Security check for edit
                if ($edit_id && (!$product || (int) $product->post_author !== (int) $user_id)) {
                    echo '<div class="vh-card">Produk tidak ditemukan atau Anda tidak memiliki akses.</div>';
                    return;
                }

                $price = $edit_id ? get_post_meta($edit_id, '_vh_price', true) : '';
                $moq   = $edit_id ? get_post_meta($edit_id, '_vh_moq', true) : '1';
                $unit  = $edit_id ? get_post_meta($edit_id, '_vh_unit', true) : 'Unit';
                $term  = $edit_id ? wp_get_post_terms($edit_id, 'vh_industry', ['fields' => 'slugs']) : [];
                $cat   = !empty($term) ? $term[0] : '';
                $img_id = $edit_id ? get_post_thumbnail_id($edit_id) : '';
                $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
            ?>
                <div class="fi-section">
                    <div class="fi-section-header"><h2 class="fi-section-title"><?php echo $edit_id ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2></div>
                    <div style="padding: 24px;">
                        <div id="fi-msg-area"></div>
                        <form id="fi-product-form">
                            <?php if($edit_id): ?><input type="hidden" name="product_id" value="<?php echo $edit_id; ?>"><?php endif; ?>
                            
                            <!-- Image Upload -->
                            <div style="margin-bottom: 24px;">
                                <label class="fi-label">Foto Produk</label>
                                <div style="display: flex; gap: 16px; align-items: center;">
                                    <div id="product-thumb-preview" style="width: 120px; height: 120px; border-radius: 12px; border: 2px dashed #e2e8f0; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                                        <?php if ($img_url): ?>
                                            <img src="<?php echo esc_url($img_url); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <span class="dashicons dashicons-format-image" style="font-size: 40px; color: #cbd5e1; width: 40px; height: 40px;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <input type="hidden" name="thumbnail_id" id="vh-product-img-id" value="<?php echo esc_attr($img_id); ?>">
                                        <button type="button" id="vh-product-img-btn" class="fi-btn fi-btn-secondary"><span class="dashicons dashicons-upload"></span> <?php echo $img_id ? 'Ganti Foto' : 'Upload Foto'; ?></button>
                                        <p style="font-size: 11px; color: #94a3b8; margin-top: 8px;">Gunakan gambar jelas rasio 1:1. Maks: 2MB.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="fi-form-grid">
                                <div style="grid-column: span 2;">
                                    <label class="fi-label">Nama Produk *</label>
                                    <input type="text" name="post_title" required class="fi-input" value="<?php echo $edit_id ? esc_attr($product->post_title) : ''; ?>" placeholder="Contoh: Lampu LED Crystal 12W">
                                </div>
                                <div>
                                    <label class="fi-label">Harga Satuan (Rp) *</label>
                                    <input type="number" name="vh_price" required class="fi-input" value="<?php echo esc_attr($price); ?>" placeholder="50000">
                                </div>
                                <div>
                                    <label class="fi-label">Kategori Industri</label>
                                    <select name="vh_industry" class="fi-input">
                                        <option value="">Pilih Kategori</option>
                                        <?php 
                                        $terms = get_terms(['taxonomy' => 'vh_industry', 'hide_empty' => false]);
                                        foreach ($terms as $t) {
                                            echo '<option value="'.esc_attr($t->slug).'" '.selected($cat, $t->slug, false).'>'.esc_html($t->name).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div><label class="fi-label">Min. Order (MOQ)</label><input type="number" name="vh_moq" class="fi-input" value="<?php echo esc_attr($moq); ?>"></div>
                                <div><label class="fi-label">Satuan</label><input type="text" name="vh_unit" class="fi-input" value="<?php echo esc_attr($unit); ?>" placeholder="Unit / Kg / Box"></div>
                            </div>
                            <div style="margin-bottom:20px;"><label class="fi-label">Deskripsi Produk</label><textarea name="post_content" rows="6" class="fi-input" placeholder="Jelaskan fitur dan spesifikasi lengkap..."><?php echo $edit_id ? esc_textarea($product->post_content) : ''; ?></textarea></div>
                            <div style="display:flex;gap:12px;">
                                <button type="submit" class="fi-btn fi-btn-primary"><span class="dashicons dashicons-yes"></span> <?php echo $edit_id ? 'Perbarui Produk' : 'Tambahkan Produk'; ?></button>
                                <a href="<?php echo add_query_arg('tab','products',site_url('/dashboard')); ?>" class="fi-btn fi-btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'leads' && $is_vendor ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title">Permintaan Penawaran (RFQ) Baru</h2>
                    </div>
                    <table class="fi-table">
                        <thead><tr><th>Produk / Subjek</th><th>Buyer</th><th>Budget</th><th>Qty</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php
                        $leads = get_posts([
                            'post_type' => 'vh_rfq',
                            'meta_query' => [
                                [ 'key' => '_vh_rfq_vendor_id', 'value' => $user_id ]
                            ],
                            'posts_per_page' => 20
                        ]);
                        if ($leads): foreach ($leads as $l):
                            $p_id = get_post_meta($l->ID, '_vh_rfq_product_id', true);
                            $b    = get_post_meta($l->ID, '_vh_rfq_budget', true);
                            $q    = get_post_meta($l->ID, '_vh_rfq_quantity', true);
                            $buyer = get_userdata($l->post_author);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($l->post_title); ?></strong><br>
                                <small style="color:#64748b;"><?php echo $p_id ? 'Terkait: ' . get_the_title($p_id) : 'Broadcast RFQ'; ?></small>
                            </td>
                            <td><?php echo $buyer ? esc_html($buyer->display_name) : 'Guest'; ?></td>
                            <td><?php echo $b ? 'Rp '.number_format($b,0,',','.') : '—'; ?></td>
                            <td><?php echo esc_html($q); ?></td>
                            <td>
                                <a href="<?php echo add_query_arg('tab', 'inbox', site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary" style="padding:6px 12px; font-size:11px;">Balas</a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" style="padding:40px;text-align:center;color:#9ca3af;">Belum ada permintaan penawaran yang masuk.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ( $active_tab === 'vendor-ads' && $is_vendor ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title">Promosi & Iklan Saya</h2>
                        <button class="fi-btn fi-btn-primary" onclick="openAdModal()">
                            <span class="dashicons dashicons-plus" style="font-size:16px;width:16px;height:16px;"></span> Ajukan Iklan
                        </button>
                    </div>

                    <!-- Ad Submission Modal -->
                    <div id="ad-modal" class="fi-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                        <div class="fi-modal-content" style="background:white; padding:32px; border-radius:16px; width:90%; max-width:800px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
                            <button onclick="closeAdModal()" style="position:absolute; top:20px; right:20px; border:none; background:none; font-size:24px; cursor:pointer;">&times;</button>
                            <h2 style="margin-bottom:24px; font-size:1.5rem; font-weight:700;">Ajukan Iklan Baru</h2>
                            
                            <form id="form-submit-ad" enctype="multipart/form-data">
                                <?php wp_nonce_field('vh-auth-nonce', 'security'); ?>
                                <div style="margin-bottom:20px;">
                                    <label class="fi-label">Nama / Judul Iklan *</label>
                                    <input type="text" name="ad_title" required class="fi-input" placeholder="Contoh: Promo Ramadhan 2024">
                                </div>

                                <div style="margin-bottom:24px;">
                                    <label class="fi-label" style="margin-bottom:12px; display:block;">Pilih Paket Iklan *</label>
                                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                                        <label class="pkg-card">
                                            <input type="radio" name="ad_package" value="standar" required style="display:none;">
                                            <div class="pkg-inner">
                                                <div class="pkg-name">Standar</div>
                                                <div class="pkg-price">Rp 50rb<span> / minggu</span></div>
                                                <ul class="pkg-features">
                                                    <li>Sidebar Placement</li>
                                                    <li>7 Hari Tayang</li>
                                                </ul>
                                            </div>
                                        </label>
                                        <label class="pkg-card">
                                            <input type="radio" name="ad_package" value="premium" required style="display:none;">
                                            <div class="pkg-inner">
                                                <div class="pkg-name">Premium</div>
                                                <div class="pkg-price">Rp 150rb<span> / 2 minggu</span></div>
                                                <ul class="pkg-features">
                                                    <li>Search Results</li>
                                                    <li>14 Hari Tayang</li>
                                                    <li>Badge Khusus</li>
                                                </ul>
                                            </div>
                                        </label>
                                        <label class="pkg-card">
                                            <input type="radio" name="ad_package" value="platinum" required style="display:none;">
                                            <div class="pkg-inner">
                                                <div class="pkg-name">Platinum</div>
                                                <div class="pkg-price">Rp 500rb<span> / bulan</span></div>
                                                <ul class="pkg-features">
                                                    <li>Homepage Banner</li>
                                                    <li>30 Hari Tayang</li>
                                                    <li>Prioritas Utama</li>
                                                </ul>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div style="margin-bottom:24px;">
                                    <label class="fi-label">Hubungkan ke Produk / Tender (Opsional)</label>
                                    <select name="ad_link_id" class="fi-input">
                                        <option value="">-- Pilih --</option>
                                        <optgroup label="Produk Saya">
                                            <?php
                                            $my_prods = get_posts(['post_type'=>'vh_product', 'author'=>$user_id, 'posts_per_page'=>-1]);
                                            foreach ($my_prods as $p) echo '<option value="'.$p->ID.'">Produk: '.$p->post_title.'</option>';
                                            ?>
                                        </optgroup>
                                        <optgroup label="Tender Saya">
                                            <?php
                                            $my_tenders = get_posts(['post_type'=>'vh_tender', 'author'=>$user_id, 'posts_per_page'=>-1]);
                                            foreach ($my_tenders as $t) echo '<option value="'.$t->ID.'">Tender: '.$t->post_title.'</option>';
                                            ?>
                                        </optgroup>
                                    </select>
                                    <small style="color:#64748b;">Pilih item yang ingin Anda promosikan dengan iklan ini.</small>
                                </div>

                                <div style="margin-bottom:24px;">
                                    <label class="fi-label">Unggah Banner (Image) *</label>
                                    <input type="file" name="ad_banner" required accept="image/*" class="fi-input" style="padding:10px;">
                                    <small style="color:#64748b;">Ukuran rek: 1200x400 (Platinum) atau 300x600 (Sidebar)</small>
                                </div>

                                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px;">
                                    <button type="button" onclick="closeAdModal()" class="fi-btn fi-btn-secondary">Batal</button>
                                    <button type="submit" class="fi-btn fi-btn-primary">Kirim Pengajuan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <style>
                        .pkg-card { cursor: pointer; }
                        .pkg-inner { border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; transition: all 0.2s; height: 100%; }
                        .pkg-card input:checked + .pkg-inner { border-color: var(--primary); background: #f0f9ff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
                        .pkg-name { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 8px; }
                        .pkg-price { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin-bottom: 16px; }
                        .pkg-price span { font-size: 0.85rem; color: #64748b; font-weight: 400; }
                        .pkg-features { padding: 0; margin: 0; list-style: none; font-size: 0.85rem; color: #64748b; }
                        .pkg-features li { margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
                        .pkg-features li::before { content: "✓"; color: #10b981; font-weight: 700; }
                    </style>

                    <script>
                        function openAdModal() { document.getElementById('ad-modal').style.display = 'flex'; }
                        function closeAdModal() { document.getElementById('ad-modal').style.display = 'none'; }
                        
                        document.getElementById('form-submit-ad').onsubmit = function(e) {
                            e.preventDefault();
                            var form = this;
                            var formData = new FormData(form);
                            formData.append('action', 'vh_vendor_submit_ad');
                            
                            var btn = form.querySelector('button[type="submit"]');
                            btn.innerHTML = 'Mengirim...';
                            btn.disabled = true;

                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.data);
                                    location.reload();
                                } else {
                                    alert(data.data);
                                    btn.innerHTML = 'Kirim Pengajuan';
                                    btn.disabled = false;
                                }
                            });
                        };
                    </script>
                    <div style="padding:24px;">
                        <div class="fi-alert" style="background:#eff6ff; border-color:#bfdbfe; color:#1e40af; margin-bottom:20px;">
                            <span class="dashicons dashicons-info" style="font-size:18px; width:18px; margin-right:8px;"></span>
                            Tingkatkan visibilitas produk Anda dengan fitur Iklan Premium NiagaHUB.
                        </div>
                        <table class="fi-table">
                            <thead><tr><th>Nama Iklan</th><th>Paket</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                            <?php
                            $ads = get_posts(['post_type'=>'vh_ad', 'author'=>$user_id, 'posts_per_page'=>-1, 'post_status'=>'any']);
                            if ($ads): foreach ($ads as $a):
                                $package = get_post_meta($a->ID, '_vh_ad_package', true) ?: 'standar';
                                $stat = $a->post_status;
                                $price = get_post_meta($a->ID, '_vh_ad_price', true) ?: 50000;
                            ?>
                            <tr id="ad-row-<?php echo $a->ID; ?>">
                                <td>
                                    <strong><?php echo esc_html($a->post_title); ?></strong><br>
                                    <small style="color:#64748b;">Rp <?php echo number_format($price, 0, ',', '.'); ?></small>
                                </td>
                                <td><span class="fi-badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;"><?php echo strtoupper($package); ?></span></td>
                                <td>
                                    <?php if ($stat == 'unpaid'): ?>
                                        <span class="fi-badge" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3;">Belum Bayar</span>
                                    <?php elseif ($stat == 'pending'): ?>
                                        <span class="fi-badge orange">Menunggu Persetujuan</span>
                                    <?php elseif ($stat == 'publish'): ?>
                                        <span class="fi-badge green">Aktif / Tayang</span>
                                    <?php else: ?>
                                        <span class="fi-badge gray"><?php echo ucfirst($stat); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($stat == 'unpaid'): ?>
                                    <button class="fi-btn fi-btn-primary" onclick="payAd(<?php echo $a->ID; ?>)" style="padding:6px 12px; font-size:11px;">
                                        <span class="dashicons dashicons-money-alt"></span> Bayar
                                    </button>
                                    <?php else: ?>
                                    —
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" style="padding:40px;text-align:center;color:#9ca3af;">Anda belum memiliki iklan.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <script>
                        function payAd(adId) {
                            var btn = event.currentTarget;
                            var originalContent = btn.innerHTML;
                            btn.disabled = true;
                            btn.innerHTML = '<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span>...';

                            var formData = new FormData();
                            formData.append('action', 'vh_get_snap_token');
                            formData.append('ad_id', adId);
                            formData.append('security', '<?php echo wp_create_nonce("vh-auth-nonce"); ?>');

                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(res => {
                                if (res.success) {
                                    snap.pay(res.data.token, {
                                        onSuccess: function(result) { location.reload(); },
                                        onPending: function(result) { location.reload(); },
                                        onError: function(result) { alert("Pembayaran gagal!"); btn.disabled = false; btn.innerHTML = originalContent; },
                                        onClose: function() { btn.disabled = false; btn.innerHTML = originalContent; }
                                    });
                                } else {
                                    alert(res.data);
                                    btn.disabled = false;
                                    btn.innerHTML = originalContent;
                                }
                            });
                        }
                        </script>
                        <style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'tenders' ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title"><?php echo $is_buyer ? 'Tender Anda' : 'Tender Tersedia'; ?></h2>
                        <?php if ( $is_buyer ): ?>
                        <a href="<?php echo add_query_arg('tab','add-tender',site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary">
                            <span class="dashicons dashicons-plus" style="font-size:16px;width:16px;height:16px;"></span> Buat Tender
                        </a>
                        <?php endif; ?>
                    </div>
                    <table class="fi-table">
                        <thead><tr><th>Proyek</th><th>Budget</th><th>Deadline</th><th>Proposal</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php
                        $targs = ['post_type'=>'vh_tender','posts_per_page'=>15];
                        if($is_buyer) $targs['author'] = $user_id;
                        $tenders = get_posts($targs);
                        if($tenders): foreach($tenders as $t):
                            $budget   = get_post_meta($t->ID,'_vh_tender_budget',true);
                            $deadline = get_post_meta($t->ID,'_vh_tender_deadline',true);
                            $status   = get_post_meta($t->ID,'_vh_tender_status',true) ?: 'open';
                            
                            // Proposal count
                            $props_count = count(get_posts([
                                'post_type' => 'vh_proposal',
                                'meta_key' => '_vh_proposal_tender_id',
                                'meta_value' => $t->ID,
                                'posts_per_page' => -1
                            ]));
                            $stat_badge = ($status === 'open') ? 'blue' : (($status === 'awarded') ? 'green' : 'orange');
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html(get_the_title($t->ID)); ?></strong><br><small style="color:#9ca3af;"><?php echo get_the_date('',$t->ID); ?></small></td>
                            <td><?php echo $budget ? 'Rp '.number_format($budget,0,',','.') : '—'; ?></td>
                            <td style="color:#ef4444;font-weight:500;"><?php echo $deadline ?: '—'; ?></td>
                            <td>
                                <span class="fi-badge <?php echo $props_count > 0 ? 'blue' : 'gray'; ?>" style="background:<?php echo $props_count > 0 ? '#dbeafe' : '#f1f5f9'; ?>; color:<?php echo $props_count > 0 ? '#1d4ed8' : '#64748b'; ?>;">
                                    <?php echo $props_count; ?> Masuk
                                </span>
                            </td>
                            <td><span class="fi-badge <?php echo $stat_badge; ?>"><?php echo ucfirst($status); ?></span></td>
                            <td>
                                <div style="display:flex;gap:5px;align-items:center;">
                                    <a href="<?php echo get_permalink($t->ID); ?>" class="fi-btn fi-btn-secondary" style="padding:5px 12px;font-size:12px;">Lihat</a>
                                    <?php 
                                    if ($is_buyer && $status === 'awarded'): 
                                        $winner_id = get_post_meta($t->ID, '_vh_tender_winner_id', true);
                                        $has_review = get_posts([
                                            'post_type' => 'vh_review',
                                            'author' => $user_id,
                                            'meta_query' => [['key' => '_vh_review_tender_id', 'value' => $t->ID]],
                                            'numberposts' => 1
                                        ]);
                                        if (!$has_review && $winner_id):
                                    ?>
                                        <a href="<?php echo add_query_arg(['tab' => 'give-rating', 'tender_id' => $t->ID], site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary" style="padding:5px 12px;font-size:12px;background:#f59e0b;border-color:#f59e0b;">Beri Rating</a>
                                    <?php endif; endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5"><div class="fi-empty"><span class="dashicons dashicons-clipboard"></span><p>Tidak ada tender ditemukan.</p></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ( $active_tab === 'add-tender' && $is_buyer ) : 
                $tender_limit = get_option('vh_limit_tenders', 1);
                $my_tender_count = count(get_posts(['post_type'=>'vh_tender', 'author'=>$user_id, 'posts_per_page'=>-1]));
                if ($my_tender_count >= $tender_limit && get_user_meta($user_id, 'vh_verified', true) === '1'):
            ?>
                <div class="fi-section" style="padding:40px; text-align:center;">
                    <div style="font-size:48px; color:#94a3b8; margin-bottom:16px;"><span class="dashicons dashicons-lock"></span></div>
                    <h2 style="font-size:1.5rem; font-weight:700; color:#1e293b; margin-bottom:8px;">Batas Tender Tercapai</h2>
                    <p style="color:#64748b; margin-bottom:24px;">Anda telah membuat <?php echo $my_tender_count; ?> dari maksimal <?php echo $tender_limit; ?> tender gratis.<br>Silakan upgrade akun atau hubungi Admin untuk menambah limit.</p>
                    <a href="<?php echo add_query_arg('tab', 'settings', site_url('/dashboard')); ?>" class="fi-btn fi-btn-primary">Upgrade Akun</a>
                </div>
            <?php else: ?>
                <div class="fi-section">
                    <div class="fi-section-header"><h2 class="fi-section-title">Buat Tender / Pengadaan Baru</h2></div>
                    <div style="padding: 24px;">
                        <div id="fi-msg-area"></div>
                        <form id="fi-add-tender-form">
                            <div style="margin-bottom:20px;"><label class="fi-label">Judul Proyek *</label><input type="text" name="post_title" required class="fi-input" placeholder="Contoh: Pengadaan 50 Unit Server Rack"></div>
                            <div class="fi-form-grid">
                                <div><label class="fi-label">Estimasi Budget (Rp)</label><input type="number" name="vh_tender_budget" class="fi-input" placeholder="500000000"></div>
                                <div><label class="fi-label">Deadline Penawaran</label><input type="date" name="vh_tender_deadline" class="fi-input" min="<?php echo date('Y-m-d'); ?>"></div>
                            </div>
                            <div style="margin-bottom:20px;"><label class="fi-label">Rincian Kebutuhan</label><textarea name="post_content" rows="6" class="fi-input" placeholder="Jelaskan spesifikasi secara rinci..."></textarea></div>
                            <div style="display:flex;gap:12px;">
                                <button type="submit" class="fi-btn fi-btn-primary"><span class="dashicons dashicons-clipboard" style="font-size:16px;width:16px;height:16px;"></span> Terbitkan Tender</button>
                                <a href="<?php echo add_query_arg('tab','tenders',site_url('/dashboard')); ?>" class="fi-btn fi-btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php elseif ( $active_tab === 'adm-settings' && current_user_can('administrator') ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header"><h2 class="fi-section-title">Konfigurasi Limit Platform</h2></div>
                    <div style="padding:24px; max-width:600px;">
                        <form id="form-vh-settings">
                            <?php wp_nonce_field('vh-auth-nonce', 'security'); ?>
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Limit Produk Gratis (Akun Terverifikasi)</label>
                                <input type="number" name="vh_limit_products" value="<?php echo get_option('vh_limit_products', 1); ?>" class="fi-input" min="1">
                            </div>
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Limit Tender Gratis (Akun Terverifikasi)</label>
                                <input type="number" name="vh_limit_tenders" value="<?php echo get_option('vh_limit_tenders', 1); ?>" class="fi-input" min="1">
                            </div>

                            <hr style="margin: 24px 0; border: none; border-top: 1px solid #e2e8f0;">
                            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#1e293b;">Google API Configuration</h3>
                            
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Google Client ID</label>
                                <input type="text" name="vh_google_client_id" value="<?php echo esc_attr(get_option('vh_google_client_id')); ?>" class="fi-input" placeholder="Paste Client ID here">
                            </div>
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Google Client Secret</label>
                                <input type="password" name="vh_google_client_secret" value="<?php echo esc_attr(get_option('vh_google_client_secret')); ?>" class="fi-input" placeholder="Paste Client Secret here">
                            </div>

                            <hr style="margin: 24px 0; border: none; border-top: 1px solid #e2e8f0;">
                            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#1e293b;">Midtrans Payment Configuration</h3>
                            
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Midtrans Client Key</label>
                                <input type="text" name="vh_midtrans_client_key" value="<?php echo esc_attr(get_option('vh_midtrans_client_key')); ?>" class="fi-input" placeholder="Paste Client Key">
                            </div>
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Midtrans Server Key</label>
                                <input type="password" name="vh_midtrans_server_key" value="<?php echo esc_attr(get_option('vh_midtrans_server_key')); ?>" class="fi-input" placeholder="Paste Server Key">
                            </div>
                            <div style="margin-bottom:24px;">
                                <label class="fi-label">Midtrans Mode</label>
                                <select name="vh_midtrans_mode" class="fi-input">
                                    <option value="sandbox" <?php selected(get_option('vh_midtrans_mode', 'sandbox'), 'sandbox'); ?>>Sandbox (Testing)</option>
                                    <option value="production" <?php selected(get_option('vh_midtrans_mode'), 'production'); ?>>Production (Live)</option>
                                </select>
                            </div>

                            <hr style="margin: 24px 0; border: none; border-top: 1px solid #e2e8f0;">
                            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; color:#1e293b;">SMTP & Email Configuration</h3>
                            
                            <div class="fi-form-grid">
                                <div style="grid-column: span 2;">
                                    <label class="fi-label">SMTP Host</label>
                                    <input type="text" name="vh_smtp_host" value="<?php echo esc_attr(get_option('vh_smtp_host')); ?>" class="fi-input" placeholder="smtp.gmail.com">
                                </div>
                                <div>
                                    <label class="fi-label">SMTP Port</label>
                                    <input type="number" name="vh_smtp_port" value="<?php echo esc_attr(get_option('vh_smtp_port', 587)); ?>" class="fi-input">
                                </div>
                                <div>
                                    <label class="fi-label">Encryption</label>
                                    <select name="vh_smtp_encryption" class="fi-input">
                                        <option value="none" <?php selected(get_option('vh_smtp_encryption'), 'none'); ?>>None</option>
                                        <option value="ssl" <?php selected(get_option('vh_smtp_encryption'), 'ssl'); ?>>SSL</option>
                                        <option value="tls" <?php selected(get_option('vh_smtp_encryption', 'tls'), 'tls'); ?>>TLS</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="fi-label">SMTP Username</label>
                                    <input type="text" name="vh_smtp_user" value="<?php echo esc_attr(get_option('vh_smtp_user')); ?>" class="fi-input">
                                </div>
                                <div>
                                    <label class="fi-label">SMTP Password</label>
                                    <input type="password" name="vh_smtp_pass" value="<?php echo esc_attr(get_option('vh_smtp_pass')); ?>" class="fi-input">
                                </div>
                                <div>
                                    <label class="fi-label">From Email</label>
                                    <input type="email" name="vh_from_email" value="<?php echo esc_attr(get_option('vh_from_email', get_option('admin_email'))); ?>" class="fi-input">
                                </div>
                                <div>
                                    <label class="fi-label">From Name</label>
                                    <input type="text" name="vh_from_name" value="<?php echo esc_attr(get_option('vh_from_name', get_bloginfo('name'))); ?>" class="fi-input">
                                </div>
                            </div>

                            <button type="submit" class="fi-btn fi-btn-primary">Simpan Pengaturan</button>
                        </form>
                    </div>
                </div>
                <script>
                document.getElementById('form-vh-settings').onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    formData.append('action', 'vh_admin_save_settings');
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
                    .then(res => res.json()).then(data => { alert(data.data.message || data.data); if(data.success) location.reload(); });
                };
                </script>

            <?php elseif ( $active_tab === 'settings' ) : ?>
            <div class="fi-section">
                    <div style="padding: 24px;">
                        <div id="fi-msg-area"></div>
                        <form id="fi-profile-form">
                                       <?php
                            $vendor_logo_id  = get_user_meta( $user_id, 'vh_vendor_logo', true );
                            $vendor_logo_url = $vendor_logo_id ? wp_get_attachment_image_url( $vendor_logo_id, 'thumbnail' ) : '';
                            
                            // Load additional meta
                            $tagline       = get_user_meta( $user_id, 'vh_tagline', true );
                            $description   = get_user_meta( $user_id, 'vh_description', true );
                            $business_type = get_user_meta( $user_id, 'vh_business_type', true );
                            $nib           = get_user_meta( $user_id, 'vh_nib', true );
                            $industry      = get_user_meta( $user_id, 'vh_industry', true );
                            $wa_number     = get_user_meta( $user_id, 'vh_wa_number', true );
                            $biz_email     = get_user_meta( $user_id, 'vh_business_email', true );
                            $website       = get_user_meta( $user_id, 'vh_website', true );
                            $address       = get_user_meta( $user_id, 'vh_address', true );
                            $biz_scale     = get_user_meta( $user_id, 'vh_biz_scale', true );
                            $est_year      = get_user_meta( $user_id, 'vh_est_year', true );
                            ?>


                            <!-- Logo Upload -->
                            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
                                <div id="vh-logo-preview" style="width: 80px; height: 80px; border-radius: 12px; border: 1px solid var(--border-color); background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                                    <?php if ( $vendor_logo_url ) : ?>
                                        <img id="vh-logo-img" src="<?php echo esc_url($vendor_logo_url); ?>" style="width:100%;height:100%;object-fit:cover;">
                                    <?php else : ?>
                                        <span class="dashicons dashicons-businessman" style="font-size:36px;width:36px;height:36px;color:#cbd5e1;"></span>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-main); margin-bottom: 3px;"><?php _e('Logo Perusahaan', 'vendorhub'); ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px;"><?php _e('Tampil di profil publik. Format JPG/PNG, maks 2MB.', 'vendorhub'); ?></div>
                                    <input type="hidden" name="vh_vendor_logo" id="vh-logo-input" value="<?php echo esc_attr( $vendor_logo_id ); ?>">
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" id="vh-logo-picker" class="fi-btn fi-btn-secondary" style="font-size:12px;padding:6px 14px;">
                                            <span class="dashicons dashicons-upload" style="font-size:14px;width:14px;height:14px;"></span>
                                            <?php echo $vendor_logo_url ? __('Ganti Logo', 'vendorhub') : __('Upload Logo', 'vendorhub'); ?>
                                        </button>
                                        <?php if ( $vendor_logo_url ) : ?>
                                        <button type="button" id="vh-logo-remove" class="fi-btn" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px;"><?php _e('Hapus', 'vendorhub'); ?></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr; gap: 28px;">
                                <!-- Dasar Perusahaan -->
                                <section>
                                    <h3 style="font-size: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                        <span class="dashicons dashicons-admin-home" style="font-size: 14px; width:14px;height:14px;"></span> <?php _e('Informasi Dasar', 'vendorhub'); ?>
                                    </h3>
                                    <div class="fi-form-grid">
                                        <div>
                                            <label class="fi-label"><?php _e('Nama Tampilan', 'vendorhub'); ?></label>
                                            <input type="text" name="display_name" class="fi-input" value="<?php echo esc_attr($current_user->display_name); ?>" placeholder="Nama di Dashboard">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Nama Perusahaan', 'vendorhub'); ?></label>
                                            <input type="text" name="vh_company_name" class="fi-input" value="<?php echo esc_attr($company_name); ?>" placeholder="Nama Resmi Perusahaan">
                                        </div>
                                        <div style="grid-column: span 2;">
                                            <label class="fi-label"><?php _e('Tagline Bisnis', 'vendorhub'); ?></label>
                                            <input type="text" name="vh_tagline" class="fi-input" value="<?php echo esc_attr($tagline); ?>" placeholder="Slogan perusahaan Anda...">
                                        </div>
                                        <div style="grid-column: span 2;">
                                            <label class="fi-label"><?php _e('Deskripsi Perusahaan', 'vendorhub'); ?></label>
                                            <textarea name="vh_description" class="fi-input" rows="4" placeholder="Ceritakan sejarah, visi, dan misi perusahaan Anda secara detail..."><?php echo esc_textarea($description); ?></textarea>
                                        </div>
                                    </div>
                                </section>

                                <!-- Legalitas & Industri -->
                                <section>
                                    <h3 style="font-size: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                        <span class="dashicons dashicons-shield-alt" style="font-size: 14px; width:14px;height:14px;"></span> <?php _e('Legalitas & Kategori', 'vendorhub'); ?>
                                    </h3>
                                    <div class="fi-form-grid">
                                        <div>
                                            <label class="fi-label"><?php _e('Tipe Badan Usaha', 'vendorhub'); ?></label>
                                            <select name="vh_business_type" class="fi-input">
                                                <option value="PT" <?php selected($business_type, 'PT'); ?>>PT (Perseroan Terbatas)</option>
                                                <option value="CV" <?php selected($business_type, 'CV'); ?>>CV (Commanditaire Vennootschap)</option>
                                                <option value="Koperasi" <?php selected($business_type, 'Koperasi'); ?>>Koperasi</option>
                                                <option value="UMKM" <?php selected($business_type, 'UMKM'); ?>>UMKM / Perorangan</option>
                                                <option value="Firma" <?php selected($business_type, 'Firma'); ?>>Firma</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Nomor NIB', 'vendorhub'); ?></label>
                                            <input type="text" name="vh_nib" class="fi-input" value="<?php echo esc_attr($nib); ?>" placeholder="0123456789...">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Bidang Industri', 'vendorhub'); ?></label>
                                            <select name="vh_industry" class="fi-input">
                                                <?php 
                                                $terms = get_terms(['taxonomy' => 'vh_industry', 'hide_empty' => false]);
                                                foreach ($terms as $term) {
                                                    echo '<option value="'.esc_attr($term->slug).'" '.selected($industry, $term->slug, false).'>'.esc_html($term->name).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Skala Bisnis', 'vendorhub'); ?></label>
                                            <select name="vh_biz_scale" class="fi-input">
                                                <option value="Kecil" <?php selected($biz_scale, 'Kecil'); ?>><?php _e('Kecil (Mikro)', 'vendorhub'); ?></option>
                                                <option value="Menengah" <?php selected($biz_scale, 'Menengah'); ?>><?php _e('Menengah', 'vendorhub'); ?></option>
                                                <option value="Besar" <?php selected($biz_scale, 'Besar'); ?>><?php _e('Besar (Korporasi)', 'vendorhub'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </section>

                                <!-- Kontak & Lokasi -->
                                <section>
                                    <h3 style="font-size: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                        <span class="dashicons dashicons-location-alt" style="font-size: 14px; width:14px;height:14px;"></span> <?php _e('Kontak & Lokasi', 'vendorhub'); ?>
                                    </h3>
                                    <div class="fi-form-grid">
                                        <div>
                                            <label class="fi-label"><?php _e('Email Bisnis', 'vendorhub'); ?></label>
                                            <input type="email" name="vh_business_email" class="fi-input" value="<?php echo esc_attr($biz_email); ?>" placeholder="email@perusahaan.com">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('WhatsApp Bisnis', 'vendorhub'); ?></label>
                                            <input type="text" name="vh_wa_number" class="fi-input" value="<?php echo esc_attr($wa_number); ?>" placeholder="0812...">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Website', 'vendorhub'); ?></label>
                                            <input type="url" name="vh_website" class="fi-input" value="<?php echo esc_attr($website); ?>" placeholder="https://...">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Tahun Berdiri', 'vendorhub'); ?></label>
                                            <input type="number" name="vh_est_year" class="fi-input" value="<?php echo esc_attr($est_year); ?>" placeholder="Contoh: 2010">
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Kota/Provinsi', 'vendorhub'); ?></label>
                                            <input type="text" name="vh_location" class="fi-input" value="<?php echo esc_attr($location); ?>" placeholder="Jakarta, Indonesia">
                                        </div>
                                        <div style="grid-column: span 2;">
                                            <label class="fi-label"><?php _e('Alamat Lengkap Kantor', 'vendorhub'); ?></label>
                                            <textarea name="vh_address" class="fi-input" rows="3" placeholder="Jl. Nama Jalan No. 123..."><?php echo esc_textarea($address); ?></textarea>
                                        </div>
                                    </div>
                                </section>

                                <!-- Keamanan -->
                                <section>
                                    <h3 style="font-size: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                        <span class="dashicons dashicons-lock" style="font-size: 14px; width:14px;height:14px;"></span> <?php _e('Keamanan Akun', 'vendorhub'); ?>
                                    </h3>
                                    <div class="fi-form-grid">
                                        <div>
                                            <label class="fi-label"><?php _e('Email Akun (Login)', 'vendorhub'); ?></label>
                                            <input type="email" class="fi-input" value="<?php echo esc_attr($current_user->user_email); ?>" disabled style="background:#fef3c7; cursor:not-allowed; border-color: #fcd34d;">
                                            <p style="font-size: 11px; color: #b45309; margin-top: 4px;"><?php _e('Email login tidak dapat diubah sendiri.', 'vendorhub'); ?></p>
                                        </div>
                                        <div>
                                            <label class="fi-label"><?php _e('Ganti Password', 'vendorhub'); ?></label>
                                            <input type="password" name="user_pass" class="fi-input" placeholder="Isi hanya jika ingin mengganti" style="border-color: #fcd34d;">
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div style="margin-top: 40px; padding-top: 24px; border-top: 2px solid #f1f5f9; display: flex; justify-content: flex-end;">
                                <button type="submit" class="fi-btn fi-btn-primary" style="padding: 12px 32px; font-size: 15px; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.4);">
                                    <span class="dashicons dashicons-saved" style="font-size: 18px;"></span> <?php _e('Simpan Semua Perubahan', 'vendorhub'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ( strpos($active_tab, 'adm-') === 0 && current_user_can('administrator') ) : ?>
                <div class="fi-section">
                    <div class="fi-section-header">
                        <h2 class="fi-section-title">
                            <?php 
                            if($active_tab == 'adm-users') echo 'Semua Pengguna';
                            if($active_tab == 'adm-vendors') echo 'Data Vendor';
                            if($active_tab == 'adm-buyers') echo 'Data Buyer';
                            if($active_tab == 'adm-tenders') echo 'Semua Tender Proyek';
                            if($active_tab == 'adm-products') echo 'Semua Produk Marketplace';
                            if($active_tab == 'adm-ads') echo 'Manajemen Iklan & Banner';
                            ?>
                        </h2>
                        <?php if ($active_tab == 'adm-users'): ?>
                            <button id="btn-add-user" class="fi-btn fi-btn-primary" style="padding: 8px 16px; font-size: 13px;">
                                <span class="dashicons dashicons-plus-alt" style="font-size:16px; width:16px; height:16px;"></span> Tambah Pengguna
                            </button>
                        <?php endif; ?>
                    </div>
                    <table class="fi-table">
                        <?php if ($active_tab == 'adm-users' || $active_tab == 'adm-vendors' || $active_tab == 'adm-buyers'): ?>
                            <thead><tr><th>User</th><th>Email</th><th>Role/Status</th><th>Joined</th><th>Aksi</th></tr></thead>
                            <tbody>
                            <?php
                            $u_args = ['number' => 100];
                            if($active_tab == 'adm-vendors') $u_args['meta_key'] = 'vh_is_vendor';
                            if($active_tab == 'adm-buyers') $u_args['meta_key'] = 'vh_is_buyer';
                            
                            $users = get_users($u_args);
                            foreach($users as $u):
                                $role = implode(', ', $u->roles);
                                $is_v = get_user_meta($u->ID, 'vh_is_vendor', true);
                                $is_b = get_user_meta($u->ID, 'vh_is_buyer', true);
                                $label = $is_v ? '<span class="fi-badge orange">Vendor</span>' : ($is_b ? '<span class="fi-badge blue">Buyer</span>' : '<span class="fi-badge">User</span>');
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($u->display_name); ?></strong><br><small>@<?php echo esc_html($u->user_login); ?></small></td>
                                <td><?php echo esc_html($u->user_email); ?></td>
                                <td><?php echo $label; ?><br><small><?php echo ucfirst($role); ?></small></td>
                                <td><?php echo date('d M Y', strtotime($u->user_registered)); ?></td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <a href="<?php echo get_edit_user_link($u->ID); ?>" class="fi-btn fi-btn-secondary" style="padding:4px 8px; font-size:11px;" target="_blank">Edit</a>
                                        <?php if ($is_v): 
                                            $v_status = get_user_meta($u->ID, 'vh_verified', true);
                                            $btn_class = ($v_status == '1') ? 'fi-btn-danger' : 'fi-btn-primary';
                                            $btn_text = ($v_status == '1') ? 'Unverify' : 'Verify';
                                        ?>
                                            <button class="fi-btn <?php echo $btn_class; ?> btn-verify-vendor" data-user-id="<?php echo $u->ID; ?>" style="padding:4px 8px; font-size:11px;">
                                                <?php echo $btn_text; ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>

                        <?php elseif ($active_tab == 'adm-tenders'): ?>
                            <thead><tr><th>Proyek</th><th>Author</th><th>Budget</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                            <?php
                            $tenders = get_posts(['post_type'=>'vh_tender','posts_per_page'=>100]);
                            foreach($tenders as $t):
                                $author = get_userdata($t->post_author);
                                $budget = get_post_meta($t->ID,'_vh_tender_budget',true);
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html(get_the_title($t->ID)); ?></strong><br><small><?php echo get_the_date('d M Y',$t->ID); ?></small></td>
                                <td><?php echo $author ? esc_html($author->display_name) : '—'; ?></td>
                                <td><?php echo $budget ? 'Rp '.number_format($budget,0,',','.') : '—'; ?></td>
                                <td><span class="fi-badge green">Active</span></td>
                                <td><a href="<?php echo get_permalink($t->ID); ?>" class="fi-btn fi-btn-secondary" style="padding:4px 8px; font-size:11px;" target="_blank">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>

                        <?php elseif ($active_tab == 'adm-products'): ?>
                            <thead><tr><th>Produk</th><th>Vendor</th><th>Harga</th><th>MOQ</th><th>Aksi</th></tr></thead>
                            <tbody>
                            <?php
                            $prods = get_posts(['post_type'=>'vh_product','posts_per_page'=>100]);
                            foreach($prods as $p):
                                $vendor = get_userdata($p->post_author);
                                $price = get_post_meta($p->ID,'_vh_price',true);
                                $moq = get_post_meta($p->ID,'_vh_moq',true);
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html(get_the_title($p->ID)); ?></strong></td>
                                <td><?php echo $vendor ? esc_html($vendor->display_name) : '—'; ?></td>
                                <td><?php echo $price ? 'Rp '.number_format($price,0,',','.') : '—'; ?></td>
                                <td><?php echo $moq ?: '1'; ?></td>
                                <td><a href="<?php echo get_permalink($p->ID); ?>" class="fi-btn fi-btn-secondary" style="padding:4px 8px; font-size:11px;" target="_blank">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <?php elseif ($active_tab == 'adm-ads'): ?>
                            <thead><tr><th>Preview</th><th>Ad Title / Vendor</th><th>Package</th><th>Duration</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                            <?php
                            $ads = get_posts(['post_type'=>'vh_ad','posts_per_page'=>40, 'post_status'=>'any']);
                            if ($ads): foreach ($ads as $ad):
                                $pkg    = get_post_meta($ad->ID, '_vh_ad_package', true);
                                $dur    = get_post_meta($ad->ID, '_vh_ad_duration', true);
                                $vendor = get_userdata($ad->post_author);
                                $status = $ad->post_status;
                                $thumb  = get_the_post_thumbnail_url($ad->ID, 'medium') ?: 'https://via.placeholder.com/400x150?text=No+Banner';
                                 $badge_color = ($status == 'publish') ? 'green' : (($status == 'pending') ? 'orange' : (($status == 'unpaid') ? 'red' : 'gray'));
                                 $status_labels = ['publish'=>'Aktif', 'pending'=>'Paid / Pending', 'unpaid'=>'Belum Bayar'];
                                 $display_status = $status_labels[$status] ?? ucfirst($status);
                                ?>
                                <tr id="ad-row-<?php echo $ad->ID; ?>">
                                    <td style="width:160px;"><img src="<?php echo esc_url($thumb); ?>" style="width:140px; height:50px; border-radius:6px; object-fit:cover; border:1px solid #eee;"></td>
                                    <td>
                                        <strong><?php echo esc_html(get_the_title($ad->ID)); ?></strong><br>
                                        <small style="color:#64748b;">Vendor: <?php echo $vendor ? esc_html($vendor->display_name) : '—'; ?></small>
                                    </td>
                                    <td><span class="fi-badge" style="background:#f1f5f9; color:#1e293b;"><?php echo strtoupper($pkg ?: 'Standar'); ?></span></td>
                                    <td><?php echo $dur ?: '7'; ?> Hari</td>
                                    <td><span class="fi-badge <?php echo $badge_color; ?>"><?php echo $display_status; ?></span></td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <?php if($status == 'pending'): ?>
                                        <button class="fi-btn fi-btn-primary btn-ad-action" data-id="<?php echo $ad->ID; ?>" data-action="approve" style="padding:4px 8px; font-size:11px;">Setujui</button>
                                        <?php endif; ?>
                                        <button class="fi-btn btn-ad-action" data-id="<?php echo $ad->ID; ?>" data-action="delete" style="padding:4px 8px; font-size:11px; background:#fff1f2; color:#e11d48; border-color:#fecdd3;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="6" style="padding:40px;text-align:center;color:#9ca3af;">Belum ada pengajuan iklan.</td></tr>
                            <?php endif; ?>
                            </tbody>
                            <script>
                            document.querySelectorAll('.btn-ad-action').forEach(btn => {
                                btn.onclick = function() {
                                    var id = this.dataset.id;
                                    var act = this.dataset.action;
                                    if(act === 'delete' && !confirm('Hapus iklan ini?')) return;
                                    
                                    var formData = new FormData();
                                    formData.append('action', 'vh_admin_manage_ad');
                                    formData.append('ad_id', id);
                                    formData.append('ad_action', act);
                                    formData.append('security', '<?php echo wp_create_nonce("vh-auth-nonce"); ?>');

                                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
                                    .then(res => res.json()).then(data => {
                                        if(data.success) {
                                            if(act === 'delete') document.getElementById('ad-row-'+id).remove();
                                            else location.reload();
                                        } else alert(data.data);
                                    });
                                }
                            });
                            </script>
                        <?php endif; ?>
                    </table>
                </div>

            <?php else : /* DASHBOARD OVERVIEW */ ?>
                <!-- Stats Row -->
                <div class="fi-stat-grid">
                    <?php
                    $count_type = $is_vendor ? 'vh_product' : 'vh_tender';
                    $total = wp_count_posts($count_type)->publish ?? 0;
                    ?>
                    <div class="fi-stat-card orange">
                        <div class="fi-stat-label"><?php echo $is_vendor ? 'Total Produk' : 'Tender Dibuat'; ?></div>
                        <div class="fi-stat-value"><?php echo $total; ?></div>
                        <div class="fi-stat-sub"><span class="dashicons dashicons-products" style="font-size:15px;width:15px;height:15px;"></span> <?php echo $total; ?> item aktif</div>
                    </div>
                    <div class="fi-stat-card blue">
                        <div class="fi-stat-label">Pesan Masuk</div>
                        <div class="fi-stat-value"><?php echo $unread_count; ?></div>
                        <div class="fi-stat-sub"><span class="dashicons dashicons-email" style="font-size:15px;width:15px;height:15px;"></span> Pesan belum dibaca</div>
                    </div>
                    <div class="fi-stat-card green">
                        <div class="fi-stat-label">Status Akun</div>
                        <div class="fi-stat-value" style="font-size:1.4rem;"><?php echo $is_verified ? 'Verified ✓' : 'Standard'; ?></div>
                        <div class="fi-stat-sub"><span class="dashicons dashicons-shield" style="font-size:15px;width:15px;height:15px;"></span> <?php echo $is_vendor ? 'Vendor' : 'Buyer'; ?> Member</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="fi-section" style="padding: 22px 24px; margin-bottom: 24px;">
                    <h2 class="fi-section-title" style="margin-bottom: 18px;">Aksi Cepat</h2>
                    <div class="fi-action-grid">
                        <?php if ( $is_vendor ): ?>
                        <a href="<?php echo add_query_arg('tab','add-product',site_url('/dashboard')); ?>" class="fi-action-card">
                            <div class="fi-action-icon orange"><span class="dashicons dashicons-plus-alt"></span></div>
                            <div><div class="fi-action-title">Tambah Produk Baru</div><div class="fi-action-sub">Upload katalog produk ke marketplace</div></div>
                        </a>
                        <a href="<?php echo add_query_arg('tab','tenders',site_url('/dashboard')); ?>" class="fi-action-card">
                            <div class="fi-action-icon blue"><span class="dashicons dashicons-clipboard"></span></div>
                            <div><div class="fi-action-title">Cari Proyek Tender</div><div class="fi-action-sub">Temukan proyek yang sesuai keahlianmu</div></div>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo add_query_arg('tab','add-tender',site_url('/dashboard')); ?>" class="fi-action-card">
                            <div class="fi-action-icon orange"><span class="dashicons dashicons-plus-alt"></span></div>
                            <div><div class="fi-action-title">Buat Tender Baru</div><div class="fi-action-sub">Umumkan kebutuhan pengadaan</div></div>
                        </a>
                        <a href="<?php echo site_url('/marketplace-vendor'); ?>" class="fi-action-card">
                            <div class="fi-action-icon green"><span class="dashicons dashicons-businessman"></span></div>
                            <div><div class="fi-action-title">Cari Vendor</div><div class="fi-action-sub">Temukan vendor terverifikasi</div></div>
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo add_query_arg('tab','inbox',site_url('/dashboard')); ?>" class="fi-action-card">
                            <div class="fi-action-icon purple"><span class="dashicons dashicons-email-alt"></span></div>
                            <div><div class="fi-action-title">Pesan Masuk <?php if($unread_count>0): ?><span class="fi-badge orange" style="font-size:10px;"><?php echo $unread_count; ?></span><?php endif; ?></div><div class="fi-action-sub">Buka kotak pesan Anda</div></div>
                        </a>
                        <a href="<?php echo add_query_arg('tab','settings',site_url('/dashboard')); ?>" class="fi-action-card">
                            <div class="fi-action-icon blue"><span class="dashicons dashicons-admin-settings"></span></div>
                            <div><div class="fi-action-title">Pengaturan Akun</div><div class="fi-action-sub">Update profil & password</div></div>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- /fi-content -->
    </main>
</div><!-- /fi-app -->

<script>
jQuery(document).ready(function($){
    var ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';
    var nonce   = '<?php echo esc_js(wp_create_nonce('vh-auth-nonce')); ?>';

    function fiForm(formId, action) {
        $(formId).on('submit', function(e){
            e.preventDefault();
            var $msg = $('#fi-msg-area');
            $msg.html('<span style="color:#64748b;">⏳ Menyimpan...</span>');
            $.post(ajaxUrl, $(this).serialize() + '&action=' + action + '&security=' + nonce, function(res){
                if (res.success) {
                    $msg.html('<span style="color:#16a34a;">✅ ' + res.data.message + '</span>');
                    if (res.data.redirect) setTimeout(function(){ window.location.href = res.data.redirect; }, 1200);
                } else {
                    $msg.html('<span style="color:#dc2626;">❌ ' + res.data + '</span>');
                }
            });
        });
    }

    fiForm('#fi-product-form', 'vh_save_product');
    fiForm('#fi-add-tender-form',  'vh_add_tender');
    fiForm('#fi-profile-form',     'vh_save_profile');

    // Product Delete
    $('.fi-delete-product').on('click', function(){
        if (!confirm('Hapus produk ini secara permanen?')) return;
        var id = $(this).data('id');
        var $row = $('#product-row-' + id);
        $.post(ajaxUrl, {action: 'vh_delete_product', id: id, security: nonce}, function(res){
            if (res.success) {
                $row.fadeOut(function(){ $(this).remove(); });
            } else {
                alert(res.data);
            }
        });
    });

    // Product Image Upload
    var productFrame;
    $('#vh-product-img-btn').on('click', function(e){
        e.preventDefault();
        if (productFrame) { productFrame.open(); return; }
        productFrame = wp.media({
            title: 'Pilih Foto Produk',
            button: { text: 'Gunakan Foto Ini' },
            multiple: false
        });
        productFrame.on('select', function(){
            var attachment = productFrame.state().get('selection').first().toJSON();
            $('#vh-product-img-id').val(attachment.id);
            $('#product-thumb-preview').html('<img src="'+attachment.url+'" style="width:100%;height:100%;object-fit:cover;">');
        });
        productFrame.open();
    });

    // ——— WordPress Media Library Logo Picker ———
    var mediaFrame;

    $('#vh-logo-picker').on('click', function(e){
        e.preventDefault();

        if ( mediaFrame ) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title:    'Pilih Logo Perusahaan',
            button:   { text: 'Gunakan Gambar Ini' },
            multiple: false,
            library:  { type: 'image' }
        });

        mediaFrame.on('select', function(){
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            $('#vh-logo-input').val( attachment.id );

            // Update preview
            var $preview = $('#vh-logo-preview');
            $preview.html('<img id="vh-logo-img" src="' + attachment.url + '" style="width:100%;height:100%;object-fit:cover;">');
            $('#vh-logo-picker').html('<span class="dashicons dashicons-upload" style="font-size:16px;width:16px;height:16px;"></span> Ganti Logo');

            // Show remove button if not already there
            if ( !$('#vh-logo-remove').length ) {
                $('#vh-logo-picker').after('<button type="button" id="vh-logo-remove" class="fi-btn" style="background:transparent;color:#ef4444;border:1px solid #fecaca;margin-left:8px;">Hapus</button>');
                bindRemove();
            }
        });

        mediaFrame.open();
    });

    // Inbox Reply Handler
    $(document).on('click', '.fi-msg-reply-btn', function(){
        const $btn = $(this);
        const $textarea = $('.fi-msg-reply-bar textarea');
        const content = $textarea.val().trim();
        const msgId = window.current_msg_id;

        if (!content) return;
        if (!msgId) { alert('Pilih pesan terlebih dahulu.'); return; }

        if (typeof fi_msgs === 'undefined') return;
        const msgObj = fi_msgs.find(m => m.id == msgId);
        if (!msgObj) return;

        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Kirim...');
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'vh_send_message',
                to_user_id: msgObj.author_id,
                message: content,
                thread_id: msgObj.thread_raw,
                security: nonce
            },
            success: function(res) {
                if (res.success) {
                    $textarea.val('');
                    alert('Balasan terkirim!');
                    location.reload(); 
                } else {
                    alert('Gagal: ' + res.data);
                }
            },
            error: function() {
                alert('Terjadi kesalahan koneksi.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-paperplane"></span> Kirim');
            }
        });
    });

    // Ad Payment Handler
    window.payAd = function(adId) {
        if (typeof snap === 'undefined') {
            alert('Layanan pembayaran (Midtrans) sedang memuat. Silakan tunggu sebentar atau muat ulang halaman.');
            return;
        }

        const $btn = $(`#ad-row-${adId} button[onclick^="payAd"]`);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Menyiapkan...');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'vh_get_snap_token',
                ad_id: adId,
                security: nonce
            },
            success: function(res) {
                if (res.success && res.data.token) {
                    snap.pay(res.data.token, {
                        onSuccess: function(result) {
                            alert('Pembayaran Berhasil! Iklan akan segera dikonfirmasi oleh Admin.');
                            location.reload();
                        },
                        onPending: function(result) {
                            alert('Pembayaran Menunggu: Silakan selesaikan pembayaran Anda sesuai instruksi Midtrans.');
                            location.reload();
                        },
                        onError: function(result) {
                            alert('Pembayaran Gagal: ' + result.status_message);
                            $btn.prop('disabled', false).html(originalHtml);
                        },
                        onClose: function() {
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                } else {
                    alert('Gagal: ' + (res.data || 'Terjadi kesalahan saat membuat token pembayaran.'));
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                alert('Terjadi kesalahan koneksi ke server.');
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    };

    bindRemove();
});
</script>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<?php wp_footer(); ?>
    <!-- ═══════════════════ SCRIPTS ═══════════════════ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('fi-menu-btn');
            const sidebar = document.querySelector('.fi-sidebar');
            const overlay = document.querySelector('.fi-sidebar-overlay');

            if (menuBtn && sidebar && overlay) {
                const toggleMenu = () => {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('open');
                    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
                };

                menuBtn.addEventListener('click', toggleMenu);
                overlay.addEventListener('click', toggleMenu);
            }
        });
    </script>
<?php if (current_user_can('administrator')): ?>
<!-- Add User Modal -->
<div id="modal-add-user" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:#fff; width:450px; border-radius:12px; padding:32px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0; font-size:20px; color:#1e293b;">Tambah Pengguna Baru</h3>
        <form id="form-admin-add-user">
            <div style="margin-bottom:16px;">
                <label class="fi-label">Username</label>
                <input type="text" name="user_login" class="fi-input" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="fi-label">Display Name</label>
                <input type="text" name="display_name" class="fi-input">
            </div>
            <div style="margin-bottom:16px;">
                <label class="fi-label">Email</label>
                <input type="email" name="user_email" class="fi-input" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="fi-label">Password</label>
                <input type="password" name="user_pass" class="fi-input" required minlength="6">
            </div>
            <div style="margin-bottom:24px;">
                <label class="fi-label">Role</label>
                <select name="user_role" class="fi-input" required>
                    <option value="vendor">Vendor</option>
                    <option value="buyer">Buyer</option>
                    <option value="administrator">Administrator</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" id="btn-close-modal" class="fi-btn fi-btn-secondary">Batal</button>
                <button type="submit" class="fi-btn fi-btn-primary">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-add-user');
    const btnAdd = document.getElementById('btn-add-user');
    const btnClose = document.getElementById('btn-close-modal');
    const formAdd = document.getElementById('form-admin-add-user');

    if (btnAdd) btnAdd.onclick = () => modal.style.display = 'flex';
    if (btnClose) btnClose.onclick = () => modal.style.display = 'none';

    // Handle User Creation
    if (formAdd) {
        formAdd.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'vh_admin_create_user');
            formData.append('security', vh_auth_obj.nonce);

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memproses...';

            fetch(vh_auth_obj.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.data.message);
                    location.reload();
                } else {
                    alert(data.data);
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Buat Akun';
                }
            });
        };
    }

    // Handle Vendor Verification
    document.querySelectorAll('.btn-verify-vendor').forEach(btn => {
        btn.onclick = function() {
            const userId = this.dataset.userId;
            const originalText = this.innerText;
            this.disabled = true;
            this.innerText = '...';

            const formData = new FormData();
            formData.append('action', 'vh_admin_toggle_verify');
            formData.append('user_id', userId);
            formData.append('security', vh_auth_obj.nonce);

            fetch(vh_auth_obj.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.data);
                    this.disabled = false;
                    this.innerText = originalText;
                }
            });
        };
    });
});
</script>
<?php endif; ?>
</body>
</html>
