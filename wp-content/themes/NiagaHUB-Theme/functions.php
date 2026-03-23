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

    // Midtrans Snap JS
    $midtrans_mode = get_option('vh_midtrans_mode', 'sandbox');
    $snap_url = ($midtrans_mode === 'production') 
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    
    wp_enqueue_script( 'midtrans-snap', $snap_url, array(), null, true );
    wp_add_inline_script( 'midtrans-snap', 'if(typeof window.snap === "undefined") { console.warn("Snap JS failed to load"); }', 'after' );
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

/**
 * Security: Hide WordPress Version & Identifiers (Hide WP Detector)
 */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// Remove version from scripts and styles
function vh_remove_wp_version_strings( $src ) {
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if ( !empty($query['ver']) && $query['ver'] === $wp_version ) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter( 'script_loader_src', 'vh_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'vh_remove_wp_version_strings' );

// Remove Emoji Support
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Global SEO & Social Media Meta Tags
 */
function vh_header_seo_tags() {
    $site_name = get_bloginfo('name');
    
    if (is_singular('vh_product')) {
        $price = get_post_meta(get_the_ID(), '_vh_price', true);
        $desc = get_the_excerpt();
        $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
        
        echo '<meta name="description" content="'.esc_attr($desc).'">';
        echo '<meta property="og:type" content="product">';
        echo '<meta property="og:price:amount" content="'.esc_attr($price).'">';
        echo '<meta property="og:price:currency" content="IDR">';
        if ($img) echo '<meta property="og:image" content="'.esc_url($img).'">';
        
        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">';
        echo '<meta name="twitter:title" content="'.esc_attr(get_the_title()).'">';
        echo '<meta name="twitter:description" content="'.esc_attr($desc).'">';
        if ($img) echo '<meta name="twitter:image" content="'.esc_url($img).'">';

    } elseif (is_singular('vh_tender')) {
        $desc = 'Informasi Tender: ' . get_the_title();
        echo '<meta name="description" content="'.esc_attr($desc).'">';
        echo '<meta property="og:type" content="article">';
        echo '<meta name="twitter:card" content="summary">';
        echo '<meta name="twitter:title" content="'.esc_attr(get_the_title()).'">';
        echo '<meta name="twitter:description" content="'.esc_attr($desc).'">';

    } elseif (is_author()) {
        $vendor_id = get_queried_object_id();
        $name = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_the_author_meta('display_name', $vendor_id);
        $desc = get_the_author_meta('description', $vendor_id) ?: 'Profil Vendor di ' . $site_name;
        echo '<meta name="description" content="'.esc_attr($desc).'">';
        echo '<meta property="og:title" content="'.esc_attr($name).' - NiagaHUB Vendor">';

    } elseif (is_tax('vh_industry')) {
        $term = get_queried_object();
        $desc = 'Daftar produk dan vendor di kategori ' . $term->name . ' - ' . $site_name;
        echo '<meta name="description" content="'.esc_attr($desc).'">';
    }
}
add_action('wp_head', 'vh_header_seo_tags');

/**
 * Manage Search Engine Indexing (Robots)
 */
function vh_robots_meta_tags() {
    // Noindex sensitive pages
    if (is_page('dashboard') || is_page('auth') || is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, nofollow">';
    }
}
add_action('wp_head', 'vh_robots_meta_tags');

/**
 * Enhanced JSON-LD Schema (Product, LocalBusiness, Breadcrumbs)
 */
function vh_add_json_ld_schema() {
    $schemas = [];

    // 1. Breadcrumb Schema
    if (!is_front_page()) {
        $items = [
            ["@type" => "ListItem", "position" => 1, "name" => "Beranda", "item" => home_url('/')]
        ];

        if (is_singular(['vh_product', 'vh_tender', 'post'])) {
            $items[] = ["@type" => "ListItem", "position" => 2, "name" => get_the_title(), "item" => get_permalink()];
        } elseif (is_tax('vh_industry')) {
            $term = get_queried_object();
            $items[] = ["@type" => "ListItem", "position" => 2, "name" => $term->name, "item" => get_term_link($term)];
        } elseif (is_author()) {
            $vendor_id = get_queried_object_id();
            $name = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_the_author_meta('display_name', $vendor_id);
            $items[] = ["@type" => "ListItem", "position" => 2, "name" => $name, "item" => get_author_posts_url($vendor_id)];
        }

        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $items
        ];
    }

    // 2. Main Entity Schema
    if (is_singular('vh_product')) {
        $schemas[] = [
            "@context" => "https://schema.org/",
            "@type" => "Product",
            "name" => get_the_title(),
            "description" => get_the_excerpt(),
            "image" => get_the_post_thumbnail_url(),
            "offers" => [
                "@type" => "Offer",
                "priceCurrency" => "IDR",
                "price" => get_post_meta(get_the_ID(), '_vh_price', true),
                "availability" => "https://schema.org/InStock"
            ]
        ];
    } elseif (is_author()) {
        $vendor_id = get_queried_object_id();
        $schemas[] = [
            "@context" => "https://schema.org/",
            "@type" => "LocalBusiness",
            "name" => get_user_meta($vendor_id, 'vh_company_name', true) ?: get_the_author_meta('display_name', $vendor_id),
            "description" => get_the_author_meta('description', $vendor_id),
            "address" => [
                "@type" => "PostalAddress",
                "addressLocality" => get_user_meta($vendor_id, 'vh_location', true)
            ]
        ];
    }

    if (!empty($schemas)) {
        foreach ($schemas as $s) {
            echo '<script type="application/ld+json">' . json_encode($s) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'vh_add_json_ld_schema');

// Setup is now handled by the NiagaHUB Plugin Seeder for better portability.
