<?php
/**
 * NiagaHUB Data Seeder
 * Run this by visiting your-site.com/vh-seed.php once, then DELETE it.
 */

define('WP_USE_THEMES', false);
require_once('wp-load.php');

// Allow running without login just for seeding testing
if ( isset($_GET['auth']) && $_GET['auth'] === 'dev' ) {
    // skip auth
} elseif (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Unauthorized access. Use ?auth=dev for testing.');
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

function vh_attach_image_to_post($post_id, $file_path, $desc = '') {
    if (!file_exists($file_path)) return false;
    
    $upload_dir = wp_upload_dir();
    $filename = basename($file_path);
    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    copy($file_path, $file);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_content'   => $desc,
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($post_id, $attach_id);
    
    return $attach_id;
}

echo "Starting seeding process...<br>";

// 0. Create Test Users

// Helper to create vendor
function create_seed_vendor($username, $company, $logo_path = '') {
    $uid = username_exists($username);
    if (!$uid) {
        $uid = wp_create_user($username, 'password', $username.'@test.com');
        $user = new WP_User($uid);
        $user->set_role('subscriber');
        update_user_meta($uid, 'vh_role', 'vendor');
        update_user_meta($uid, 'vh_company_name', $company);
        echo "Created vendor '$username'<br>";
    }
    
    if ($logo_path && file_exists($logo_path)) {
        $existing_logo = get_user_meta($uid, 'vh_vendor_logo', true);
        if (!$existing_logo) {
            $attach_id = vh_attach_image_to_post(0, $logo_path, $company . ' Logo');
            if ($attach_id) {
                update_user_meta($uid, 'vh_vendor_logo', $attach_id);
                echo "Attached logo to '$username'<br>";
            }
        }
    }
    return $uid;
}

$vendor_id = create_seed_vendor('vendor', 'PT Surya Baja Mandiri', WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/banner1.png');
$vendor2_id = create_seed_vendor('vendor2', 'PT Krakatau Konstruksi', WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/banner2.png');
$vendor3_id = create_seed_vendor('vendor3', 'TechNusa Solutions', WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/banner3.png');

$buyer_id = username_exists('buyer');
if (!$buyer_id) {
    $buyer_id = wp_create_user('buyer', 'password', 'buyer@test.com');
    $user = new WP_User($buyer_id);
    $user->set_role('subscriber');
    update_user_meta($buyer_id, 'vh_role', 'buyer');
    update_user_meta($buyer_id, 'vh_company_name', 'PT Adhi Karya Logistik');
    echo "Created user 'buyer'<br>";
}


// 1. Create Industry Terms
$industries = [
    'Konstruksi & Perkakas',
    'Komputer & Teknologi',
    'Mesin Industri',
    'Otomotif & Transportasi',
    'Kebutuhan Kantor',
    'Energi & Listrik'
];

foreach ($industries as $ind) {
    if (!term_exists($ind, 'vh_industry')) {
        wp_insert_term($ind, 'vh_industry');
        echo "Created Industry: $ind<br>";
    }
}

// 2. Sample Products
$products = [
    [
        'title'   => 'Server Rack 2U Enterprise',
        'content' => 'Server rack berkualitas tinggi untuk kebutuhan enterprise.',
        'price'   => 15000000,
        'moq'     => 1,
        'unit'    => 'Unit',
        'term'    => 'Komputer & Teknologi',
        'image'   => WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/prod2.png'
    ],
    [
        'title'   => 'Baja Ringan Galvalum C75',
        'content' => 'Baja ringan standar SNI untuk rangka atap.',
        'price'   => 85000,
        'moq'     => 100,
        'unit'    => 'Batang',
        'term'    => 'Konstruksi & Perkakas',
        'image'   => WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/prod1.png'
    ],
    [
        'title'   => 'Industrial Generator 500kVA',
        'content' => 'High performance generator for heavy manufacturing.',
        'price'   => 60000000,
        'moq'     => 1,
        'unit'    => 'Set',
        'term'    => 'Mesin Industri',
        'image'   => WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/prod3.png'
    ]
];

$vendor_user = get_user_by('login', 'vendor');
$vendor_author_id = $vendor_user ? $vendor_user->ID : get_current_user_id();

foreach ($products as $p) {
    $post_id = wp_insert_post([
        'post_title'    => $p['title'],
        'post_content'  => $p['content'],
        'post_author'   => $vendor_author_id,
        'post_status'   => 'publish',
        'post_type'     => 'vh_product',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_vh_price', $p['price']);
        update_post_meta($post_id, '_vh_moq', $p['moq']);
        update_post_meta($post_id, '_vh_unit', $p['unit']);
        
        if (isset($p['image']) && $p['image']) {
            vh_attach_image_to_post($post_id, $p['image'], $p['title']);
        }

        $term = get_term_by('name', $p['term'], 'vh_industry');
        if ($term) {
            wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
        }
        echo "Created Product: " . $p['title'] . "<br>";
    }
}

// 3. Sample Tenders
$tenders = [
    [
        'title'    => 'Proyek Renovasi Gudang Logistik Tangerang',
        'content'  => 'Dibutuhkan kontraktor untuk renovasi atap dan lantai gudang seluas 2000m2.',
        'budget'   => 1200000000,
        'deadline' => '2026-04-15',
        'term'     => 'Konstruksi & Perkakas',
        'image'    => 'C:\Users\Arya\.gemini\antigravity\brain\68c218a9-d5a1-4033-8449-1a6a0e9b3b7c\hero_banner_b2b_industrial_1773475256422.png'
    ],
    [
        'title'    => 'Pengadaan 500 Unit Laptop Karyawan',
        'content'  => 'Spesifikasi minimal Core i5, RAM 16GB, SSD 512GB untuk kebutuhan operasional perusahaan.',
        'budget'   => 5000000000,
        'deadline' => '2026-06-30',
        'term'     => 'Komputer & Teknologi',
        'image'    => 'C:\Users\Arya\.gemini\antigravity\brain\68c218a9-d5a1-4033-8449-1a6a0e9b3b7c\hero_banner_b2b_supplies_1773475271737.png'
    ]
];

foreach ($tenders as $t) {
    $buyer_user = get_user_by('login', 'buyer');
    $buyer_author_id = $buyer_user ? $buyer_user->ID : get_current_user_id();

    $post_id = wp_insert_post([
        'post_title'    => $t['title'],
        'post_content'  => $t['content'],
        'post_author'   => $buyer_author_id,
        'post_status'   => 'publish',
        'post_type'     => 'vh_tender',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_vh_tender_budget', $t['budget']);
        update_post_meta($post_id, '_vh_tender_deadline', $t['deadline']);

        if (isset($t['image']) && $t['image']) {
            vh_attach_image_to_post($post_id, $t['image'], $t['title']);
        }

        $term = get_term_by('name', $t['term'], 'vh_industry');
        if ($term) {
            wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
        }
        echo "Created Tender: " . $t['title'] . "<br>";
    }
}

echo "<br><b>Seeding Completed! Please delete this file for security.</b>";
