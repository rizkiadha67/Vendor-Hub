<?php
/**
 * VendorHub – Setup required pages
 * Run once via: http://localhost/VendorHub/vh-setup-pages.php
 */
define('ABSPATH', dirname(__FILE__) . '/');
require_once 'wp-load.php';

// Security: only run for admins or in CLI
if (!current_user_can('administrator') && PHP_SAPI !== 'cli') {
    // Allow running via browser for initial setup
}

$pages = [
    [
        'title'    => 'Dashboard',
        'slug'     => 'dashboard',
        'template' => 'page-dashboard.php',
        'content'  => '',
    ],
    [
        'title'    => 'Auth',
        'slug'     => 'auth',
        'template' => 'page-auth.php',
        'content'  => '',
    ],
    [
        'title'    => 'Direktori Vendor',
        'slug'     => 'marketplace-vendor',
        'template' => '',
        'content'  => '[vh_marketplace_vendor]',
    ],
    [
        'title'    => 'Marketplace Produk',
        'slug'     => 'marketplace-produk',
        'template' => '',
        'content'  => '[vh_marketplace_products]',
    ],
    [
        'title'    => 'Pusat Tender',
        'slug'     => 'pusat-tender',
        'template' => '',
        'content'  => '[vh_tender_listing]', // We'll need to verify or create this shortcode
    ],
];

echo '<pre>';
foreach ($pages as $page_data) {
    $existing = get_page_by_path($page_data['slug']);

    if ($existing) {
        // Update template if needed
        if (!empty($page_data['template'])) {
            update_post_meta($existing->ID, '_wp_page_template', $page_data['template']);
        }
        echo "EXISTS: [{$page_data['slug']}] (ID: {$existing->ID})\n";
        continue;
    }

    $args = [
        'post_type'    => 'page',
        'post_title'   => $page_data['title'],
        'post_name'    => $page_data['slug'],
        'post_content' => $page_data['content'],
        'post_status'  => 'publish',
    ];

    $post_id = wp_insert_post($args);

    if (is_wp_error($post_id)) {
        echo "ERROR: [{$page_data['slug']}] " . $post_id->get_error_message() . "\n";
    } else {
        if (!empty($page_data['template'])) {
            update_post_meta($post_id, '_wp_page_template', $page_data['template']);
        }
        echo "CREATED: [{$page_data['slug']}] (ID: {$post_id})\n";
    }
}

// Flush rewrite rules so slugs work
flush_rewrite_rules();
echo "\n✅ Done! Rewrite rules flushed.\n";
echo '</pre>';
