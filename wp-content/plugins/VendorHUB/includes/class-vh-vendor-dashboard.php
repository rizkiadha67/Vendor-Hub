<?php
/**
 * Frontend Vendor Dashboard CRUD for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Vendor_Dashboard {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'handle_form_submissions' ) );
    }

    /**
     * Handle Product CRUD Submissions
     */
    public static function handle_form_submissions() {
        if ( ! is_user_logged_in() || ! vh_is_vendor() ) {
            return;
        }

        if ( ! isset( $_POST['vh_vendor_nonce'] ) || ! wp_verify_nonce( $_POST['vh_vendor_nonce'], 'vh_vendor_action' ) ) {
            return;
        }

        $action = isset( $_POST['vh_action'] ) ? sanitize_text_field( $_POST['vh_action'] ) : '';

        if ( $action === 'save_product' ) {
            if ( ! vh_profile_is_complete() || ! vh_is_verified() ) {
                wp_die( __('Anda harus melengkapi profil perusahaan dan divalidasi oleh Admin sebelum dapat mengunggah produk.', 'vendorhub') );
            }
            self::save_product();
        } elseif ( $action === 'delete_product' ) {
            self::delete_product();
        } elseif ( $action === 'save_tender' ) {
            if ( ! vh_profile_is_complete() || ! vh_is_verified() ) {
                wp_die( __('Anda harus melengkapi profil perusahaan dan divalidasi oleh Admin sebelum dapat membuat tender.', 'vendorhub') );
            }
            self::save_tender();
        } elseif ( $action === 'delete_tender' ) {
            self::delete_tender();
        } elseif ( $action === 'update_verification' ) {
            self::update_verification();
        }
    }

    private static function save_tender() {
        $tender_id = isset( $_POST['tender_id'] ) ? intval( $_POST['tender_id'] ) : 0;
        $title = sanitize_text_field( $_POST['tender_name'] );
        $content = wp_kses_post( $_POST['tender_description'] );
        $budget = floatval( $_POST['tender_budget'] );
        $deadline = sanitize_text_field( $_POST['tender_deadline'] );
        $industry = intval( $_POST['tender_industry'] );

        $post_data = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'vh_tender',
            'post_author'  => get_current_user_id()
        );

        if ( $tender_id > 0 ) {
            $post_data['ID'] = $tender_id;
            wp_update_post( $post_data );
        } else {
            $tender_id = wp_insert_post( $post_data );
        }

        if ( $tender_id ) {
            update_post_meta( $tender_id, '_vh_tender_budget', $budget );
            update_post_meta( $tender_id, '_vh_tender_deadline', $deadline );
            
            if ( $industry ) {
                wp_set_post_terms( $tender_id, array( $industry ), 'vh_industry' );
            }

            if ( ! empty( $_FILES['tender_image']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                $attachment_id = media_handle_upload( 'tender_image', $tender_id );
                if ( ! is_wp_error( $attachment_id ) ) {
                    set_post_thumbnail( $tender_id, $attachment_id );
                }
            }

            wp_redirect( add_query_arg( array( 'section' => 'tenders', 'message' => 'saved' ), site_url('/dashboard') ) );
            exit;
        }
    }

    private static function delete_tender() {
        $tender_id = isset( $_POST['tender_id'] ) ? intval( $_POST['tender_id'] ) : 0;
        $post = get_post( $tender_id );
        if ( $post && $post->post_author == get_current_user_id() ) {
            wp_delete_post( $tender_id, true );
            wp_redirect( add_query_arg( array( 'section' => 'tenders', 'message' => 'deleted' ), site_url('/dashboard') ) );
            exit;
        }
    }

    private static function update_verification() {
        $user_id = get_current_user_id();
        
        // Basic Info
        if ( isset( $_POST['company_name'] ) ) update_user_meta( $user_id, 'vh_company_name', sanitize_text_field( $_POST['company_name'] ) );
        if ( isset( $_POST['tagline'] ) )      update_user_meta( $user_id, 'vh_tagline', sanitize_text_field( $_POST['tagline'] ) );
        if ( isset( $_POST['description'] ) )  update_user_meta( $user_id, 'vh_description', sanitize_textarea_field( $_POST['description'] ) );
        
        // Legal & Category
        if ( isset( $_POST['business_type'] ) ) update_user_meta( $user_id, 'vh_business_type', sanitize_text_field( $_POST['business_type'] ) );
        if ( isset( $_POST['nib_number'] ) )    update_user_meta( $user_id, 'vh_nib', sanitize_text_field( $_POST['nib_number'] ) );
        if ( isset( $_POST['industry'] ) )      update_user_meta( $user_id, 'vh_industry', sanitize_text_field( $_POST['industry'] ) );
        
        // Contact
        if ( isset( $_POST['business_email'] ) ) update_user_meta( $user_id, 'vh_business_email', sanitize_email( $_POST['business_email'] ) );
        if ( isset( $_POST['wa_number'] ) )      update_user_meta( $user_id, 'vh_wa_number', sanitize_text_field( $_POST['wa_number'] ) );
        if ( isset( $_POST['website'] ) )       update_user_meta( $user_id, 'vh_website', esc_url_raw( $_POST['website'] ) );
        
        // Location & Scale
        if ( isset( $_POST['location'] ) )  update_user_meta( $user_id, 'vh_location', sanitize_text_field( $_POST['location'] ) );
        if ( isset( $_POST['address'] ) )   update_user_meta( $user_id, 'vh_address', sanitize_textarea_field( $_POST['address'] ) );
        if ( isset( $_POST['biz_scale'] ) ) update_user_meta( $user_id, 'vh_biz_scale', sanitize_text_field( $_POST['biz_scale'] ) );
        if ( isset( $_POST['est_year'] ) )  update_user_meta( $user_id, 'vh_est_year', sanitize_text_field( $_POST['est_year'] ) );

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        // Handle NIB File Upload
        if ( ! empty( $_FILES['nib_file']['name'] ) ) {
            $attachment_id = media_handle_upload( 'nib_file', 0 );
            if ( ! is_wp_error( $attachment_id ) ) {
                update_user_meta( $user_id, 'vh_nib_file', $attachment_id );
            }
        }

        // Handle Logo Upload
        if ( ! empty( $_FILES['vendor_logo']['name'] ) ) {
            $logo_id = media_handle_upload( 'vendor_logo', 0 );
            if ( ! is_wp_error( $logo_id ) ) {
                update_user_meta( $user_id, 'vh_vendor_logo', $logo_id );
            }
        }

        wp_redirect( add_query_arg( array( 'section' => 'settings', 'message' => 'profile_updated' ), site_url('/dashboard') ) );
        exit;
    }

    private static function save_product() {
        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $title = sanitize_text_field( $_POST['product_name'] );
        $content = wp_kses_post( $_POST['product_description'] );
        $price = floatval( $_POST['product_price'] );
        $moq = intval( $_POST['product_moq'] );
        $unit = sanitize_text_field( $_POST['product_unit'] );
        $industry = intval( $_POST['product_industry'] );

        $post_data = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'vh_product',
            'post_author'  => get_current_user_id()
        );

        if ( $product_id > 0 ) {
            // Update
            $post_data['ID'] = $product_id;
            wp_update_post( $post_data );
        } else {
            // Create
            $product_id = wp_insert_post( $post_data );
        }

        if ( $product_id ) {
            update_post_meta( $product_id, '_vh_price', $price );
            update_post_meta( $product_id, '_vh_moq', $moq );
            update_post_meta( $product_id, '_vh_unit', $unit );
            
            if ( $industry ) {
                wp_set_post_terms( $product_id, array( $industry ), 'vh_industry' );
            }

            // Handle Image
            if ( ! empty( $_FILES['product_image']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );

                $attachment_id = media_handle_upload( 'product_image', $product_id );
                if ( ! is_wp_error( $attachment_id ) ) {
                    set_post_thumbnail( $product_id, $attachment_id );
                }
            }

            wp_redirect( add_query_arg( array( 'section' => 'products', 'message' => 'saved' ), site_url('/dashboard') ) );
            exit;
        }
    }

    private static function delete_product() {
        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $post = get_post( $product_id );

        if ( $post && $post->post_author == get_current_user_id() ) {
            wp_delete_post( $product_id, true );
            wp_redirect( add_query_arg( array( 'section' => 'products', 'message' => 'deleted' ), site_url('/dashboard') ) );
            exit;
        }
    }
}

VH_Vendor_Dashboard::init();
