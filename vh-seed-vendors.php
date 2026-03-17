<?php
/**
 * Vendor Seeder - NiagaHUB
 */
define('WP_USE_THEMES', false);
require_once('wp-load.php');

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Unauthorized access.');
}

echo "Seeding Vendors...<br>";

$sample_vendors = [
    [
        'username' => 'vendor_tekno',
        'email' => 'contact@teknonusa.id',
        'company' => 'PT Tekno Nusa Tama',
        'location' => 'Jakarta Selatan',
        'desc' => 'Penyedia infrastruktur IT, server enterprise, dan solusi jaringan untuk korporasi skala besar.'
    ],
    [
        'username' => 'vendor_konstruksi',
        'email' => 'sales@bangunbeton.com',
        'company' => 'CV Bangun Beton Jaya',
        'location' => 'Surabaya',
        'desc' => 'Produksi beton readymix dan supplier semen berkualitas tinggi untuk proyek infrastruktur nasional.'
    ],
    [
        'username' => 'vendor_kantor',
        'email' => 'info@officemax.co.id',
        'company' => 'OfficeMax Indonesia',
        'location' => 'Bandung',
        'desc' => 'Pusat pengadaan alat tulis kantor, furniture, dan kebutuhan operasional perusahaan terlengkap.'
    ]
];

foreach ($sample_vendors as $v) {
    if (!username_exists($v['username'])) {
        $user_id = wp_create_user($v['username'], 'password123', $v['email']);
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('vendor');
            
            update_user_meta($user_id, 'vh_company_name', $v['company']);
            update_user_meta($user_id, 'vh_location', $v['location']);
            update_user_meta($user_id, 'vh_verified', '1');
            update_user_meta($user_id, 'vh_role', 'vendor'); // Ensure role meta is also set
            update_user_meta($user_id, 'description', $v['desc']);
            
            echo "Created Vendor: {$v['company']}<br>";
        }
    } else {
        echo "Vendor {$v['username']} already exists.<br>";
    }
}

echo "Updating existing products to have valid authors...<br>";
$vendor_users = get_users(['role' => 'vendor']);
if (!empty($vendor_users)) {
    $products = get_posts(['post_type' => 'vh_product', 'posts_per_page' => -1]);
    foreach ($products as $p) {
        $random_vendor = $vendor_users[array_rand($vendor_users)];
        wp_update_post([
            'ID' => $p->ID,
            'post_author' => $random_vendor->ID
        ]);
    }
    echo "Linked products to vendors.<br>";
}

echo "<br><b>Vendor Seeding Completed!</b>";
