<?php
/**
 * Seeder class for NiagaHUB
 * Handles automatic creation of pages, industries, and sample content.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Seeder {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'auto_seed' ) );
    }

    public static function auto_seed() {
        if ( get_option( 'vh_seeding_version' ) === VENDORHUB_VERSION ) {
            return;
        }

        self::seed_pages();
        self::seed_industries();
        self::seed_users();
        self::seed_content();

        update_option( 'vh_seeding_version', VENDORHUB_VERSION );
    }

    public static function seed_pages() {
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
                if ($post_id && $p['template']) {
                    update_post_meta($post_id, '_wp_page_template', $p['template']);
                }
            }
        }
    }

    public static function seed_industries() {
        $industries = [
            'Konstruksi & Perkakas'  => 'industry_construction.png',
            'Komputer & Teknologi'   => 'industry_tech.png',
            'Mesin Industri'         => 'industry_industrial.png',
            'Otomotif & Transportasi' => 'industry_automotive.png',
            'Kebutuhan Kantor'       => 'industry_office.png',
            'Energi & Listrik'       => 'industry_energy.png'
        ];

        foreach ($industries as $name => $img) {
            $term = term_exists($name, 'vh_industry');
            if (!$term) {
                $term = wp_insert_term($name, 'vh_industry');
            }
            
            $term_id = is_array($term) ? $term['term_id'] : $term;
            if ($term_id && !get_term_meta($term_id, 'vh_industry_image', true)) {
                $attach_id = self::attach_asset_image(0, $img);
                if ($attach_id) {
                    update_term_meta($term_id, 'vh_industry_image', $attach_id);
                }
            }
        }
    }

    public static function seed_users() {
        self::create_vh_user('vendor_surya', 'PT Surya Baja Mandiri', 'vendor');
        self::create_vh_user('vendor_krakatau', 'PT Krakatau Konstruksi', 'vendor');
        self::create_vh_user('buyer_adhi', 'PT Adhi Karya Logistik', 'buyer');
    }

    private static function create_vh_user($username, $company, $role) {
        $uid = username_exists($username);
        if (!$uid) {
            $uid = wp_create_user($username, 'password', $username.'@niagahub.test');
            $user = new WP_User($uid);
            $user->set_role('subscriber');
            update_user_meta($uid, 'vh_role', $role);
            update_user_meta($uid, 'vh_company_name', $company);
            update_user_meta($uid, 'vh_verified', '1');
            update_user_meta($uid, 'vh_location', 'Jakarta');
        }
        return $uid;
    }

    public static function seed_content() {
        $v1 = username_exists('vendor_surya');
        $v2 = username_exists('vendor_krakatau');
        $b1 = username_exists('buyer_adhi');

        // Sample Products
        $products = [
            [
                'title'   => 'Baja Ringan Galvalum C75',
                'content' => 'Baja ringan standar SNI untuk rangka atap berkualitas tinggi.',
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
                self::attach_asset_image($post_id, $p['img']);
                $term = get_term_by('name', $p['term'], 'vh_industry');
                if ($term) wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
            }
        }

        // Sample Tenders
        $tenders = [
            [
                'title'    => 'Proyek Konstruksi Gudang Logistik',
                'content'  => 'Dibutuhkan kontraktor untuk renovasi atap dan lantai gudang seluas 2000m2.',
                'budget'   => 1200000000,
                'deadline' => '2026-06-15',
                'term'     => 'Konstruksi & Perkakas',
                'img'      => 'industry_construction.png',
            ],
            [
                'title'    => 'Pengadaan IT Hardware Karyawan',
                'content'  => 'Spesifikasi minimal Core i5, RAM 16GB untuk kebutuhan operasional perusahaan.',
                'budget'   => 5000000000,
                'deadline' => '2026-04-30',
                'term'     => 'Komputer & Teknologi',
                'img'      => 'industry_tech.png',
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
                self::attach_asset_image($post_id, $t['img']);
                $term = get_term_by('name', $t['term'], 'vh_industry');
                if ($term) wp_set_post_terms($post_id, [$term->term_id], 'vh_industry');
            }
        }

        // Sample Articles
        $articles = [
            [
                'title'   => 'Tips Memilih Vendor Konstruksi Terpercaya',
                'content' => 'Dalam proyek besar, memilih sub-kontraktor atau vendor material adalah kunci keberhasilan. Pastikan mereka memiliki NIB dan portofolio yang jelas.',
                'img'     => 'industry_construction.png',
            ],
            [
                'title'   => 'Tren Digitalisasi Procurement di Tahun 2026',
                'content' => 'Dunia e-procurement berkembang pesat. Perusahaan yang beralih ke platform digital seperti NiagaHUB terbukti menghemat biaya hingga 20%.',
                'img'     => 'industry_tech.png',
            ],
            [
                'title'   => 'Pentingnya Maintenance Mesin Industri Secara Berkala',
                'content' => 'Jangan tunggu rusak. Melakukan maintenance rutin pada mesin industri dapat memperpanjang usia pakai aset perusahaan Anda secara signifikan.',
                'img'     => 'industry_industrial.png',
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
                self::attach_asset_image($post_id, $a['img']);
            }
        }
    }

    private static function attach_asset_image($post_id, $filename) {
        $file_path = VENDORHUB_PATH . 'assets/images/' . $filename;
        if (!file_exists($file_path)) return false;

        $upload_dir = wp_upload_dir();
        $dest = $upload_dir['path'] . '/' . time() . '_' . basename($file_path);
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
        if ($post_id > 0) {
            set_post_thumbnail($post_id, $attach_id);
        }
        return $attach_id;
    }
}

VH_Seeder::init();
