<?php
/**
 * Messages System for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Messages {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_type' ) );
        add_action( 'wp_ajax_vh_send_message', array( __CLASS__, 'handle_send_message' ) );
        add_action( 'wp_ajax_nopriv_vh_send_message', array( __CLASS__, 'handle_send_message' ) );
    }

    public static function register_post_type() {
        register_post_type( 'vh_message', array(
            'labels' => array(
                'name' => 'Messages',
                'singular_name' => 'Message',
            ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'editor', 'author' ),
        ) );
    }

    public static function handle_send_message() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'Login required.' );
        }

        $to_user_id   = intval( $_POST['to_user_id'] );
        $message_text = sanitize_textarea_field( $_POST['message'] );
        $thread_id    = sanitize_text_field( $_POST['thread_id'] ?? '' ); 

        if ( ! $to_user_id || ! $message_text ) {
            wp_send_json_error( 'Invalid data. ID Penerima dan Pesan wajib diisi.' );
        }

        $post_id = wp_insert_post( array(
            'post_title'   => 'Msg to User #' . $to_user_id,
            'post_content' => $message_text,
            'post_status'  => 'publish',
            'post_type'    => 'vh_message',
            'post_author'  => get_current_user_id(),
        ) );

        if ( $post_id ) {
            update_post_meta( $post_id, '_vh_msg_to', $to_user_id );
            
            if ( $thread_id ) {
                update_post_meta( $post_id, '_vh_msg_thread', $thread_id );
            }

            // Trigger notification
            if ( class_exists( 'VH_Notifications' ) ) {
                $sender_name = wp_get_current_user()->display_name;
                VH_Notifications::create(
                    $to_user_id,
                    '📩 Pesan Baru',
                    $sender_name . ' mengirimkan pesan baru: "' . wp_trim_words($message_text, 10) . '"',
                    add_query_arg('tab', 'inbox', site_url('/dashboard')),
                    'info'
                );
            }

            wp_send_json_success( array(
                'message' => 'Pesan terkirim!',
                'id'      => $post_id
            ) );
        } else {
            wp_send_json_error( 'Gagal mengirim pesan.' );
        }
    }
}

VH_Messages::init();
