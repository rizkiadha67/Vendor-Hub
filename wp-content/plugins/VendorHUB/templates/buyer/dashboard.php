<?php
/**
 * Buyer Dashboard Template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user = wp_get_current_user();
$section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'home');
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<style>
    :root {
        --fi-primary: #3b82f6;
        --fi-primary-light: #eff6ff;
        --fi-bg: #f3f4f6;
        --fi-card: #ffffff;
        --fi-border: #e5e7eb;
        --fi-text: #1f2937;
        --fi-text-muted: #6b7280;
    }

    .vh-dashboard-wrapper {
        background-color: var(--fi-bg);
        min-height: 100vh;
        padding: 2rem 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .vh-card {
        background: var(--fi-card);
        border: 1px solid var(--fi-border);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .vh-sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .vh-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--fi-text-muted);
        font-weight: 500;
        transition: all 0.2s;
    }

    .vh-nav-item:hover {
        background-color: rgba(59, 130, 246, 0.05);
        color: var(--fi-primary);
    }

    .vh-nav-item.active {
        background-color: var(--fi-primary-light);
        color: var(--fi-primary);
    }

    .vh-nav-item .dashicons {
        font-size: 20px;
        width: 20px;
        height: 20px;
    }

    .vh-stat-card {
        text-align: left;
    }

    .vh-stat-card h3 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--fi-text);
        margin: 0.5rem 0 0;
    }

    .vh-stat-card p {
        color: var(--fi-text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
    }

    .vh-stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: var(--fi-primary-light);
        color: var(--fi-primary);
        margin-bottom: 0.75rem;
    }

    .vh-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .vh-btn-primary {
        background-color: var(--fi-primary);
        color: white;
    }

    .vh-badge-buyer {
        background: #fee2e2;
        color: #991b1b;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    @media (max-width: 1024px) {
        .vh-dashboard-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="vh-dashboard-wrapper">
    <div class="vh-container">
        <div class="vh-dashboard-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 2rem;">
            
            <!-- Sidebar Navigation -->
            <aside class="vh-dashboard-sidebar">
                <div class="vh-card" style="padding: 1rem; position: sticky; top: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 2rem; padding: 0.5rem;">
                        <img src="<?php echo get_avatar_url( $current_user->ID ); ?>" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;">
                        <div style="min-width: 0;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html( $current_user->display_name ); ?></h4>
                            <span class="vh-badge-buyer"><?php _e('Corporate Buyer', 'vendorhub'); ?></span>
                        </div>
                    </div>

                    <nav class="vh-sidebar-nav">
                        <a href="<?php echo site_url('/dashboard'); ?>" class="vh-nav-item <?php echo $section === 'home' ? 'active' : ''; ?>">
                            <span class="dashicons dashicons-dashboard"></span> <?php _e('Dashboard', 'vendorhub'); ?>
                        </a>
                        <a href="<?php echo add_query_arg('section', 'tenders', site_url('/dashboard')); ?>" class="vh-nav-item <?php echo $section === 'tenders' ? 'active' : ''; ?>">
                            <span class="dashicons dashicons-clipboard"></span> <?php _e('Tender Saya', 'vendorhub'); ?>
                        </a>
                        <a href="<?php echo site_url('/marketplace-vendor'); ?>" class="vh-nav-item">
                            <span class="dashicons dashicons-search"></span> <?php _e('Cari Vendor', 'vendorhub'); ?>
                        </a>
                        <a href="<?php echo add_query_arg('section', 'settings', site_url('/dashboard')); ?>" class="vh-nav-item <?php echo ($section === 'settings' || $section === 'verification') ? 'active' : ''; ?>">
                            <span class="dashicons dashicons-admin-generic"></span> <?php _e('Pengaturan Profil', 'vendorhub'); ?>
                        </a>
                        <div style="margin: 1rem 0; border-top: 1px solid var(--fi-border);"></div>
                        <a href="<?php echo wp_logout_url(site_url()); ?>" class="vh-nav-item" style="color: #ef4444;">
                            <span class="dashicons dashicons-logout"></span> <?php _e('Log Out', 'vendorhub'); ?>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="vh-dashboard-main">
                <?php 
                if ( isset( $_GET['message'] ) ) {
                    $msg = sanitize_text_field( $_GET['message'] );
                    $is_error = $msg === 'deleted';
                    $text = $msg === 'deleted' ? __('Berhasil dihapus.', 'vendorhub') : __('Berhasil disimpan.', 'vendorhub');
                    echo '<div style="background: '.($is_error ? '#fef2f2' : '#f0fdf4').'; color: '.($is_error ? '#991b1b' : '#166534').'; padding: 1rem; border-radius: 12px; border: 1px solid '.($is_error ? '#fee2e2' : '#dcfce7').'; margin-bottom: 2rem; font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 8px;"><span class="dashicons dashicons-'.($is_error ? 'warning' : 'yes-alt').'"></span> '.$text.'</div>';
                }

                switch ( $section ) {
                    case 'tenders':
                        if ( $action === 'edit' || $action === 'add' ) {
                            include VENDORHUB_PATH . 'templates/vendor/tender-edit.php';
                        } else {
                            include VENDORHUB_PATH . 'templates/vendor/tender-list.php';
                        }
                        break;

                    case 'verification':
                    case 'settings':
                        include VENDORHUB_PATH . 'templates/vendor/verification-form.php';
                        break;

                    default:
                        ?>
                        <header style="margin-bottom: 2.5rem;">
                            <h1 style="font-size: 1.875rem; font-weight: 800; color: var(--fi-text); margin: 0;"><?php printf( __( 'Halo, %s!', 'vendorhub' ), esc_html( $current_user->display_name ) ); ?></h1>
                            <p style="color: var(--fi-text-muted); margin-top: 0.5rem;"><?php _e( 'Kelola pengadaan dan cari mitra vendor terbaik untuk bisnis Anda.', 'vendorhub' ); ?></p>
                        </header>

                        <?php if ( ! vh_profile_is_complete() || ! vh_is_verified() ) : ?>
                            <div class="vh-card" style="border-left: 4px solid #f59e0b; background: white; margin-bottom: 2.5rem;">
                                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                                    <div style="background: #fffbeb; color: #f59e0b; padding: 10px; border-radius: 10px;">
                                        <span class="dashicons dashicons-warning" style="font-size: 24px; width: 24px; height: 24px;"></span>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0; color: #92400e; font-weight: 700;"><?php _e('Lengkapi Profil Perusahaan', 'vendorhub'); ?></h4>
                                        <p style="margin: 0.5rem 0 1rem; color: #b45309; font-size: 14px; line-height: 1.5;">
                                            <?php _e('Anda perlu melengkapi profil perusahaan Anda untuk dapat mempublikasikan tender dan mendapatkan penawaran dari vendor.', 'vendorhub'); ?>
                                        </p>
                                        <a href="<?php echo add_query_arg('section', 'verification', site_url('/dashboard')); ?>" class="vh-btn" style="background: #f59e0b; color: white;">
                                            <?php _e('Lengkapi Sekarang', 'vendorhub'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php 
                        $user_id = get_current_user_id();
                        $tender_count = count_user_posts( $user_id, 'vh_tender' );
                        $rfq_count    = count_user_posts( $user_id, 'vh_rfq' );
                        ?>
                        <div class="vh-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
                            <div class="vh-card vh-stat-card">
                                <div class="vh-stat-icon">
                                    <span class="dashicons dashicons-clipboard"></span>
                                </div>
                                <p><?php _e( 'Total Tender Saya', 'vendorhub' ); ?></p>
                                <h3><?php echo $tender_count; ?></h3>
                                <div style="font-size: 13px; color: var(--fi-text-muted); margin-top: 4px;"><?php printf( _n( '%s tender aktif', '%s tender aktif', $tender_count, 'vendorhub' ), $tender_count ); ?></div>
                            </div>
                            <div class="vh-card vh-stat-card">
                                <div class="vh-stat-icon" style="background: #fdf2f8; color: #db2777;">
                                    <span class="dashicons dashicons-email-alt"></span>
                                </div>
                                <p><?php _e( 'Permintaan RFQ', 'vendorhub' ); ?></p>
                                <h3><?php echo $rfq_count; ?></h3>
                                <div style="font-size: 13px; color: var(--fi-text-muted); margin-top: 4px;"><?php printf( _n( '%s RFQ terkirim', '%s RFQ terkirim', $rfq_count, 'vendorhub' ), $rfq_count ); ?></div>
                            </div>
                            <div class="vh-card vh-stat-card">
                                <div class="vh-stat-icon" style="background: #f0fdf4; color: #16a34a;">
                                    <span class="dashicons dashicons-star-filled"></span>
                                </div>
                                <p><?php _e( 'Status Akun', 'vendorhub' ); ?></p>
                                <h3><?php _e('Buyer', 'vendorhub'); ?></h3>
                                <div style="font-size: 13px; color: var(--fi-text-muted); margin-top: 4px;"><?php _e('Verified Corporate', 'vendorhub'); ?></div>
                            </div>
                        </div>

                        <div class="vh-card" style="margin-top: 2rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.5rem;"><?php _e('Pantau Tender Anda', 'vendorhub'); ?></h3>
                            <div style="text-align: center; padding: 3rem 0; color: var(--fi-text-muted);">
                                <span class="dashicons dashicons-info" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.3;"></span>
                                <p style="margin-top: 1rem; font-size: 14px;"><?php _e('Belum ada tender aktif yang memerlukan perhatian.', 'vendorhub'); ?></p>
                            </div>
                        </div>
                        <?php
                        break;
                }
                ?>
            </main>
        </div>
    </div>
</div>
