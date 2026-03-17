<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Top Bar -->
<div class="nh-top-bar">
    <div class="nh-container nh-top-bar-inner">
        <div>
            <a href="#"><?php _e('Hubungi Kami', 'niagahub-theme'); ?></a>
            <a href="#"><?php _e('Bantuan', 'niagahub-theme'); ?></a>
        </div>
        <div>
            <?php if ( ! is_user_logged_in() ) : ?>
                <a href="<?php echo site_url('/auth'); ?>" class="nh-top-cta"><?php _e('Jadi Vendor NiagaHUB', 'niagahub-theme'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="nh-header">
    <div class="nh-container nh-header-inner">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nh-logo">
            Niaga<span>HUB</span>
        </a>

        <!-- Category Tooltip Trigger -->
        <div class="nh-category-container">
            <button class="nh-category-menu" id="nhCategoryToggle" type="button">
                <span class="dashicons dashicons-menu"></span>
                <span><?php _e('Kategori', 'niagahub-theme'); ?></span>
                <span class="dashicons dashicons-arrow-down-alt2" style="font-size:13px;"></span>
            </button>

            <!-- Category Tooltip -->
            <div class="nh-category-popup" id="nhCategoryPopup">
                <div class="nh-category-popup-inner">
                    <div class="nh-category-popup-header">
                        <h4><?php _e('Kategori Produk', 'niagahub-theme'); ?></h4>
                    </div>
                    <div class="nh-category-popup-grid">
                        <?php
                        $industries = get_terms( array(
                            'taxonomy'   => 'vh_industry',
                            'hide_empty' => false,
                        ) );
                        if ( ! empty( $industries ) && ! is_wp_error( $industries ) ) :
                            foreach ( $industries as $industry ) : ?>
                                <a href="<?php echo get_term_link( $industry ); ?>" class="nh-cat-item">
                                    <span class="dashicons dashicons-tag nh-cat-icon"></span>
                                    <span class="nh-cat-label"><?php echo esc_html( $industry->name ); ?></span>
                                </a>
                            <?php endforeach;
                        else : ?>
                            <p style="color:#94a3b8;font-size:13px;padding:8px;"><?php _e('Belum ada kategori.', 'niagahub-theme'); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="nh-category-popup-footer">
                        <a href="<?php echo site_url('/marketplace-produk'); ?>"><?php _e('Lihat Semua Produk →', 'niagahub-theme'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Toggle -->
        <button class="nh-mobile-toggle" id="nhMobileToggle" aria-label="Menu">
            <span class="dashicons dashicons-menu-alt3"></span>
        </button>

        <!-- Collapsible Nav Area -->
        <div class="nh-header-collapse" id="nhHeaderCollapse">

            <!-- Primary Navigation -->
            <?php if ( has_nav_menu( 'primary' ) ) :
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => 'nav',
                    'container_class'=> 'nh-primary-nav',
                    'menu_class'     => 'nh-nav-list',
                ) );
            else : ?>
                <nav class="nh-primary-nav">
                    <ul class="nh-nav-list">
                        <li><a href="<?php echo site_url('/'); ?>"><span class="dashicons dashicons-admin-home"></span> <?php _e('Beranda', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo site_url('/marketplace-produk'); ?>"><span class="dashicons dashicons-cart"></span> <?php _e('Produk', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo site_url('/marketplace-vendor'); ?>"><span class="dashicons dashicons-store"></span> <?php _e('Vendor', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo site_url('/pusat-tender'); ?>"><span class="dashicons dashicons-clipboard"></span> <?php _e('Tender', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo site_url('/artikel'); ?>"><span class="dashicons dashicons-welcome-write-blog"></span> <?php _e('Artikel', 'niagahub-theme'); ?></a></li>
                    </ul>
                </nav>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="nh-search-bar">
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="nh-search-container">
                        <input type="search" name="s" placeholder="<?php _e('Cari produk, merk, atau vendor...', 'niagahub-theme'); ?>" value="<?php echo get_search_query(); ?>">
                        <button type="submit" class="nh-search-btn">
                            <span class="dashicons dashicons-search"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Actions -->
            <nav class="nh-nav-actions">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo add_query_arg('tab', 'inbox', site_url('/dashboard')); ?>" class="nh-icon-btn" title="Pesan Masuk">
                        <span class="dashicons dashicons-email-alt"></span>
                    </a>
                    <a href="<?php echo site_url('/dashboard'); ?>" class="nh-user-btn">
                        <span class="dashicons dashicons-admin-users"></span>
                        <span><?php _e('Dashboard', 'niagahub-theme'); ?></span>
                    </a>
                <?php else : ?>
                    <a href="<?php echo site_url('/auth'); ?>" class="btn-login"><?php _e('Masuk', 'niagahub-theme'); ?></a>
                    <a href="<?php echo site_url('/auth'); ?>" class="btn-register-main"><?php _e('Daftar', 'niagahub-theme'); ?></a>
                <?php endif; ?>
            </nav>

        </div><!-- /.nh-header-collapse -->

    </div><!-- /.nh-header-inner -->
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    var toggle   = document.getElementById('nhMobileToggle');
    var collapse = document.getElementById('nhHeaderCollapse');
    if (toggle && collapse) {
        toggle.addEventListener('click', function() {
            collapse.classList.toggle('active');
        });
    }

    // Category tooltip toggle
    var catToggle = document.getElementById('nhCategoryToggle');
    var catPopup  = document.getElementById('nhCategoryPopup');
    if (catToggle && catPopup) {
        catToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            catPopup.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
            if (!catPopup.contains(e.target) && !catToggle.contains(e.target)) {
                catPopup.classList.remove('active');
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') catPopup.classList.remove('active');
        });
    }
});
</script>
