<?php
/**
 * Admin Manager for NiagaHUB
 * Handles Super Admin specific AJAX actions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Admin_Manager {

    public static function init() {
        add_action( 'wp_ajax_vh_admin_create_user', array( __CLASS__, 'handle_admin_create_user' ) );
        add_action( 'wp_ajax_vh_admin_toggle_verify', array( __CLASS__, 'handle_admin_toggle_verify' ) );
        add_action( 'wp_ajax_vh_vendor_submit_ad', array( __CLASS__, 'handle_vendor_submit_ad' ) );
        add_action( 'wp_ajax_vh_admin_save_settings', array( __CLASS__, 'handle_admin_save_settings' ) );
        add_action( 'wp_ajax_vh_admin_manage_ad', array( __CLASS__, 'handle_admin_manage_ad' ) );
    }

    /**
     * AJAX handler to create a new user by Super Admin
     */
    public static function handle_admin_create_user() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! current_user_can( 'administrator' ) ) {
            wp_send_json_error( __( 'Akses ditolak. Anda bukan Super Admin.', 'vendorhub' ) );
        }

        $username = sanitize_user( $_POST['user_login'] );
        $email    = sanitize_email( $_POST['user_email'] );
        $role     = sanitize_text_field( $_POST['user_role'] );
        $password = $_POST['user_pass'];
        $display_name = sanitize_text_field( $_POST['display_name'] );

        if ( empty( $username ) || empty( $email ) || empty( $password ) || empty( $role ) ) {
            wp_send_json_error( __( 'Semua field wajib diisi.', 'vendorhub' ) );
        }

        if ( username_exists( $username ) ) {
            wp_send_json_error( __( 'Username sudah ada.', 'vendorhub' ) );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( __( 'Email sudah terdaftar.', 'vendorhub' ) );
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( $user_id->get_error_message() );
        }

        $user = new WP_User( $user_id );
        $user->set_role( $role );

        wp_update_user( array(
            'ID'           => $user_id,
            'display_name' => $display_name ? $display_name : $username,
        ) );

        // Set custom meta based on role
        if ( $role === 'vendor' ) {
            update_user_meta( $user_id, 'vh_is_vendor', '1' );
        } elseif ( $role === 'buyer' ) {
            update_user_meta( $user_id, 'vh_is_buyer', '1' );
        }

        wp_send_json_success( array(
            'message' => __( 'User berhasil dibuat!', 'vendorhub' ),
            'user_id' => $user_id
        ) );
    }

    /**
     * AJAX handler to toggle verification status for a vendor
     */
    public static function handle_admin_toggle_verify() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! current_user_can( 'administrator' ) ) {
            wp_send_json_error( __( 'Akses ditolak.', 'vendorhub' ) );
        }

        $user_id = absint( $_POST['user_id'] );
        if ( ! $user_id ) {
            wp_send_json_error( __( 'ID User tidak valid.', 'vendorhub' ) );
        }

        $is_verified = get_user_meta( $user_id, 'vh_verified', true );
        $new_status  = ( $is_verified == '1' ) ? '0' : '1';

        update_user_meta( $user_id, 'vh_verified', $new_status );

        wp_send_json_success( array(
            'message' => ( $new_status == '1' ) ? __( 'Vendor telah terverifikasi!', 'vendorhub' ) : __( 'Verifikasi vendor dicabut.', 'vendorhub' ),
            'status'  => $new_status
        ) );
    }

    /**
     * AJAX handler for vendor to submit an advertisement
     */
    public static function handle_vendor_submit_ad() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! is_user_logged_in() || ! vh_is_vendor() ) {
            wp_send_json_error( __( 'Akses ditolak. Anda bukan Vendor.', 'vendorhub' ) );
        }

        $title   = sanitize_text_field( $_POST['ad_title'] );
        $package = sanitize_text_field( $_POST['ad_package'] );
        $user_id = get_current_user_id();

        if ( empty( $title ) || empty( $package ) || empty( $_FILES['ad_banner'] ) ) {
            wp_send_json_error( __( 'Lengkapi semua field dan unggah banner.', 'vendorhub' ) );
        }

        // Create Ad Post (Unpaid)
        $ad_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_type'    => 'vh_ad',
            'post_status'  => 'unpaid',
            'post_author'  => $user_id,
        ) );

        if ( is_wp_error( $ad_id ) ) {
            wp_send_json_error( $ad_id->get_error_message() );
        }

        // Handle Banner Upload
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attachment_id = media_handle_upload( 'ad_banner', $ad_id );

        if ( is_wp_error( $attachment_id ) ) {
            wp_delete_post( $ad_id, true );
            wp_send_json_error( __( 'Gagal mengunggah banner: ', 'vendorhub' ) . $attachment_id->get_error_message() );
        }

        // Set Featured Image
        set_post_thumbnail( $ad_id, $attachment_id );

        // Set Meta
        update_post_meta( $ad_id, '_vh_ad_package', $package );
        
        if ( !empty($_POST['ad_link_id']) ) {
            update_post_meta( $ad_id, '_vh_ad_link_id', absint($_POST['ad_link_id']) );
        }
        
        $durations = array( 'standar' => 7, 'premium' => 14, 'platinum' => 30 );
        $prices    = array( 'standar' => 50000, 'premium' => 150000, 'platinum' => 500000 );

        update_post_meta( $ad_id, '_vh_ad_duration', $durations[$package] ?? 7 );
        update_post_meta( $ad_id, '_vh_ad_price', $prices[$package] ?? 50000 );

        wp_send_json_success( array(
            'message' => __( 'Iklan berhasil dibuat! Silakan lakukan pembayaran untuk mengaktifkan.', 'vendorhub' ),
            'ad_id'   => $ad_id
        ) );
    }

    /**
     * AJAX handler to save general platform settings (limits)
     */
    public static function handle_admin_save_settings() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! current_user_can( 'administrator' ) ) {
            wp_send_json_error( __( 'Akses ditolak.', 'vendorhub' ) );
        }

        if ( isset($_POST['vh_limit_products']) ) {
            update_option( 'vh_limit_products', absint($_POST['vh_limit_products']) );
        }
        if ( isset($_POST['vh_limit_tenders']) ) {
            update_option( 'vh_limit_tenders', absint($_POST['vh_limit_tenders']) );
        }
        if ( isset($_POST['vh_google_client_id']) ) {
            update_option( 'vh_google_client_id', sanitize_text_field($_POST['vh_google_client_id']) );
        }
        if ( isset($_POST['vh_google_client_secret']) ) {
            update_option( 'vh_google_client_secret', sanitize_text_field($_POST['vh_google_client_secret']) );
        }
        if ( isset($_POST['vh_midtrans_client_key']) ) {
            update_option( 'vh_midtrans_client_key', sanitize_text_field($_POST['vh_midtrans_client_key']) );
        }
        if ( isset($_POST['vh_midtrans_server_key']) ) {
            update_option( 'vh_midtrans_server_key', sanitize_text_field($_POST['vh_midtrans_server_key']) );
        }
        if ( isset($_POST['vh_midtrans_mode']) ) {
            update_option( 'vh_midtrans_mode', sanitize_text_field($_POST['vh_midtrans_mode']) );
        }

        // SMTP Settings
        if ( isset($_POST['vh_smtp_host']) ) {
            update_option( 'vh_smtp_host', sanitize_text_field($_POST['vh_smtp_host']) );
        }
        if ( isset($_POST['vh_smtp_port']) ) {
            update_option( 'vh_smtp_port', absint($_POST['vh_smtp_port']) );
        }
        if ( isset($_POST['vh_smtp_encryption']) ) {
            update_option( 'vh_smtp_encryption', sanitize_text_field($_POST['vh_smtp_encryption']) );
        }
        if ( isset($_POST['vh_smtp_user']) ) {
            update_option( 'vh_smtp_user', sanitize_text_field($_POST['vh_smtp_user']) );
        }
        if ( isset($_POST['vh_smtp_pass']) ) {
            update_option( 'vh_smtp_pass', sanitize_text_field($_POST['vh_smtp_pass']) );
        }
        if ( isset($_POST['vh_from_email']) ) {
            update_option( 'vh_from_email', sanitize_email($_POST['vh_from_email']) );
        }
        if ( isset($_POST['vh_from_name']) ) {
            update_option( 'vh_from_name', sanitize_text_field($_POST['vh_from_name']) );
        }

        wp_send_json_success( array( 'message' => __( 'Pengaturan berhasil disimpan!', 'vendorhub' ) ) );
    }

    /**
     * AJAX handler to approve or delete an advertisement
     */
    public static function handle_admin_manage_ad() {
        check_ajax_referer( 'vh-auth-nonce', 'security' );

        if ( ! current_user_can( 'administrator' ) ) {
            wp_send_json_error( __( 'Akses ditolak.', 'vendorhub' ) );
        }

        $ad_id  = absint( $_POST['ad_id'] );
        $action = sanitize_text_field( $_POST['ad_action'] );

        if ( ! $ad_id ) {
            wp_send_json_error( __( 'ID Iklan tidak valid.', 'vendorhub' ) );
        }

        if ( $action === 'approve' ) {
            wp_update_post( array(
                'ID'          => $ad_id,
                'post_status' => 'publish'
            ) );
            wp_send_json_success( __( 'Iklan telah disetujui!', 'vendorhub' ) );
        } elseif ( $action === 'delete' ) {
            wp_delete_post( $ad_id, true );
            wp_send_json_success( __( 'Iklan telah dihapus.', 'vendorhub' ) );
        } else {
            wp_send_json_error( __( 'Aksi tidak valid.', 'vendorhub' ) );
        }
    }
}

VH_Admin_Manager::init();
