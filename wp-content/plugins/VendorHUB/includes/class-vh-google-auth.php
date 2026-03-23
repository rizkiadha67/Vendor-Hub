<?php
/**
 * Google OAuth 2.0 Handler for VendorHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Google_Auth {

    public static function init() {
        // Only run if credentials are set
        $client_id     = get_option( 'vh_google_client_id' );
        $client_secret = get_option( 'vh_google_client_secret' );

        if ( ! $client_id || ! $client_secret ) {
            return;
        }

        add_action( 'init', array( __CLASS__, 'handle_google_login_request' ) );
    }

    /**
     * Listen for vh_google_login and callback
     */
    public static function handle_google_login_request() {
        if ( isset( $_GET['vh_google_login'] ) ) {
            self::redirect_to_google();
        }

        if ( isset( $_GET['code'] ) && isset( $_GET['state'] ) && $_GET['state'] === 'vh_google_state' ) {
            self::handle_callback();
        }
    }

    /**
     * Redirect user to Google Auth Server
     */
    private static function redirect_to_google() {
        $client_id = get_option( 'vh_google_client_id' );
        
        $redirect_uri = home_url( '/auth/' );
        $scope        = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
        $state        = 'vh_google_state';
        
        $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query( array(
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => $scope,
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ) );

        wp_redirect( $auth_url );
        exit;
    }

    /**
     * Handle the callback from Google
     */
    private static function handle_callback() {
        $code          = $_GET['code'];
        $client_id     = get_option( 'vh_google_client_id' );
        $client_secret = get_option( 'vh_google_client_secret' );
        $redirect_uri  = home_url( '/auth/' );

        // Exchange code for token
        $response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
            'body' => array(
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            wp_die( 'Google OAuth Error: ' . $response->get_error_message() );
        }

        $params = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $params['access_token'] ) ) {
            $access_token = $params['access_token'];

            // Get user info
            $info_response = wp_remote_get( 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $access_token );
            $user_info     = json_decode( wp_remote_retrieve_body( $info_response ), true );

            if ( isset( $user_info['email'] ) ) {
                self::process_user_login( $user_info );
            }
        } else {
            wp_die( 'Failed to retrieve access token.' );
        }
    }

    /**
     * Login or Register the user
     */
    private static function process_user_login( $user_info ) {
        $email = $user_info['email'];
        $user  = get_user_by( 'email', $email );

        if ( ! $user ) {
            // Register new user
            $base_user = strtok($email, '@');
            $username  = sanitize_user($base_user) . '_' . wp_generate_password( 4, false );
            $password  = wp_generate_password( 12, true );
            
            $user_id = wp_create_user( $username, $password, $email );
            
            if ( is_wp_error( $user_id ) ) {
                wp_die( 'Registration failed: ' . $user_id->get_error_message() );
            }

            // Set default role (Buyer) or meta
            $user = get_user_by( 'id', $user_id );
            $user->set_role( 'subscriber' ); // Default to buyer/subscriber
            update_user_meta( $user_id, 'vh_is_buyer', '1' );
            if ( isset($user_info['name']) ) update_user_meta( $user_id, 'vh_full_name', $user_info['name'] );
        }

        // Login user
        wp_clear_auth_cookie();
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );

        // Redirect to dashboard
        wp_redirect( site_url( '/dashboard' ) );
        exit;
    }
}

VH_Google_Auth::init();
