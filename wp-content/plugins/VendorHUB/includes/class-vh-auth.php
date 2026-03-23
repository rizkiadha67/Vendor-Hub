<?php
/**
 * Authentication Handler for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Auth {

    public static function init() {
        add_action( 'wp_ajax_vh_ajax_register', array( __CLASS__, 'handle_registration' ) );
        add_action( 'wp_ajax_nopriv_vh_ajax_register', array( __CLASS__, 'handle_registration' ) );
        
        add_action( 'wp_ajax_vh_ajax_login', array( __CLASS__, 'handle_login' ) );
        add_action( 'wp_ajax_nopriv_vh_ajax_login', array( __CLASS__, 'handle_login' ) );

        add_action( 'wp_ajax_vh_save_profile', array( __CLASS__, 'handle_save_profile' ) );
        add_action( 'wp_ajax_vh_save_product', array( __CLASS__, 'handle_save_product' ) );
        add_action( 'wp_ajax_vh_delete_product', array( __CLASS__, 'handle_delete_product' ) );
        add_action( 'wp_ajax_vh_add_tender', array( __CLASS__, 'handle_add_tender' ) );
    }

    /**
     * Handle AJAX Registration
     */
    public static function handle_registration() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        $username = sanitize_user( $_POST['user_login'] );
        $email    = sanitize_email( $_POST['user_email'] );
        $role     = sanitize_text_field( $_POST['user_role'] );
        $password = isset( $_POST['user_pass'] ) ? $_POST['user_pass'] : '';

        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            wp_send_json_error( __( 'Semua field wajib diisi.', 'vendorhub' ) );
        }

        if ( strlen( $password ) < 6 ) {
            wp_send_json_error( __( 'Password minimal 6 karakter.', 'vendorhub' ) );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( __( 'Email ini sudah terdaftar.', 'vendorhub' ) );
        }

        if ( username_exists( $username ) ) {
            wp_send_json_error( __( 'Username ini sudah dipakai.', 'vendorhub' ) );
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( $user_id->get_error_message() );
        }

        $user = new WP_User( $user_id );
        $user->set_role( $role );

        if ( isset( $_POST['vh_company_name'] ) ) {
            update_user_meta( $user_id, 'vh_company_name', sanitize_text_field( $_POST['vh_company_name'] ) );
        }
        if ( isset( $_POST['vh_location'] ) ) {
            update_user_meta( $user_id, 'vh_location', sanitize_text_field( $_POST['vh_location'] ) );
        }
        update_user_meta( $user_id, 'vh_role', $role );

        // Auto Login
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id );

        wp_send_json_success( array(
            'message'  => __( 'Akun berhasil dibuat! Mengalihkan...', 'vendorhub' ),
            'redirect' => site_url( '/dashboard' )
        ) );
    }

    /**
     * Handle AJAX Login
     */
    public static function handle_login() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        $info = array();
        $info['user_login']    = $_POST['log'];
        $info['user_password'] = $_POST['pwd'];
        $info['remember']      = true;

        $user_signon = wp_signon( $info, is_ssl() );

        if ( is_wp_error( $user_signon ) ) {
            wp_send_json_error( __( 'Invalid username or password.', 'vendorhub' ) );
        } else {
            wp_send_json_success( array(
                'message' => __( 'Login successful! Redirecting...', 'vendorhub' ),
                'redirect' => site_url( '/dashboard' )
            ) );
        }
    }

    /**
     * Handle Profile Save
     */
    public static function handle_save_profile() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in.', 'vendorhub' ) );
        }

        $user_id = get_current_user_id();

        // Update standard user info
        if ( ! empty( $_POST['display_name'] ) ) {
            wp_update_user( array( 'ID' => $user_id, 'display_name' => sanitize_text_field( $_POST['display_name'] ) ) );
        }
        if ( ! empty( $_POST['user_pass'] ) && strlen( $_POST['user_pass'] ) >= 6 ) {
            wp_set_password( $_POST['user_pass'], $user_id );
        }

        // List of meta fields to save
        $meta_fields = array(
            'vh_company_name'  => 'sanitize_text_field',
            'vh_location'      => 'sanitize_text_field',
            'vh_tagline'       => 'sanitize_text_field',
            'vh_description'   => 'sanitize_textarea_field',
            'vh_business_type' => 'sanitize_text_field',
            'vh_nib'           => 'sanitize_text_field',
            'vh_industry'      => 'sanitize_text_field',
            'vh_wa_number'     => 'sanitize_text_field',
            'vh_business_email'=> 'sanitize_email',
            'vh_website'       => 'esc_url_raw',
            'vh_address'       => 'sanitize_textarea_field',
            'vh_biz_scale'     => 'sanitize_text_field',
            'vh_est_year'      => 'sanitize_text_field',
        );

        foreach ( $meta_fields as $key => $sanitize ) {
            if ( isset( $_POST[$key] ) ) {
                update_user_meta( $user_id, $key, $sanitize( $_POST[$key] ) );
            }
        }

        // Save vendor logo (attachment ID)
        if ( isset( $_POST['vh_vendor_logo'] ) ) {
            $logo_id = absint( $_POST['vh_vendor_logo'] );
            if ( $logo_id > 0 ) {
                update_user_meta( $user_id, 'vh_vendor_logo', $logo_id );
            } else {
                delete_user_meta( $user_id, 'vh_vendor_logo' );
            }
        }

        wp_send_json_success( array( 'message' => __( 'Profil berhasil disimpan!', 'vendorhub' ) ) );
    }

    /**
     * Handle Save Product (Create or Update)
     */
    public static function handle_save_product() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! is_user_logged_in() || ! vh_is_vendor() ) {
            wp_send_json_error( __( 'Unauthorized.', 'vendorhub' ) );
        }

        $user_id    = get_current_user_id();
        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $title      = sanitize_text_field( $_POST['post_title'] ?? '' );

        if ( empty( $title ) ) {
            wp_send_json_error( __( 'Judul produk tidak boleh kosong.', 'vendorhub' ) );
        }

        // Security check for update
        if ( $product_id && (int) get_post_field( 'post_author', $product_id ) !== (int) $user_id ) {
            wp_send_json_error( __( 'Akses ditolak.', 'vendorhub' ) );
        }

        $post_data = array(
            'post_type'    => 'vh_product',
            'post_title'   => $title,
            'post_content' => wp_kses_post( $_POST['post_content'] ?? '' ),
            'post_status'  => 'publish',
        );

        if ( $product_id ) {
            $post_data['ID'] = $product_id;
            $res = wp_update_post( $post_data );
        } else {
            $post_data['post_author'] = $user_id;
            $res = wp_insert_post( $post_data );
            $product_id = $res;
        }

        if ( is_wp_error( $res ) ) {
            wp_send_json_error( $res->get_error_message() );
        }

        // Save Metadata
        update_post_meta( $product_id, '_vh_price', sanitize_text_field( $_POST['vh_price'] ?? '' ) );
        update_post_meta( $product_id, '_vh_moq',   sanitize_text_field( $_POST['vh_moq'] ?? '1' ) );
        update_post_meta( $product_id, '_vh_unit',  sanitize_text_field( $_POST['vh_unit'] ?? 'Unit' ) );

        // Save Industry/Category
        if ( ! empty( $_POST['vh_industry'] ) ) {
            wp_set_object_terms( $product_id, sanitize_text_field( $_POST['vh_industry'] ), 'vh_industry' );
        }

        // Save Image (Thumbnail)
        if ( ! empty( $_POST['thumbnail_id'] ) ) {
            set_post_thumbnail( $product_id, absint( $_POST['thumbnail_id'] ) );
        } else {
            delete_post_thumbnail( $product_id );
        }

        wp_send_json_success( array(
            'message'  => $product_id ? __( 'Produk berhasil diperbarui!', 'vendorhub' ) : __( 'Produk berhasil ditambahkan!', 'vendorhub' ),
            'redirect' => add_query_arg( 'tab', 'products', site_url( '/dashboard' ) ),
        ) );
    }

    /**
     * Handle Product Deletion
     */
    public static function handle_delete_product() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        $id = absint( $_POST['id'] ?? 0 );
        if ( ! $id || ! is_user_logged_in() ) {
            wp_send_json_error( __( 'ID tidak valid.', 'vendorhub' ) );
        }

        // Check ownership
        if ( (int) get_post_field( 'post_author', $id ) !== (int) get_current_user_id() && ! current_user_can('administrator') ) {
            wp_send_json_error( __( 'Akses ditolak.', 'vendorhub' ) );
        }

        if ( wp_trash_post( $id ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( __( 'Gagal menghapus produk.', 'vendorhub' ) );
        }
    }

    /**
     * Handle Add Tender (Buyer)
     */
    public static function handle_add_tender() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! is_user_logged_in() || ! vh_is_buyer() ) {
            wp_send_json_error( __( 'Unauthorized.', 'vendorhub' ) );
        }

        $title = sanitize_text_field( $_POST['post_title'] ?? '' );
        if ( empty( $title ) ) {
            wp_send_json_error( __( 'Judul tender tidak boleh kosong.', 'vendorhub' ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'    => 'vh_tender',
            'post_title'   => $title,
            'post_content' => wp_kses_post( $_POST['post_content'] ?? '' ),
            'post_status'  => 'publish',
            'post_author'  => get_current_user_id(),
        ) );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        update_post_meta( $post_id, '_vh_tender_budget',   sanitize_text_field( $_POST['vh_tender_budget'] ?? '' ) );
        update_post_meta( $post_id, '_vh_tender_deadline', sanitize_text_field( $_POST['vh_tender_deadline'] ?? '' ) );

        wp_send_json_success( array(
            'message'  => __( 'Tender berhasil dibuat!', 'vendorhub' ),
            'redirect' => add_query_arg( 'tab', 'tenders', site_url( '/dashboard' ) ),
        ) );
    }
}

VH_Auth::init();
