<?php
/**
 * NiagaHUB Theme Functions
 */

function niagahub_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    
    // Register Menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'niagahub-theme' ),
        'footer'  => __( 'Footer Menu', 'niagahub-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'niagahub_theme_setup' );

function niagahub_theme_scripts() {
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_style( 'niagahub-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap', array(), null );
    wp_enqueue_style( 'niagahub-main-style', get_stylesheet_uri(), array(), time() );
}
add_action( 'wp_enqueue_scripts', 'niagahub_theme_scripts' );

/**
 * Customize Archive Title
 */
function niagahub_archive_title( $title ) {
    if ( is_post_type_archive( 'vh_product' ) ) {
        $title = __( 'Marketplace Produk', 'niagahub-theme' );
    } elseif ( is_post_type_archive( 'vh_tender' ) ) {
        $title = __( 'Pusat Tender & Proyek', 'niagahub-theme' );
    } elseif ( is_tax( 'vh_industry' ) ) {
        $title = sprintf( __( 'Industri: %s', 'niagahub-theme' ), single_term_title( '', false ) );
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'niagahub_archive_title' );

/**
 * Handle Theme Redirections
 * Force non-admins to Filament Dashboard after login
 */
function niagahub_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            return admin_url();
        }
    }
    return site_url( '/dashboard' );
}
add_filter( 'login_redirect', 'niagahub_login_redirect', 10, 3 );

/**
 * Block WP-Admin for non-administrators
 */
function niagahub_block_admin_access() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_safe_redirect( site_url( '/dashboard' ) );
        exit;
    }
}
add_action( 'init', 'niagahub_block_admin_access' );


/**
 * Hide Admin Bar for non-administrators
 */
function niagahub_hide_admin_bar() {
    if ( ! current_user_can( 'administrator' ) ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'niagahub_hide_admin_bar' );

/**
 * Breadcrumbs Helper
 */
function vh_breadcrumbs( $theme = 'dark' ) {
    $class = ( $theme === 'light' ) ? 'vh-breadcrumbs vh-breadcrumbs-light' : 'vh-breadcrumbs';
    echo '<nav class="' . esc_attr($class) . '">';
    echo '<a href="'.home_url().'">Beranda</a>';
    
    if ( is_search() ) {
        echo ' <span class="vh-sep">/</span> <span class="vh-current">Hasil Pencarian</span>';
    } elseif ( is_tax('vh_industry') ) {
        echo ' <span class="vh-sep">/</span> <a href="'.site_url('/marketplace-produk').'">Marketplace</a>';
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">' . single_term_title('', false) . '</strong>';
    } elseif ( is_post_type_archive('vh_product') ) {
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">Marketplace Produk</strong>';
    } elseif ( is_post_type_archive('vh_tender') ) {
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">Pusat Tender</strong>';
    } elseif ( is_singular('vh_product') ) {
        echo ' <span class="vh-sep">/</span> <a href="'.site_url('/marketplace-produk').'">Marketplace</a>';
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">' . get_the_title() . '</strong>';
    } elseif ( is_page() ) {
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">' . get_the_title() . '</strong>';
    } elseif ( is_archive() ) {
        echo ' <span class="vh-sep">/</span> <strong class="vh-current">' . get_the_archive_title() . '</strong>';
    }
    
    echo '</nav>';
}

/**
 * Get relevant background image for industry
 */
function vh_get_industry_bg( $slug ) {
    $bgs = array(
        'komputer-teknologi' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=2000',
        'office-construction-supplies' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=2000',
        'alat-berat' => 'https://images.unsplash.com/photo-1541888081622-c90a169b1288?auto=format&fit=crop&q=80&w=2000',
        'default' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=2000'
    );
    return isset($bgs[$slug]) ? $bgs[$slug] : $bgs['default'];
}

// Setup is now handled by the NiagaHUB Plugin Seeder for better portability.
