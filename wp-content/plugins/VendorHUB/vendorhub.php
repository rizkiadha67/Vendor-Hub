<?php
/**
 * Plugin Name: NiagaHUB
 * Plugin URI: https://github.com/your-repo/vendorhub
 * Description: B2B Marketplace Vendor & Procurement Platform for WordPress.
 * Version: 1.0.0
 * Author: Antigravity AI
 * Author URI: https://github.com/antigravity
 * License: GPL2
 * Text Domain: vendorhub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Constants
define( 'VENDORHUB_VERSION', '1.0.1' );
define( 'VENDORHUB_PATH', plugin_dir_path( __FILE__ ) );
define( 'VENDORHUB_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main VendorHub Class
 */
class VendorHub {

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();

        // Initialize core components
        if ( class_exists( 'VH_Admin_Manager' ) ) { VH_Admin_Manager::init(); }
        if ( class_exists( 'VH_Proposal_Manager' ) ) { VH_Proposal_Manager::init(); }
        if ( class_exists( 'VH_Post_Types' ) ) { VH_Post_Types::init(); }
        if ( class_exists( 'VH_Rating' ) ) { VH_Rating::init(); }
        if ( class_exists( 'VH_Notifications' ) ) { VH_Notifications::init(); }
        if ( class_exists( 'VH_Google_Auth' ) ) { VH_Google_Auth::init(); }
        if ( class_exists( 'VH_Payment' ) ) { VH_Payment::init(); }
        if ( class_exists( 'VH_Email' ) ) { VH_Email::init(); }
	}

	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		require_once VENDORHUB_PATH . 'includes/class-vh-post-types.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-shortcodes.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-vendor.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-rfq.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-auth.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-messages.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-importer.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-seeder.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-vendor-dashboard.php';
		// New features
		require_once VENDORHUB_PATH . 'includes/class-vh-proposal.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-membership.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-rating.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-payment.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-notifications.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-admin-manager.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-google-auth.php';
		require_once VENDORHUB_PATH . 'includes/class-vh-email.php';
	}

	/**
	 * Register hooks.
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        
        add_action( 'init', array( $this, 'register_roles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

    /**
     * Enqueue CSS and JS
     */
    public function enqueue_assets() {
        wp_enqueue_style( 'vendorhub-styles', VENDORHUB_URL . 'assets/css/vendorhub-styles.css', array(), VENDORHUB_VERSION );
        wp_enqueue_script( 'vendorhub-scripts', VENDORHUB_URL . 'assets/js/vendorhub-scripts.js', array('jquery'), VENDORHUB_VERSION, true );
        
        wp_localize_script( 'vendorhub-scripts', 'vh_auth_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'vh-auth-nonce' ),
        ) );
    }

	/**
	 * Activation Hook
	 */
	public function activate() {
		$this->register_roles();
		flush_rewrite_rules();
	}

	/**
	 * Deactivation Hook
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Register User Roles: Vendor and Buyer
	 */
	public function register_roles() {
		add_role( 'vendor', __( 'Vendor', 'vendorhub' ), array(
			'read' => true,
			'edit_posts' => true,
			'delete_posts' => false,
			'publish_posts' => true,
			'upload_files' => true,
		) );

		add_role( 'buyer', __( 'Buyer', 'vendorhub' ), array(
			'read' => true,
			'edit_posts' => true,
			'delete_posts' => false,
			'publish_posts' => true,
			'upload_files' => true,
		) );
        if ( class_exists( 'VH_Admin_Manager' ) ) { VH_Admin_Manager::init(); }
        if ( class_exists( 'VH_Notifications' ) ) { VH_Notifications::init(); }
        if ( class_exists( 'VH_Google_Auth' ) ) { VH_Google_Auth::init(); }
        if ( class_exists( 'VH_Payment' ) ) { VH_Payment::init(); }
        if ( class_exists( 'VH_Email' ) ) { VH_Email::init(); }
	}
}

// Initialize the plugin
VendorHub::get_instance();
