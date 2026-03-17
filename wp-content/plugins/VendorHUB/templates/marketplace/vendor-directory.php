<?php
/**
 * Vendor Directory Template
 */

$search_keyword  = isset($_GET['s'])   ? sanitize_text_field($_GET['s'])   : '';
$search_location = isset($_GET['loc']) ? sanitize_text_field($_GET['loc']) : '';
$search_industry = isset($_GET['industry']) ? sanitize_text_field($_GET['industry']) : '';

$current_page = max(1, get_query_var('paged'));
$per_page     = 12;
$offset       = ($current_page - 1) * $per_page;

$args = array(
    'search'         => $search_keyword ? "*{$search_keyword}*" : '',
    'search_columns' => array('display_name', 'user_email'),
    'number'         => $per_page,
    'offset'         => $offset,
    'count_total'    => true,
);

$args['meta_query'] = array(
    'relation' => 'AND',
    array( 'key' => 'vh_role', 'value' => 'vendor' ),
    array( 'key' => 'vh_verified', 'value' => '1' ),
    array( 'key' => 'vh_company_name', 'value' => '', 'compare' => '!=' ),
    array( 'key' => 'vh_nib', 'value' => '', 'compare' => '!=' ),
    array( 'key' => 'vh_nib_file', 'value' => '', 'compare' => '!=' ),
);

if ( $search_location ) {
    $args['meta_query'][] = array( 'key' => 'vh_location', 'value' => $search_location, 'compare' => 'LIKE' );
}

if ( $search_industry ) {
    $args['meta_query'][] = array( 'key' => 'vh_industry', 'value' => $search_industry, 'compare' => 'LIKE' );
}

$user_query = new WP_User_Query( $args );
$vendors    = $user_query->get_results();
$total_users = $user_query->get_total();
$total_pages = ceil($total_users / $per_page);
?>

