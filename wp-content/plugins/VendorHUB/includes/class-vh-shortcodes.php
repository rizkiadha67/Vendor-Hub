<?php
/**
 * Shortcodes for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Shortcodes {

    public static function init() {
        add_shortcode( 'vh_vendor_dashboard', array( __CLASS__, 'vendor_dashboard' ) );
        add_shortcode( 'vh_buyer_dashboard', array( __CLASS__, 'buyer_dashboard' ) );
        add_shortcode( 'vh_marketplace_vendor', array( __CLASS__, 'marketplace_vendor' ) );
        add_shortcode( 'vh_vendor_directory', array( __CLASS__, 'marketplace_vendor' ) ); // Alias

        add_shortcode( 'vh_marketplace_products', array( __CLASS__, 'marketplace_products' ) );
        add_shortcode( 'vh_product_listing', array( __CLASS__, 'marketplace_products' ) ); // Alias

        add_shortcode( 'vh_marketplace_services', array( __CLASS__, 'marketplace_services' ) );
        add_shortcode( 'vh_tender_listing', array( __CLASS__, 'tender_listing' ) );
        add_shortcode( 'vh_inbox', array( __CLASS__, 'render_inbox' ) );
    }

    public static function vendor_dashboard() {
        if ( ! is_user_logged_in() || ( ! vh_is_vendor() && ! current_user_can('administrator') ) ) {
            return '<div class="vh-container"><p>' . __( 'You must be logged in as a Vendor to view this dashboard.', 'vendorhub' ) . '</p></div>';
        }

        ob_start();
        include VENDORHUB_PATH . 'templates/vendor/dashboard.php';
        return ob_get_clean();
    }

    public static function buyer_dashboard() {
        if ( ! is_user_logged_in() || ( ! vh_is_buyer() && ! current_user_can('administrator') ) ) {
            return '<div class="vh-container"><p>' . __( 'You must be logged in as a Buyer to view this dashboard.', 'vendorhub' ) . '</p></div>';
        }

        ob_start();
        include VENDORHUB_PATH . 'templates/buyer/dashboard.php';
        return ob_get_clean();
    }

    public static function marketplace_vendor() {
        ob_start();
        include VENDORHUB_PATH . 'templates/marketplace/vendor-directory.php';
        return ob_get_clean();
    }

    public static function marketplace_products() {
        ob_start();
        include VENDORHUB_PATH . 'templates/marketplace/product-list.php';
        return ob_get_clean();
    }

    public static function marketplace_services() {
        ob_start();
        include VENDORHUB_PATH . 'templates/marketplace/service-list.php';
        return ob_get_clean();
    }

    public static function tender_listing() {
        ob_start();
        // Fallback to a basic loop if template doesn't exist yet, or load template
        if (file_exists(VENDORHUB_PATH . 'templates/marketplace/tender-list.php')) {
            include VENDORHUB_PATH . 'templates/marketplace/tender-list.php';
        } else {
            echo '<p>Tender list template missing.</p>';
        }
        return ob_get_clean();
    }

    public static function render_inbox() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please login to view your inbox.', 'vendorhub' ) . '</p>';
        }
        ob_start();
        include VENDORHUB_PATH . 'templates/dashboard/inbox.php';
        return ob_get_clean();
    }
}

VH_Shortcodes::init();

/**
 * Helper Functions (Internal but kept here for now)
 */
function vh_is_vendor( $user_id = null ) {
    $user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
    return $user && in_array( 'vendor', (array) $user->roles );
}

function vh_is_buyer( $user_id = null ) {
    $user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
    return $user && in_array( 'buyer', (array) $user->roles );
}
