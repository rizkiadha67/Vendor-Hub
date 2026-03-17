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
    <?php wp_enqueue_media(); wp_head(); ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        :root {
            --primary: #f97316;
            --secondary: #1e293b;
            --sidebar-bg: #0f172a;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
        }

        /* Hide theme elements */
        #wpadminbar, .nh-top-bar, .nh-header, #masthead, .site-header, footer.site-footer, #colophon { display: none !important; }

        /* =================== LAYOUT =================== */
        .fi-app {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
        }

        /* =================== SIDEBAR =================== */
        .fi-sidebar {
            width: 280px;
            min-width: 280px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 50;
        }

        .fi-sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .fi-brand-avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary), #ea580c);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: white; font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .fi-brand-name { color: white; font-weight: 800; font-size: 16px; letter-spacing: -0.02em; }
        .fi-brand-role { color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }

        .fi-nav { padding: 1.5rem 1rem; flex: 1; }

        .fi-nav-group {
            font-size: 10px; font-weight: 800;
            color: #475569; text-transform: uppercase; letter-spacing: 0.15em;
            padding: 1.5rem 1rem 0.5rem;
        }

        .fi-nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 12px;
            color: #94a3b8; font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 4px;
        }

        .fi-nav-item:hover { background: rgba(255,255,255,0.05); color: white; transform: translateX(4px); }
        .fi-nav-item.active { background: rgba(249,115,22,0.1); color: var(--primary); }
        .fi-nav-item .dashicons { font-size: 18px; width: 18px; height: 18px; }

        .fi-nav-badge {
            margin-left: auto; background: var(--primary); color: white;
            border-radius: 999px; font-size: 10px; font-weight: 800;
            padding: 2px 8px; min-width: 22px; text-align: center;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);
        }

        .fi-nav-item.danger { color: #fb7185; }
        .fi-nav-item.danger:hover { background: rgba(251,113,133,0.1); }

        .fi-sidebar-footer { padding: 1.5rem 1rem; border-top: 1px solid rgba(255,255,255,0.05); }

        /* =================== MAIN =================== */
        .fi-main {
            flex: 1; display: flex; flex-direction: column;
            min-width: 0; overflow-y: auto;
            background: #f8fafc;
        }

        /* =================== TOPBAR =================== */
        .fi-topbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            height: 72px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            position: sticky; top: 0; z-index: 40;
        }

        .fi-topbar-title { font-weight: 800; font-size: 18px; color: var(--text-main); letter-spacing: -0.02em; }
        
        .fi-mobile-toggle {
            display: none;
            background: none; border: none; padding: 8px; cursor: pointer;
            color: var(--text-main); font-size: 24px;
        }

        .fi-topbar-actions { display: flex; align-items: center; gap: 1rem; }

        .fi-topbar-link {
            font-size: 13px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 8px; font-weight: 600;
            padding: 8px 14px; border-radius: 10px; transition: all 0.2s;
            border: 1px solid transparent;
        }
        .fi-topbar-link:hover { background: white; border-color: var(--border-color); color: var(--text-main); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }

        /* =================== CONTENT =================== */
        .fi-content { padding: 2rem; max-width: 1280px; margin: 0 auto; width: 100%; }

        /* =================== MOBILE RESPONSIVENESS =================== */
        @media (max-width: 1024px) {
            .fi-sidebar {
                position: fixed;
                left: 0; top: 0; bottom: 0;
                transform: translateX(-100%);
            }
            .fi-sidebar.open { transform: translateX(0); }
            .fi-mobile-toggle { display: block; }
            .fi-topbar { padding: 0 1.25rem; }
            .fi-sidebar-overlay { 
                display: none; position: fixed; inset: 0; 
                background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); 
                z-index: 45; 
            }
            .fi-sidebar-overlay.open { display: block; }
        }

        /* =================== WIDGETS =================== */
        .fi-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .fi-stat-card {
            background: white; border-radius: 14px;
            padding: 22px 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            position: relative; overflow: hidden;
        }

        .fi-stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 4px; height: 100%;
        }
        .fi-stat-card.orange::before { background: #f97316; }
        .fi-stat-card.blue::before   { background: #3b82f6; }
        .fi-stat-card.green::before  { background: #22c55e; }

        .fi-stat-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; }
        .fi-stat-value { font-size: 2.4rem; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 10px; }
        .fi-stat-sub { font-size: 13px; color: #94a3b8; display: flex; align-items: center; gap: 5px; }

        /* =================== SECTION =================== */
        .fi-section {
            background: white; border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .fi-section-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }

        .fi-section-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }

        /* =================== TABLE =================== */
        table.fi-table { width: 100%; border-collapse: collapse; }
        table.fi-table thead tr { background: #f8fafc; }
        table.fi-table thead th {
            padding: 11px 22px; font-size: 11px; font-weight: 700;
            color: #64748b; text-transform: uppercase; letter-spacing: 0.06em;
            text-align: left;
        }
        table.fi-table tbody tr { border-top: 1px solid #f1f5f9; }
        table.fi-table tbody td { padding: 14px 22px; font-size: 14px; color: #374151; }
        table.fi-table tbody tr:hover { background: #fafafa; }

        /* =================== BADGE =================== */
        .fi-badge { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .fi-badge.green  { background: #dcfce7; color: #15803d; }
        .fi-badge.orange { background: #ffedd5; color: #c2410c; }
        .fi-badge.blue   { background: #dbeafe; color: #1d4ed8; }

        /* =================== FORMS =================== */
        .fi-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }
        .fi-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .fi-input {
            width: 100%; padding: 9px 13px;
            border: 1px solid #d1d5db; border-radius: 8px;
            font-size: 14px; color: #111827; background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .fi-input:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.15); }
        textarea.fi-input { resize: vertical; }

        /* =================== BUTTONS =================== */
        .fi-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 18px; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: none;
            text-decoration: none; transition: all 0.15s;
        }
        .fi-btn-primary   { background: #f97316; color: white; }
        .fi-btn-primary:hover { background: #ea580c; color: white; }
        .fi-btn-secondary { background: white; color: #374151; border: 1px solid #d1d5db; }
        .fi-btn-secondary:hover { background: #f9fafb; }

        /* =================== QUICK ACTIONS =================== */
        .fi-action-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .fi-action-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 18px 22px; text-decoration: none; color: inherit;
            display: flex; align-items: center; gap: 14px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .fi-action-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .fi-action-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .fi-action-icon .dashicons { font-size: 22px; width: 22px; height: 22px; }
        .fi-action-icon.orange { background: #ffedd5; color: #f97316; }
        .fi-action-icon.blue   { background: #dbeafe; color: #3b82f6; }
        .fi-action-icon.green  { background: #dcfce7; color: #16a34a; }
        .fi-action-icon.purple { background: #ede9fe; color: #7c3aed; }
        .fi-action-title { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
        .fi-action-sub { font-size: 12px; color: #94a3b8; }

        /* =================== EMPTY STATE =================== */
        .fi-empty { padding: 60px; text-align: center; }
        .fi-empty .dashicons { font-size: 3rem; width: 3rem; height: 3rem; opacity: 0.2; margin-bottom: 16px; }
        .fi-empty p { color: #94a3b8; font-size: 14px; margin: 0; }

        /* =================== MSG =================== */
        #fi-msg-area { min-height: 28px; font-size: 14px; font-weight: 600; margin-bottom: 16px; }
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
                <div class="fi-brand-role"><?php echo $is_vendor ? 'Vendor Admin' : 'Buyer Portal'; ?></div>
            </div>
        </div>

        <nav class="fi-nav">
            <a href="<?php echo site_url('/dashboard'); ?>" class="fi-nav-item <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-dashboard"></span>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo add_query_arg('tab', 'inbox', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'inbox' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-email-alt"></span>
                <span>Messages</span>
                <?php if ( $unread_count > 0 ): ?><span class="fi-nav-badge"><?php echo $unread_count; ?></span><?php endif; ?>
            </a>

            <div class="fi-nav-group">Management</div>

            <?php if ( $is_vendor ): ?>
                <a href="<?php echo add_query_arg('tab', 'products', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo in_array($active_tab, ['products', 'add-product', 'edit-product']) ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-cart"></span>
                    <span>My Products</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'tenders', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'tenders' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-clipboard"></span>
                    <span>Tender Projects</span>
                </a>
            <?php endif; ?>

            <?php if ( $is_buyer ): ?>
                <a href="<?php echo add_query_arg('tab', 'tenders', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'tenders' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-clipboard"></span>
                    <span>My Tenders</span>
                </a>
                <a href="<?php echo add_query_arg('tab', 'add-tender', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'add-tender' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <span>Post Tender</span>
                </a>
            <?php endif; ?>

            <div class="fi-nav-group">System</div>

            <a href="<?php echo add_query_arg('tab', 'settings', site_url('/dashboard')); ?>" class="fi-nav-item <?php echo $active_tab == 'settings' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-settings"></span>
                <span>Account Settings</span>
            </a>
        </nav>

        <div class="fi-sidebar-footer">
            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="fi-nav-item danger">
                <span class="dashicons dashicons-exit"></span>
                <span>Sign Out</span>
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
                        case 'inbox':       echo 'Inbox Messages'; break;
                        case 'products':    echo 'Product Catalog'; break;
                        case 'add-product': echo 'Add New Product'; break;
                        case 'edit-product': echo 'Edit Product'; break;
                        case 'tenders':     echo $is_buyer ? 'My Tenders' : 'Tender Projects'; break;
                        case 'add-tender':  echo 'Create New Tender'; break;
                        case 'settings':    echo 'Account Settings'; break;
                        default:            echo 'Overview'; break;
                    }
                    ?>
                </span>
            </div>
            <div class="fi-topbar-actions">
                <a href="<?php echo home_url(); ?>" class="fi-topbar-link" target="_blank">
                    <span class="dashicons dashicons-external"></span>
                    <span class="desktop-only">View Site</span>
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

            <?php elseif ( ( $active_tab === 'add-product' || $active_tab === 'edit-product' ) && $is_vendor ) : 
                $edit_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
                $product = $edit_id ? get_post($edit_id) : null;
                
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
                        <thead><tr><th>Proyek</th><th>Budget</th><th>Deadline</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php
                        $targs = ['post_type'=>'vh_tender','posts_per_page'=>15];
                        if($is_buyer) $targs['author'] = $user_id;
                        $tenders = get_posts($targs);
                        if($tenders): foreach($tenders as $t):
                            $budget   = get_post_meta($t->ID,'_vh_tender_budget',true);
                            $deadline = get_post_meta($t->ID,'_vh_tender_deadline',true);
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html(get_the_title($t->ID)); ?></strong><br><small style="color:#9ca3af;"><?php echo get_the_date('',$t->ID); ?></small></td>
                            <td><?php echo $budget ? 'Rp '.number_format($budget,0,',','.') : '—'; ?></td>
                            <td style="color:#ef4444;font-weight:500;"><?php echo $deadline ?: '—'; ?></td>
                            <td><span class="fi-badge green">Open</span></td>
                            <td><a href="<?php echo get_permalink($t->ID); ?>" class="fi-btn fi-btn-secondary" style="padding:5px 12px;font-size:12px;">Lihat</a></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5"><div class="fi-empty"><span class="dashicons dashicons-clipboard"></span><p>Tidak ada tender ditemukan.</p></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ( $active_tab === 'add-tender' && $is_buyer ) : ?>
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

            <?php elseif ( $active_tab === 'settings' ) : ?>
            <div class="fi-section">
                    <div class="fi-section-header"><h2 class="fi-section-title">Pengaturan Akun</h2></div>
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
                            <div style="margin-bottom: 32px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <label class="fi-label"><?php _e('Logo Perusahaan', 'vendorhub'); ?></label>
                                <p style="font-size: 13px; color: #94a3b8; margin: 0 0 16px;"><?php _e('Logo akan tampil pada profil publik Anda.', 'vendorhub'); ?></p>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <div id="vh-logo-preview" style="width: 100px; height: 100px; border-radius: 16px; border: 2px dashed #e2e8f0; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                                        <?php if ( $vendor_logo_url ) : ?>
                                            <img id="vh-logo-img" src="<?php echo esc_url($vendor_logo_url); ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <?php else : ?>
                                            <span class="dashicons dashicons-format-image" style="font-size:40px;color:#cbd5e1;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <input type="hidden" name="vh_vendor_logo" id="vh-logo-input" value="<?php echo esc_attr( $vendor_logo_id ); ?>">
                                        <div style="display: flex; gap: 8px;">
                                            <button type="button" id="vh-logo-picker" class="fi-btn fi-btn-secondary">
                                                <span class="dashicons dashicons-upload"></span> <?php echo $vendor_logo_url ? __('Ganti Logo', 'vendorhub') : __('Upload Logo', 'vendorhub'); ?>
                                            </button>
                                            <?php if ( $vendor_logo_url ) : ?>
                                            <button type="button" id="vh-logo-remove" class="fi-btn" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;"><?php _e('Hapus', 'vendorhub'); ?></button>
                                            <?php endif; ?>
                                        </div>
                                        <p style="font-size:12px;color:#94a3b8;margin:10px 0 0;">Format: JPG, PNG. Rekomendasi 500x500px.</p>
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr; gap: 32px;">
                                <!-- Dasar Perusahaan -->
                                <section>
                                    <h3 style="font-size: 14px; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-admin-home" style="font-size: 18px;"></span> <?php _e('Informasi Dasar', 'vendorhub'); ?>
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
                                <section style="border-top: 1px solid #f1f5f9; padding-top: 32px;">
                                    <h3 style="font-size: 14px; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-shield-alt" style="font-size: 18px;"></span> <?php _e('Legalitas & Kategori', 'vendorhub'); ?>
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
                                <section style="border-top: 1px solid #f1f5f9; padding-top: 32px;">
                                    <h3 style="font-size: 14px; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-location-alt" style="font-size: 18px;"></span> <?php _e('Kontak & Lokasi', 'vendorhub'); ?>
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
                                <section style="border-top: 1px solid #f1f5f9; padding-top: 32px; background: #fffbeb; margin: 0 -24px; padding-left: 24px; padding-right: 24px;">
                                    <h3 style="font-size: 14px; color: #92400e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-lock" style="font-size: 18px;"></span> <?php _e('Keamanan Akun', 'vendorhub'); ?>
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

    function bindRemove() {
        $('#vh-logo-remove').off('click').on('click', function(){
            $('#vh-logo-input').val('');
            $('#vh-logo-preview').html('<span class="dashicons dashicons-format-image" style="font-size:32px;color:#cbd5e1;"></span>');
            $('#vh-logo-picker').html('<span class="dashicons dashicons-upload" style="font-size:16px;width:16px;height:16px;"></span> Upload Logo');
            $(this).remove();
        });
    }
    bindRemove();
});
</script>

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
</body>
</html>