<?php if ( ! is_front_page() ) : ?>
<?php $bg_url = function_exists('vh_get_industry_bg') ? vh_get_industry_bg('default') : ''; ?>
<div class="nh-marketplace-header" style="background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.85)), url('<?php echo esc_url($bg_url); ?>') no-repeat center center; background-size: cover; padding: 4rem 0; color: white;">
    <div class="nh-container">
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs('light'); ?>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 3rem;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: white; line-height: 1.2;"><?php _e('Direktori Vendor Terpercaya', 'vendorhub'); ?></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; margin: 0; line-height: 1.6;"><?php _e('Bermitra dengan ribuan perusahaan pemasok dan distributor terverifikasi di seluruh Indonesia.', 'vendorhub'); ?></p>
            </div>
            <div class="header-search-alt" style="flex: 1; min-width: 0;">
                <form method="GET" action="" class="vh-search-form" style="display: flex; width: 100%; background: white; border: 2px solid white; border-radius: 50px; overflow: hidden; margin: 0; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                    <input type="text" name="s" value="<?php echo esc_attr($search_keyword);  ?>" placeholder="<?php _e('Nama Perusahaan...', 'vendorhub'); ?>" style="flex: 1; min-width: 0; border: none; padding: 14px 20px; outline: none; background: transparent; font-size: 15px;">
                    <span class="vh-sf-divider" style="width: 1px; background: #e2e8f0; margin: 10px 0; display: block;"></span>
                    <input type="text" name="loc" value="<?php echo esc_attr($search_location); ?>" placeholder="<?php _e('Lokasi (cth: Jakarta)', 'vendorhub'); ?>" style="width: 180px; min-width: 0; border: none; padding: 14px 20px; outline: none; background: transparent; font-size: 15px;">
                    <button type="submit" class="vh-btn vh-btn-primary" style="flex-shrink: 0; border-radius: 0; padding: 0 32px; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-search" style="font-size: 18px; width: 18px; height: 18px;"></span>
                        <?php _e('Cari', 'vendorhub'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ( is_front_page() ) : ?>
    <div class="nh-container" style="padding: 0 1.5rem 4rem;">
        <div class="vh-vendor-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
            <?php if ( ! empty($vendors) ) : foreach ( $vendors as $vendor ) :
                include VENDORHUB_PATH . 'templates/marketplace/vendor-card.php';
            endforeach; else : ?>
                <p><?php _e( 'Vendor tidak ditemukan.', 'vendorhub' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php else : ?>
<div class="nh-container" style="padding: 4rem 1.5rem;">
    <div class="vh-marketplace-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 3rem;">

        <!-- Sidebar Filters -->
        <aside class="vh-sidebar">
            <style>
                .vh-filter-group h3 {
                    font-size: 0.9rem !important;
                    font-weight: 800 !important;
                    color: #1e293b;
                    margin-bottom: 1.25rem !important;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .vh-filter-group h3::after {
                    content: '';
                    flex: 1;
                    height: 1px;
                    background: #f1f5f9;
                }
                .vh-filter-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                .vh-filter-item {
                    margin-bottom: 0.5rem;
                }
                .vh-filter-label {
                    display: flex;
                    align-items: center;
                    padding: 8px 12px;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    font-size: 14px;
                    color: #475569;
                    position: relative;
                }
                .vh-filter-label:hover {
                    background: #f8fafc;
                    color: var(--vh-primary);
                }
                .vh-filter-label input[type="radio"] {
                    display: none;
                }
                .vh-filter-label .vh-custom-radio {
                    width: 18px;
                    height: 18px;
                    border: 2px solid #cbd5e1;
                    border-radius: 50%;
                    margin-right: 12px;
                    position: relative;
                    transition: all 0.2s ease;
                    background: white;
                }
                .vh-filter-label input[type="radio"]:checked + .vh-custom-radio {
                    border-color: var(--vh-primary);
                    background: var(--vh-primary);
                }
                .vh-filter-label input[type="radio"]:checked + .vh-custom-radio::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 6px;
                    height: 6px;
                    background: white;
                    border-radius: 50%;
                }
                .vh-filter-label input[type="radio"]:checked ~ span {
                    font-weight: 700;
                    color: var(--vh-primary);
                }
                .vh-filter-count {
                    font-size: 11px;
                    font-weight: 600;
                    background: #f1f5f9;
                    color: #94a3b8;
                    padding: 2px 8px;
                    border-radius: 20px;
                    margin-left: auto;
                    transition: all 0.2s ease;
                }
                .vh-filter-label:hover .vh-filter-count {
                    background: rgba(16, 185, 129, 0.1);
                    color: var(--vh-primary);
                }
                .vh-filter-select {
                    width: 100%;
                    padding: 12px 15px;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                    background: #f8fafc;
                    font-size: 14px;
                    color: #475569;
                    cursor: pointer;
                    outline: none;
                    transition: all 0.2s ease;
                    appearance: none;
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right 15px center;
                    background-size: 16px;
                }
                .vh-filter-select:focus {
                    border-color: var(--vh-primary);
                    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                    background-color: white;
                }
                .vh-reset-btn {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    width: 100%;
                    padding: 12px;
                    background: #fff;
                    border: 1px solid #e2e8f0;
                    color: #64748b;
                    border-radius: 12px;
                    font-size: 13px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.2s ease;
                    margin-top: 1rem;
                }
                .vh-reset-btn:hover {
                    background: #f1f5f9;
                    color: #ef4444;
                    border-color: #fca5a5;
                }
            </style>
            
            <form method="GET" action="">
                <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                    <h3><?php _e('Bidang / Industri', 'vendorhub'); ?></h3>
                    <ul class="vh-filter-list">
                        <li class="vh-filter-item">
                            <label class="vh-filter-label">
                                <input type="radio" name="industry" value="" <?php echo empty($search_industry) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <div class="vh-custom-radio"></div>
                                <span><?php _e('Semua Industri', 'vendorhub'); ?></span>
                            </label>
                        </li>
                        <?php
                        // Get vendor counts per industry
                        $vendor_counts = array();
                        $all_vendors_list = get_users(array('meta_key' => 'vh_role', 'meta_value' => 'vendor', 'fields' => array('ID')));
                        foreach ($all_vendors_list as $v_obj) {
                            $v_ind = get_user_meta($v_obj->ID, 'vh_industry', true);
                            if ($v_ind) {
                                $vendor_counts[$v_ind] = (isset($vendor_counts[$v_ind]) ? $vendor_counts[$v_ind] : 0) + 1;
                            }
                        }

                        $terms = get_terms(array('taxonomy' => 'vh_industry', 'hide_empty' => false));
                        foreach ($terms as $term) : 
                            $count = isset($vendor_counts[$term->slug]) ? $vendor_counts[$term->slug] : 0;
                            if ($count === 0 && $search_industry !== $term->slug) continue;
                            
                            $checked = ($search_industry == $term->slug) ? 'checked' : '';
                        ?>
                            <li class="vh-filter-item">
                                <label class="vh-filter-label">
                                    <input type="radio" name="industry" value="<?php echo esc_attr($term->slug); ?>" <?php echo $checked; ?> onchange="this.form.submit()">
                                    <div class="vh-custom-radio"></div>
                                    <span><?php echo esc_html($term->name); ?></span>
                                    <span class="vh-filter-count"><?php echo $count; ?></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                    <h3><?php _e('Lokasi', 'vendorhub'); ?></h3>
                    <select name="loc" class="vh-filter-select" onchange="this.form.submit()">
                        <option value=""><?php _e('Semua Lokasi', 'vendorhub'); ?></option>
                        <option value="Jakarta" <?php selected($search_location, 'Jakarta'); ?>>Jakarta</option>
                        <option value="Surabaya" <?php selected($search_location, 'Surabaya'); ?>>Surabaya</option>
                        <option value="Bandung" <?php selected($search_location, 'Bandung'); ?>>Bandung</option>
                    </select>
                </div>
                
                <input type="hidden" name="s" value="<?php echo esc_attr($search_keyword); ?>">
                
                <a href="<?php echo site_url('/marketplace-vendor'); ?>" class="vh-reset-btn">
                    <span class="dashicons dashicons-image-rotate" style="font-size: 16px; width: 16px; height: 16px;"></span>
                    <?php _e('Reset Filter', 'vendorhub'); ?>
                </a>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="vh-main-marketplace">
            <style>
                .vh-vendor-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                    gap: 2rem;
                }
                @media (max-width: 768px) {
                    .nh-marketplace-header { padding: 2.5rem 0 !important; }
                    .nh-marketplace-header h1 { font-size: 1.75rem !important; }
                    .nh-marketplace-header p { font-size: 0.95rem !important; }
                    .vh-vendor-grid {
                        grid-template-columns: 1fr !important;
                        gap: 1.5rem !important;
                    }
                    .vh-marketplace-layout {
                        grid-template-columns: 1fr !important;
                        gap: 2rem !important;
                    }
                    .vh-sidebar {
                        margin-bottom: 2rem;
                    }
                }
            </style>
            <div class="vh-vendor-grid">
                <?php if ( ! empty($vendors) ) : foreach ( $vendors as $vendor ) :
                    include VENDORHUB_PATH . 'templates/marketplace/vendor-card.php';
                endforeach; else : ?>
                    <div class="vh-card" style="grid-column: 1 / -1; text-align: center; padding: 5rem;">
                        <span class="dashicons dashicons-store" style="font-size: 3rem; width: 3rem; height: 3rem; color: #cbd5e1; margin-bottom: 15px;"></span>
                        <p><?php _e('Vendor tidak ditemukan.', 'vendorhub'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="vh-pagination">
                <?php
                echo paginate_links( array(
                    'total'   => $total_pages,
                    'current' => $current_page,
                    'prev_text' => __('&laquo; Prev', 'vendorhub'),
                    'next_text' => __('Next &raquo;', 'vendorhub'),
                    'type'      => 'plain',
                ) );
                ?>
            </div>
        </main>
    </div>
</div>
<?php endif; ?>
