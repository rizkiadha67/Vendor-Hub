<?php
/**
 * Fix Vendor Logos - NiagaHUB
 */
define('WP_USE_THEMES', false);
require_once('wp-load.php');

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Unauthorized access.');
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

function vh_assign_logo($username, $file_path) {
    if (!file_exists($file_path)) {
        echo "File not found: $file_path <br>";
        return;
    }

    $user = get_user_by('login', $username);
    if (!$user) {
        echo "User not found: $username <br>";
        return;
    }

    $attachment_id = media_handle_sideload([
        'name'     => basename($file_path),
        'tmp_name' => $file_path,
    ], 0);

    // Note: media_handle_sideload might delete the source file if it was a tmp file, 
    // but here it's a persistent file. However, for media_handle_sideload to work 
    // with a local path, it expects a $_FILES array structure. 
    // Let's use a simpler way since we have the absolute path.
    
    // Better way for local files:
    $upload_dir = wp_upload_dir();
    $filename = basename($file_path);
    $target_path = $upload_dir['path'] . '/' . $filename;
    copy($file_path, $target_path);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $target_path);
    $attach_data = wp_generate_attachment_metadata($attach_id, $target_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    if ($attach_id) {
        update_user_meta($user->ID, 'vh_vendor_logo', $attach_id);
        update_user_meta($user->ID, 'vh_verified', '1'); // Also ensure verified
        echo "Successfully updated logo for $username (ID: {$user->ID}) <br>";
    }
}

echo "Updating Vendor Logos...<br>";

// Maps usernames to the generated image paths
$logo_map = [
    'vendor_tekno'      => 'C:\\Users\\Arya\\.gemini\\antigravity\\brain\\5d1dacc3-0415-4c40-9775-381bd0a3e33a\\vendor_logo_tech_1773553440214.png',
    'vendor_konstruksi' => 'C:\\Users\\Arya\\.gemini\\antigravity\\brain\\5d1dacc3-0415-4c40-9775-381bd0a3e33a\\vendor_logo_construction_1773553463338.png',
    'vendor_kantor'     => 'C:\\Users\\Arya\\.gemini\\antigravity\\brain\\5d1dacc3-0415-4c40-9775-381bd0a3e33a\\vendor_logo_office_1773553480935.png',
    'vendor3'           => 'C:\\Users\\Arya\\.gemini\\antigravity\\brain\\5d1dacc3-0415-4c40-9775-381bd0a3e33a\\vendor_logo_tech_1773553440214.png' // Also for TechNusa
];

foreach ($logo_map as $username => $path) {
    vh_assign_logo($username, $path);
}

echo "<br><b>Update Completed!</b>";
