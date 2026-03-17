<?php
/**
 * Demo Content Importer for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Importer {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_import_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'handle_import_request' ) );
        add_action( 'admin_notices', array( __CLASS__, 'add_setup_notice' ) );
    }

    public static function add_import_menu() {
        add_submenu_page(
            'edit.php?post_type=vh_product',
            __( 'Import Demo', 'vendorhub' ),
            __( 'Import Demo', 'vendorhub' ),
            'manage_options',
            'vh-import-demo',
            array( __CLASS__, 'render_import_page' )
        );
    }

    public static function render_import_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'NiagaHUB Demo Content Importer', 'vendorhub' ); ?></h1>
            <p><?php _e( 'Click the button below to populate your marketplace with premium sample vendors, products, and tenders.', 'vendorhub' ); ?></p>
            
            <div class="notice notice-warning inline">
                <p><?php _e( 'WARNING: This will create new users and posts. Use only on a fresh installation.', 'vendorhub' ); ?></p>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field( 'vh_import_demo_action', 'vh_import_nonce' ); ?>
                <input type="hidden" name="vh_import_demo" value="1" />
                <p class="submit">
                    <button type="submit" class="button button-primary button-large"><?php _e( 'Import Flagship Demo Data', 'vendorhub' ); ?></button>
                </p>
            </form>
        </div>
        <?php
    }

    public static function handle_import_request() {
        if ( ! isset( $_POST['vh_import_demo'] ) || ! check_admin_referer( 'vh_import_demo_action', 'vh_import_nonce' ) ) {
            return;
        }

        self::run_import();

        // Redirect to same page with success flag
        wp_safe_redirect( add_query_arg( 'import_success', '1', menu_page_url( 'vh-import-demo', false ) ) );
        exit;
    }

    public static function add_setup_notice() {
        // Show only if no industries exist and user can manage options
        if ( ! current_user_can( 'manage_options' ) ) return;

        $industries = get_terms( array( 'taxonomy' => 'vh_industry', 'hide_empty' => false ) );
        $is_success = isset( $_GET['import_success'] );

        if ( $is_success ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php _e( 'NiagaHUB Success!', 'vendorhub' ); ?></strong> <?php _e( 'Konten demo berhasil diinstal. Marketplace Anda kini siap digunakan!', 'vendorhub' ); ?> <a href="<?php echo site_url(); ?>"><?php _e( 'Lihat Website →', 'vendorhub' ); ?></a></p>
            </div>
            <?php
            return;
        }

        if ( empty( $industries ) || is_wp_error( $industries ) ) {
            $current_screen = get_current_screen();
            if ( $current_screen && $current_screen->id === 'vh_product_page_vh-import-demo' ) return;
            ?>
            <div class="notice notice-info">
                <p><strong><?php _e( 'Setup NiagaHUB:', 'vendorhub' ); ?></strong> <?php _e( 'Website Anda masih kosong. Ingin menginstal konten demo (produk, vendor, tender) agar terlihat seperti demo premium?', 'vendorhub' ); ?> 
                <a href="<?php echo admin_url('edit.php?post_type=vh_product&page=vh-import-demo'); ?>" class="button button-primary" style="margin-left: 10px;"><?php _e( 'Instal Konten Demo Sekarang', 'vendorhub' ); ?></a></p>
            </div>
            <?php
        }
    }

    private static function run_import() {
        // 1. Create Industry Terms
        $industries = array(
            'Konstruksi & Perkakas',
            'Komputer & Teknologi',
            'Mesin Industri',
            'Otomotif & Transportasi',
            'Kebutuhan Kantor',
            'Energi & Listrik'
        );
        $term_ids = array();
        foreach ( $industries as $ind ) {
            $term = wp_insert_term( $ind, 'vh_industry' );
            if ( ! is_wp_error( $term ) ) {
                $term_ids[$ind] = $term['term_id'];
            } else {
                $existing = get_term_by( 'name', $ind, 'vh_industry' );
                $term_ids[$ind] = $existing ? $existing->term_id : 0;
            }
        }

        // 2. Side-load common images for reuse
        $logo_id = self::sideload_image_from_assets( 'prod3.png', 'Sample Logo' );
        $banner_id = self::sideload_image_from_assets( 'banner1.png', 'Sample Banner' );

        // 3. Create Sample Vendors (Users)
        $vendors = array(
            array( 'user' => 'vendor_global', 'pass' => 'vendor123', 'email' => 'global@example.com', 'name' => 'Global Logistics Jaya', 'loc' => 'Jakarta' ),
            array( 'user' => 'vendor_tech', 'pass' => 'vendor123', 'email' => 'tech@example.com', 'name' => 'Inovasi Tech Indo', 'loc' => 'Bandung' ),
            array( 'user' => 'vendor_med', 'pass' => 'vendor123', 'email' => 'med@example.com', 'name' => 'Medika Utama Mandiri', 'loc' => 'Surabaya' ),
        );

        foreach ( $vendors as $v ) {
            if ( username_exists( $v['user'] ) ) continue;

            $user_id = wp_create_user( $v['user'], $v['pass'], $v['email'] );
            if ( ! is_wp_error( $user_id ) ) {
                $user = new WP_User( $user_id );
                $user->set_role( 'vendor' );
                update_user_meta( $user_id, 'vh_company_name', $v['name'] );
                update_user_meta( $user_id, 'vh_location', $v['loc'] );
                update_user_meta( $user_id, 'vh_verified', '1' );
                update_user_meta( $user_id, 'vh_vendor_logo', $logo_id );

                // 4. Create Products for each Vendor
                self::create_sample_products( $user_id, $v['name'], $term_ids );
            }
        }

        // 5. Create Sample Tenders
        self::create_sample_tenders( $term_ids );

        // 6. Setup Required Pages
        self::setup_required_pages();
    }

    private static function setup_required_pages() {
        $pages = array(
            array( 'title' => 'Auth', 'slug' => 'auth', 'template' => 'page-auth.php' ),
            array( 'title' => 'Dashboard', 'slug' => 'dashboard', 'template' => 'page-dashboard.php' ),
            array( 'title' => 'Direktori Vendor', 'slug' => 'marketplace-vendor', 'content' => '[vh_marketplace_vendor]' ),
            array( 'title' => 'Marketplace Produk', 'slug' => 'marketplace-produk', 'content' => '[vh_marketplace_products]' ),
            array( 'title' => 'Pusat Tender', 'slug' => 'pusat-tender', 'content' => '[vh_tender_listing]' ),
        );

        foreach ( $pages as $p ) {
            $existing = get_page_by_path( $p['slug'] );
            if ( ! $existing ) {
                $post_id = wp_insert_post( array(
                    'post_type'    => 'page',
                    'post_title'   => $p['title'],
                    'post_name'    => $p['slug'],
                    'post_content' => isset($p['content']) ? $p['content'] : '',
                    'post_status'  => 'publish',
                ) );
                if ( $post_id && isset($p['template']) ) {
                    update_post_meta( $post_id, '_wp_page_template', $p['template'] );
                }
            }
        }
        flush_rewrite_rules();
    }

    private static function create_sample_products( $vendor_id, $company_name, $term_ids ) {
        $products = array(
            array( 'title' => 'Industrial Generator 500kVA', 'content' => 'High performance generator for heavy manufacturing.', 'img' => 'prod1.png', 'moq' => 1, 'unit' => 'Set', 'ind' => 'Mesin Industri' ),
            array( 'title' => 'Premium Pallet Rack System', 'content' => 'Heavy duty storage solution for high-capacity warehouses.', 'img' => 'prod2.png', 'moq' => 10, 'unit' => 'Unit', 'ind' => 'Konstruksi & Perkakas' ),
        );

        foreach ( $products as $p ) {
            $post_id = wp_insert_post( array(
                'post_title'   => $p['title'] . ' - ' . $company_name,
                'post_content' => $p['content'],
                'post_status'  => 'publish',
                'post_type'    => 'vh_product',
                'post_author'  => $vendor_id,
            ) );

            if ( $post_id ) {
                update_post_meta( $post_id, '_vh_moq', $p['moq'] );
                update_post_meta( $post_id, '_vh_unit', $p['unit'] );
                
                $img_id = self::sideload_image_from_assets( $p['img'], $p['title'] );
                if ( $img_id ) set_post_thumbnail( $post_id, $img_id );

                $industry = isset($term_ids[$p['ind']]) ? $term_ids[$p['ind']] : 0;
                if (!$industry) {
                    $first_term = reset($term_ids);
                    $industry = $first_term;
                }
                wp_set_object_terms( $post_id, $industry, 'vh_industry' );
            }
        }
    }

    private static function create_sample_tenders( $term_ids ) {
        $tenders = array(
            array( 'title' => 'Pengadaan Armada Truk Logistik 2026', 'budget' => 5000000000, 'deadline' => '2026-12-31', 'ind' => 'Otomotif & Transportasi' ),
            array( 'title' => 'Sistem ERP Multi-Factory Implementation', 'budget' => 2500000000, 'deadline' => '2026-06-30', 'ind' => 'Komputer & Teknologi' ),
        );

        foreach ( $tenders as $t ) {
            $post_id = wp_insert_post( array(
                'post_title'   => $t['title'],
                'post_content' => 'Dibutuhkan mitra strategis untuk proyek skala nasional yang mengutamakan kualitas dan efisiensi waktu.',
                'post_status'  => 'publish',
                'post_type'    => 'vh_tender',
            ) );

            if ( $post_id ) {
                update_post_meta( $post_id, '_vh_tender_budget', $t['budget'] );
                update_post_meta( $post_id, '_vh_tender_deadline', $t['deadline'] );
                
                $industry = isset($term_ids[$t['ind']]) ? $term_ids[$t['ind']] : 0;
                if (!$industry) {
                    $first_term = reset($term_ids);
                    $industry = $first_term;
                }
                wp_set_object_terms( $post_id, $industry, 'vh_industry' );
            }
        }
    }

    /**
     * Helper to side-load image from Theme assets into Media Library
     */
    private static function sideload_image_from_assets( $filename, $title ) {
        $asset_path = get_template_directory() . '/assets/images/' . $filename;
        if ( ! file_exists( $asset_path ) ) return 0;

        // Check if already uploaded
        $existing = get_posts( array( 'post_type' => 'attachment', 'name' => sanitize_title( $title ), 'posts_per_page' => 1 ) );
        if ( ! empty( $existing ) ) return $existing[0]->ID;

        // Copy file to uploads
        $upload_dir = wp_upload_dir();
        $new_file = $upload_dir['path'] . '/' . $filename;
        copy( $asset_path, $new_file );

        // Insert into Media Library
        $wp_filetype = wp_check_filetype( $new_file, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $title,
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $new_file );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $new_file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}

VH_Importer::init();
