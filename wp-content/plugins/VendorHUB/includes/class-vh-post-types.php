<?php
/**
 * Post Types and Taxonomies for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Post_Types {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
        add_action( 'init', array( __CLASS__, 'register_post_types' ) );
    }

    public static function register_taxonomies() {
        // Industry Category for Vendors/Products/Services
        register_taxonomy( 'vh_industry', array( 'vh_product', 'vh_service', 'vh_tender' ), array(
            'labels' => array(
                'name' => __( 'Industries', 'vendorhub' ),
                'singular_name' => __( 'Industry', 'vendorhub' ),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ) );
    }

    public static function register_post_types() {
        // Already defined some in main file, but let's move them here for better organization
        
        // Products
        register_post_type( 'vh_product', array(
            'labels' => array(
                'name' => __( 'Products', 'vendorhub' ),
                'singular_name' => __( 'Product', 'vendorhub' ),
                'add_new_item' => __( 'Add New Product', 'vendorhub' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-cart',
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
            'show_in_rest' => true,
            'taxonomies' => array( 'vh_industry' ),
        ) );

        // Services
        register_post_type( 'vh_service', array(
            'labels' => array(
                'name' => __( 'Services', 'vendorhub' ),
                'singular_name' => __( 'Service', 'vendorhub' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-businessman',
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
            'show_in_rest' => true,
            'taxonomies' => array( 'vh_industry' ),
        ) );

        // Tender
        register_post_type( 'vh_tender', array(
            'labels' => array(
                'name' => __( 'Tenders', 'vendorhub' ),
                'singular_name' => __( 'Tender', 'vendorhub' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-clipboard',
            'supports' => array( 'title', 'editor', 'excerpt', 'author' ),
            'show_in_rest' => true,
        ) );

        // RFQ
        register_post_type( 'vh_rfq', array(
            'labels' => array(
                'name' => __( 'RFQs', 'vendorhub' ),
                'singular_name' => __( 'RFQ', 'vendorhub' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-email-alt',
            'supports' => array( 'title', 'editor', 'author' ),
            'show_in_rest' => true,
        ) );

        // Proposal (Vendor kirim ke Tender)
        register_post_type( 'vh_proposal', array(
            'labels' => array(
                'name'          => __( 'Proposals', 'vendorhub' ),
                'singular_name' => __( 'Proposal', 'vendorhub' ),
            ),
            'public'      => false,
            'show_ui'     => true,
            'menu_icon'   => 'dashicons-portfolio',
            'supports'    => array( 'title', 'editor', 'author' ),
            'show_in_rest'=> false,
        ) );

        // Review (Buyer rating Vendor)
        register_post_type( 'vh_review', array(
            'labels' => array(
                'name'          => __( 'Reviews', 'vendorhub' ),
                'singular_name' => __( 'Review', 'vendorhub' ),
            ),
            'public'   => false,
            'show_ui'  => true,
            'menu_icon'=> 'dashicons-star-filled',
            'supports' => array( 'title', 'editor', 'author' ),
        ) );

        // Ads (Banners for Home/Marketplace)
        register_post_type( 'vh_ad', array(
            'labels' => array(
                'name'          => __( 'Iklan & Banner', 'vendorhub' ),
                'singular_name' => __( 'Iklan', 'vendorhub' ),
                'add_new_item'  => __( 'Tambah Iklan Baru', 'vendorhub' ),
            ),
            'public'      => true,
            'show_ui'     => true,
            'menu_icon'   => 'dashicons-megaphone',
            'supports'    => array( 'title', 'thumbnail', 'excerpt', 'author' ),
            'show_in_rest'=> true,
        ) );
    }
}

VH_Post_Types::init();
