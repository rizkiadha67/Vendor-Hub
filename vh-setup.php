<?php
/**
 * NiagaHUB Complete Setup & Seeder
 * Run this by visiting your-site.com/vh-setup.php?auth=dev
 * This script will:
 * 1. Create necessary pages (Auth, Dashboard, Marketplace, etc.)
 * 2. Setup Industries (Indonesian)
 * 3. Seed Sample Content (Vendors, Products, Tenders)
 * 4. Flush Rewrite Rules
 */

define('WP_USE_THEMES', false);
require_once('wp-load.php');

// Security check
if ( PHP_SAPI !== 'cli' ) {
    if ( !isset($_GET['auth']) || $_GET['auth'] !== 'dev' ) {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            die('Unauthorized. Use ?auth=dev to bypass for initial setup.');
        }
    }
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

echo "<h1>NiagaHUB Setup & Seeder</h1>";
echo "<p>Starting setup... Please do not close this tab.</p>";

// --- 1. SETUP PAGES ---
echo "<h3>1. Setting Up Pages...</h3>";
$pages = [
    [
        'title'    => 'Auth',
        'slug'     => 'auth',
        'template' => 'page-auth.php',
        'content'  => '',
    ],
    [
        'title'    => 'Dashboard',
        'slug'     => 'dashboard',
        'template' => 'page-dashboard.php',
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
        'content'  => '[vh_tender_listing]',
    ],
    [
        'title'    => 'Artikel',
        'slug'     => 'artikel',
        'template' => 'page-artikel.php',
        'content'  => '',
    ],
];

foreach ($pages as $p) {
    $existing = get_page_by_path($p['slug']);
    if (!$existing) {
        $post_id = wp_insert_post([
            'post_type'    => 'page',
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_content' => $p['content'],
            'post_status'  => 'publish',
        ]);
        if ($post_id) {
            if ($p['template']) update_post_meta($post_id, '_wp_page_template', $p['template']);
            echo "✅ Created page: <b>{$p['title']}</b> (/{$p['slug']})<br>";
        }
    } else {
        if ($p['template']) update_post_meta($existing->ID, '_wp_page_template', $p['template']);
        echo "ℹ️ Page exists: <b>{$p['title']}</b><br>";
    }
}
flush_rewrite_rules();
echo "✅ Rewrite rules flushed.<br>";


// --- 2. SETUP INDUSTRIES ---
echo "<h3>2. Setting Up Industries...</h3>";
$industries = [
    'Konstruksi & Perkakas',
    'Komputer & Teknologi',
    'Mesin Industri',
    'Otomotif & Transportasi',
    'Kebutuhan Kantor',
    'Energi & Listrik'
];

$term_ids = [];
foreach ($industries as $ind) {
    $term = term_exists($ind, 'vh_industry');
    if (!$term) {
        $term = wp_insert_term($ind, 'vh_industry');
        echo "✅ Created Industry: <b>$ind</b><br>";
    } else {
        echo "ℹ️ Industry exists: <b>$ind</b><br>";
    }
    $term_ids[$ind] = is_array($term) ? $term['term_id'] : $term;
}


// --- 3. SEED USERS ---
echo "<h3>3. Seeding Sample Users...</h3>";

function create_vh_user($username, $company, $role = 'vendor') {
    $uid = username_exists($username);
    if (!$uid) {
        $uid = wp_create_user($username, 'password', $username.'@niagahub.test');
        $user = new WP_User($uid);
        $user->set_role('subscriber'); // Default WP role
        update_user_meta($uid, 'vh_role', $role);
        update_user_meta($uid, 'vh_company_name', $company);
        update_user_meta($uid, 'vh_verified', '1');
        echo "✅ Created $role: <b>$username</b> (Pass: password)<br>";
    } else {
        echo "ℹ️ User exists: <b>$username</b><br>";
    }
    return $uid;
}

$v1 = create_vh_user('vendor_surya', 'PT Surya Baja Mandiri', 'vendor');
$v2 = create_vh_user('vendor_krakatau', 'PT Krakatau Konstruksi', 'vendor');
$b1 = create_vh_user('buyer_adhi', 'PT Adhi Karya Logistik', 'buyer');

// Simpler accounts for quick testing
$v_simple = create_vh_user('vendor', 'Sample Vendor PT', 'vendor');
$b_simple = create_vh_user('buyer', 'Sample Buyer PT', 'buyer');


// --- 4. SEED CONTENT ---
echo "<h3>4. Seeding Sample Products & Tenders...</h3>";

// Helper for images (uses theme assets)
function vh_get_asset_image($filename) {
    $path = WP_CONTENT_DIR . '/themes/NiagaHUB-Theme/assets/images/' . $filename;
    return file_exists($path) ? $path : false;
}

function vh_attach_asset_image($post_id, $filename) {
    $file_path = vh_get_asset_image($filename);
    if (!$file_path) return false;

    $upload_dir = wp_upload_dir();
    $dest = $upload_dir['path'] . '/' . basename($file_path);
    copy($file_path, $dest);

    $wp_filetype = wp_check_filetype($dest, null);
    $attachment = [
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $dest, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $dest);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($post_id, $attach_id);
    return $attach_id;
}

