<?php
/**
 * Midtrans Payment Integration for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Payment {

    public static function init() {
        add_action('wp_ajax_vh_get_snap_token', [__CLASS__, 'handle_get_snap_token']);
        // Webhook handler (accessible from outside)
        add_action('wp_ajax_nopriv_vh_midtrans_webhook', [__CLASS__, 'handle_webhook']);
        add_action('wp_ajax_vh_midtrans_webhook', [__CLASS__, 'handle_webhook']);
    }

    /**
     * Get Midtrans Server Key
     */
    private static function get_server_key() {
        return get_option('vh_midtrans_server_key');
    }

    /**
     * Get API URL based on mode
     */
    private static function get_api_url() {
        $mode = get_option('vh_midtrans_mode', 'sandbox');
        return ($mode === 'production') 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * AJAX: Generate Snap Token
     */
    public static function handle_get_snap_token() {
        check_ajax_referer('vh-auth-nonce', 'security');
        
        $ad_id = intval($_POST['ad_id']);
        if (!$ad_id) wp_send_json_error('Invalid Ad ID');

        $ad = get_post($ad_id);
        if ($ad->post_author != get_current_user_id()) {
            wp_send_json_error('Permission denied');
        }

        $price = get_post_meta($ad_id, '_vh_ad_price', true) ?: 50000;
        $package = get_post_meta($ad_id, '_vh_ad_package', true);
        $user = wp_get_current_user();

        $order_id = 'AD-' . $ad_id . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int)$price,
            ],
            'item_details' => [[
                'id' => 'package-' . $package,
                'price' => (int)$price,
                'quantity' => 1,
                'name' => 'Paket Iklan ' . ucfirst($package),
            ]],
            'customer_details' => [
                'first_name' => $user->display_name,
                'email' => $user->user_email,
            ],
        ];

        $server_key = self::get_server_key();
        if (!$server_key) wp_send_json_error('Midtrans not configured');

        $response = wp_remote_post(self::get_api_url(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($server_key . ':'),
            ],
            'body' => json_encode($params),
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (isset($body->token)) {
            update_post_meta($ad_id, '_vh_ad_order_id', $order_id);
            wp_send_json_success(['token' => $body->token]);
        } else {
            wp_send_json_error($body->error_messages ?? 'Gagal mendapatkan Snap Token');
        }
    }

    /**
     * Webhook Handler for Midtrans Notifications
     */
    public static function handle_webhook() {
        $json = file_get_contents('php://input');
        $notif = json_decode($json);

        if (!$notif) exit;

        $order_id = $notif->order_id;
        $status   = $notif->transaction_status;

        // Extract Ad ID from order_id (Format: AD-{ID}-{TIME})
        $parts = explode('-', $order_id);
        $ad_id = isset($parts[1]) ? intval($parts[1]) : 0;

        if (!$ad_id) exit;

        if ($status == 'settlement' || $status == 'capture') {
            // Payment success
            wp_update_post([
                'ID' => $ad_id,
                'post_status' => 'pending' // Move to pending for admin approval
            ]);
            update_post_meta($ad_id, '_vh_ad_paid', '1');
            update_post_meta($ad_id, '_vh_ad_payment_time', current_time('mysql'));
        } elseif ($status == 'pending') {
             update_post_meta($ad_id, '_vh_ad_paid', '0');
        } else {
            // Failed/Expired
            update_post_meta($ad_id, '_vh_ad_paid', 'error');
        }

        echo "OK";
        exit;
    }
}

VH_Payment::init();
