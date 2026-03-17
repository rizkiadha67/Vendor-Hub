<?php
/**
 * Seeder Update
 */
define('WP_USE_THEMES', false);
require_once('wp-load.php');

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Unauthorized access.');
}

// Update images for existing posts if they exist
$products = get_posts(['post_type' => 'vh_product', 'numberposts' => -1]);
$i = 1;
foreach ($products as $p) {
    // We don't have a real media library upload here easily via script without upload_bits
    // But we can link to the theme assets for now as a fallback or just use the filenames
    update_post_meta($p->ID, '_vh_price', rand(500, 5000) * 1000); // Random price
    echo "Updated Product: {$p->post_title}<br>";
}

echo "Done.";