$products = [
    [
        'title'   => 'Baja Ringan Galvalum C75',
        'content' => 'Baja ringan standar SNI untuk rangka atap berkualtas tinggi.',
        'price'   => 85000,
        'moq'     => 100,
        'unit'    => 'Batang',
        'term'    => 'Konstruksi & Perkakas',
        'img'     => 'prod1.png',
        'author'  => $v1
    ],
    [
        'title'   => 'Server Rack 2U Enterprise',
        'content' => 'Server rack berkualitas tinggi untuk kebutuhan enterprise.',
        'price'   => 15000000,
        'moq'     => 1,
        'unit'    => 'Unit',
        'term'    => 'Komputer & Teknologi',
        'img'     => 'prod2.png',
        'author'  => $v2
    ],
    [
        'title'   => 'Industrial Generator 500kVA',
        'content' => 'High performance generator for heavy manufacturing.',
        'price'   => 450000000,
        'moq'     => 1,
        'unit'    => 'Set',
        'term'    => 'Mesin Industri',
        'img'     => 'prod3.png',
        'author'  => $v1
    ]
];

foreach ($products as $p) {
    if (get_page_by_title($p['title'], OBJECT, 'vh_product')) continue;
    
    $post_id = wp_insert_post([
        'post_title'   => $p['title'],
        'post_content' => $p['content'],
        'post_author'  => $p['author'],
        'post_status'  => 'publish',
        'post_type'    => 'vh_product',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_vh_price', $p['price']);
        update_post_meta($post_id, '_vh_moq', $p['moq']);
        update_post_meta($post_id, '_vh_unit', $p['unit']);
        vh_attach_asset_image($post_id, $p['img']);
        
        $term = get_term_by('name', $p['term'], 'vh_industry');
        if ($term) wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
        
        echo "✅ Created Product: <b>{$p['title']}</b><br>";
    }
}

$tenders = [
    [
        'title'    => 'Proyek Konstruksi Gudang Logistik',
        'content'  => 'Dibutuhkan kontraktor untuk renovasi atap dan lantai gudang seluas 2000m2.',
        'budget'   => 1200000000,
        'deadline' => '2026-06-15',
        'term'     => 'Konstruksi & Perkakas',
        'img'      => 'banner1.png',
    ],
    [
        'title'    => 'Pengadaan IT Hardware Karyawan',
        'content'  => 'Spesifikasi minimal Core i5, RAM 16GB untuk kebutuhan operasional perusahaan.',
        'budget'   => 5000000000,
        'deadline' => '2026-04-30',
        'term'     => 'Komputer & Teknologi',
        'img'      => 'banner2.png',
    ]
];

foreach ($tenders as $t) {
    if (get_page_by_title($t['title'], OBJECT, 'vh_tender')) continue;
    
    $post_id = wp_insert_post([
        'post_title'   => $t['title'],
        'post_content' => $t['content'],
        'post_author'  => $b1,
        'post_status'  => 'publish',
        'post_type'    => 'vh_tender',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_vh_tender_budget', $t['budget']);
        update_post_meta($post_id, '_vh_tender_deadline', $t['deadline']);
        vh_attach_asset_image($post_id, $t['img']);
        
        $term = get_term_by('name', $t['term'], 'vh_industry');
        if ($term) wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
        
        echo "✅ Created Tender: <b>{$t['title']}</b><br>";
    }
}

// --- 5. SEED ARTICLES ---
echo "<h3>5. Seeding Sample Articles...</h3>";
$articles = [
    [
        'title'   => 'Tips Memilih Vendor Konstruksi Terpercaya',
        'content' => 'Dalam proyek besar, memilih sub-kontraktor atau vendor material adalah kunci keberhasilan. Pastikan mereka memiliki NIB dan portofolio yang jelas.',
        'img'     => 'banner1.png',
    ],
    [
        'title'   => 'Tren Digitalisasi Procurement di Tahun 2026',
        'content' => 'Dunia e-procurement berkembang pesat. Perusahaan yang beralih ke platform digital seperti NiagaHUB terbukti menghemat biaya hingga 20%.',
        'img'     => 'banner2.png',
    ],
    [
        'title'   => 'Pentingnya Maintenance Mesin Industri Secara Berkala',
        'content' => 'Jangan tunggu rusak. Melakukan maintenance rutin pada mesin industri dapat memperpanjang usia pakai aset perusahaan Anda secara signifikan.',
        'img'     => 'prod3.png',
    ]
];

foreach ($articles as $a) {
    if (get_page_by_title($a['title'], OBJECT, 'post')) continue;
    
    $post_id = wp_insert_post([
        'post_title'   => $a['title'],
        'post_content' => $a['content'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
    ]);

    if ($post_id) {
        vh_attach_asset_image($post_id, $a['img']);
        echo "✅ Created Article: <b>{$a['title']}</b><br>";
    }
}

echo "<h2>🎉 Setup Completed Successfully!</h2>";
echo "<p>You can now visit:</p>";
echo "<ul>
    <li><a href='/auth'>Login / Register Page</a></li>
    <li><a href='/marketplace-produk'>Marketplace Produk</a></li>
    <li><a href='/pusat-tender'>Pusat Tender</a></li>
    <li><a href='/wp-admin'>Wordpress Admin</a></li>
</ul>";
echo "<p style='color:red;'><b>IMPORTANT: Please delete this file (vh-setup.php) for security!</b></p>";
