<?php
/**
 * Industry Taxonomy Archive Template
 */

get_header(); 

$term = get_queried_object();
$bg_url = vh_get_industry_bg( $term->slug );

// Filtering params
$search_keyword  = isset($_GET['s'])   ? sanitize_text_field($_GET['s'])   : '';
$search_location = isset($_GET['loc']) ? sanitize_text_field($_GET['loc']) : '';
$current_cat     = $term->slug;
?>

<div class="nh-marketplace-header" style="background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.85)), url('<?php echo esc_url($bg_url); ?>') no-repeat center center; background-size: cover; padding: 4rem 0; color: white;">
    <div class="nh-container">
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs('light'); ?>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 3rem;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: white; line-height: 1.2; text-transform: capitalize;"><?php single_term_title(); ?></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; margin: 0; line-height: 1.6;"><?php echo term_description() ?: sprintf(__('Eksplorasi produk terbaik di kategori %s dari vendor terverifikasi NiagaHUB.', 'niagahub-theme'), single_term_title('', false)); ?></p>
            </div>
            <div class="header-search-alt" style="flex: 1; min-width: 0;">
                <form method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="vh-search-form" style="display: flex; width: 100%; background: white; border: 2px solid white; border-radius: 50px; overflow: hidden; margin: 0; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                    <input type="hidden" name="post_type" value="vh_product">
                    <input type="text" name="s" value="<?php echo esc_attr($search_keyword); ?>" placeholder="Cari di kategori ini..." style="flex: 1; min-width: 0; border: none; padding: 14px 20px; outline: none; background: transparent; font-size: 14px;">
                    
                    <button type="submit" class="vh-btn vh-btn-primary" style="flex-shrink: 0; border-radius: 0; padding: 0 32px; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-search" style="font-size: 18px; width: 18px; height: 18px;"></span>
                        <?php _e('Cari', 'niagahub-theme'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
            </style>
            
            <form method="GET" action="<?php echo esc_url(get_term_link($term)); ?>">
                <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                    <h3><?php _e('Bidang / Kategori', 'niagahub-theme'); ?></h3>
                    <ul class="vh-filter-list">
                        <?php
                        $industries = get_terms(array('taxonomy' => 'vh_industry', 'hide_empty' => false));
                        foreach ($industries as $industry) : 
                            $checked = ($term->slug == $industry->slug) ? 'checked' : '';
                        ?>
                            <li class="vh-filter-item">
                                <a href="<?php echo esc_url(get_term_link($industry)); ?>" style="text-decoration: none;">
                                    <label class="vh-filter-label">
                                        <input type="radio" name="cat_mock" value="<?php echo esc_attr($industry->slug); ?>" <?php echo $checked; ?> disabled>
                                        <div class="vh-custom-radio"></div>
                                        <span><?php echo esc_html($industry->name); ?></span>
                                        <span class="vh-filter-count"><?php echo $industry->count; ?></span>
                                    </label>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                    <h3><?php _e('Lokasi', 'niagahub-theme'); ?></h3>
                    <select name="loc" class="vh-filter-select" onchange="this.form.submit()">
                        <option value=""><?php _e('Semua Lokasi', 'niagahub-theme'); ?></option>
                        <option value="Jakarta" <?php selected($search_location, 'Jakarta'); ?>>Jakarta</option>
                        <option value="Surabaya" <?php selected($search_location, 'Surabaya'); ?>>Surabaya</option>
                        <option value="Bandung" <?php selected($search_location, 'Bandung'); ?>>Bandung</option>
                    </select>
                </div>
            </form>
        </aside>

        <!-- Product Grid Column -->
        <main class="vh-main-marketplace">
            <style>
                .vh-product-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                    gap: 1.5rem;
                }
                @media (max-width: 768px) {
                    .nh-marketplace-header { padding: 2.5rem 0 !important; }
                    .nh-marketplace-header h1 { font-size: 1.75rem !important; }
                    .nh-marketplace-header p { font-size: 0.95rem !important; }
                    .vh-product-grid {
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
            
            <div class="vh-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                <div class="vh-result-count" style="font-size: 14px; color: #64748b;">
                    <?php
                    global $wp_query;
                    printf( 'Menampilkan <strong>%d</strong> produk di %s', $wp_query->found_posts, single_term_title('', false) );
                    ?>
                </div>
            </div>

            <div class="vh-product-grid">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                    if ( defined('VENDORHUB_PATH') ) {
                        include VENDORHUB_PATH . 'templates/marketplace/product-card.php';
                    }
                endwhile; else : ?>
                    <div class="vh-card" style="grid-column: 1 / -1; text-align: center; padding: 5rem;">
                        <span class="dashicons dashicons-search" style="font-size: 3rem; width: 3rem; height: 3rem; color: #cbd5e1; margin-bottom: 15px;"></span>
                        <p><?php _e( 'Produk tidak ditemukan.', 'niagahub-theme' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="vh-pagination" style="margin-top: 4rem;">
                <?php the_posts_pagination( array(
                    'prev_text' => __('&laquo; Prev', 'niagahub-theme'),
                    'next_text' => __('Next &raquo;', 'niagahub-theme'),
                    'type'      => 'plain',
                ) ); ?>
            </div>
        </main>
    </div>
</div>

<?php get_footer(); ?>
