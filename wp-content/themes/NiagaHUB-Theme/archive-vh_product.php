<?php
/**
 * Product Marketplace Archive Template
 */

get_header(); ?>

<div class="nh-marketplace-header" style="background: white; border-bottom: 1px solid var(--border-color); padding: 4rem 0;">
    <div class="nh-container" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php _e('Marketplace Produk', 'niagahub-theme'); ?></h1>
            <p class="text-muted" style="font-size: 1.1rem;"><?php _e('Temukan produk berkualitas dari vendor manufaktur & distributor terpercaya.', 'niagahub-theme'); ?></p>
        </div>
        <div class="header-search-alt" style="width: 400px;">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="vh-search-form">
                <input type="hidden" name="post_type" value="vh_product">
                <input type="search" placeholder="<?php _e('Cari di marketplace...', 'niagahub-theme'); ?>" name="s" value="<?php echo get_search_query(); ?>">
                <button type="submit" class="vh-btn vh-btn-primary"><span class="dashicons dashicons-search"></span></button>
            </form>
        </div>
    </div>
</div>

<div class="nh-container" style="padding: 3rem 1.5rem;">
    <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
    <div class="vh-marketplace-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 3rem;">
        
        <!-- Sidebar Filters -->
        <aside class="vh-sidebar">
            <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px;"><?php _e('Industri', 'niagahub-theme'); ?></h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php
                    $industries = get_terms( array(
                        'taxonomy' => 'vh_industry',
                        'hide_empty' => false,
                    ) );
                    
                    if ( ! empty( $industries ) && ! is_wp_error( $industries ) ) :
                        foreach ( $industries as $industry ) : ?>
                            <li style="margin-bottom: 0.8rem;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 15px;">
                                    <input type="checkbox" style="width: 18px; height: 18px; accent-color: var(--primary-color);">
                                    <?php echo esc_html( $industry->name ); ?>
                                    <span class="text-muted" style="font-size: 12px; margin-left: auto;">(<?php echo $industry->count; ?>)</span>
                                </label>
                            </li>
                        <?php endforeach;
                    else : ?>
                        <li class="text-muted"><?php _e('Belum ada kategori.', 'niagahub-theme'); ?></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px;"><?php _e('Lokasi', 'niagahub-theme'); ?></h3>
                <select style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <option value=""><?php _e('Semua Wilayah', 'niagahub-theme'); ?></option>
                    <option>Jakarta</option>
                    <option>Surabaya</option>
                    <option>Bandung</option>
                    <option>Medan</option>
                </select>
            </div>

            <div class="vh-promo-box nh-industry-card" style="background: var(--secondary-color); color: white; padding: 1.5rem; border-radius: 12px; margin-top: 2rem;">
                <h4 style="color: var(--primary-color); margin-top: 0;"><?php _e('Punya Proyek Besar?', 'niagahub-theme'); ?></h4>
                <p style="font-size: 13px; opacity: 0.8;"><?php _e('Biarkan vendor kami yang mencari Anda. Buat Tender sekarang.', 'niagahub-theme'); ?></p>
                <a href="<?php echo site_url('/tender'); ?>" class="btn-rfq" style="display: block; text-align: center; margin-top: 1rem;"><?php _e('Buka Tender', 'niagahub-theme'); ?></a>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="vh-main-marketplace">
            <div class="vh-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <div class="vh-result-count">
                    <?php
                    global $wp_query;
                    printf( _n( 'Menampilkan <strong>%d</strong> produk', 'Menampilkan <strong>%d</strong> produk', $wp_query->found_posts, 'niagahub-theme' ), $wp_query->found_posts );
                    ?>
                </div>
                <div class="vh-sort">
                    <select style="padding: 8px; border-radius: 6px; border: 1px solid var(--border-color);">
                        <option><?php _e('Terbaru', 'niagahub-theme'); ?></option>
                        <option><?php _e('Harga Terendah', 'niagahub-theme'); ?></option>
                        <option><?php _e('Rating Tertinggi', 'niagahub-theme'); ?></option>
                    </select>
                </div>
            </div>

            <?php if ( have_posts() ) : ?>
                <div class="vh-product-grid">
                    <?php while ( have_posts() ) : the_post(); 
                        $price        = get_post_meta( get_the_ID(), '_vh_price', true );
                        $moq          = get_post_meta( get_the_ID(), '_vh_moq',   true );
                        $unit         = get_post_meta( get_the_ID(), '_vh_unit',  true );
                        
                        $vendor_id    = get_post_field( 'post_author', get_the_ID() );
                        $location     = get_user_meta($vendor_id, 'vh_location', true);
                        
                        // Product Category
                        $terms        = get_the_terms( get_the_ID(), 'vh_industry' );
                        $cat_name     = ! empty($terms) && ! is_wp_error($terms) ? $terms[0]->name : 'Umum';
                    ?>
                        <div class="vh-product-card">
                            <!-- Product Image -->
                            <a href="<?php the_permalink(); ?>" class="vh-product-img-wrap">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <div class="vh-product-no-img">
                                        <span class="dashicons dashicons-format-image"></span>
                                    </div>
                                <?php endif; ?>
                                <span class="vh-product-cat-badge"><?php echo esc_html($cat_name); ?></span>
                            </a>

                            <div class="vh-product-content">
                                <!-- Title & Price -->
                                <h3 class="vh-product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="vh-product-price">
                                    <?php echo $price ? 'Rp ' . number_format($price, 0, ',', '.') : __('Hubungi Vendor', 'vendorhub'); ?>
                                    <span class="vh-product-unit">/ <?php echo esc_html( $unit ?: 'Unit' ); ?></span>
                                </div>

                                <!-- Specs -->
                                <div class="vh-product-specs">
                                    <div class="vh-spec-item">
                                        <span class="dashicons dashicons-cart"></span>
                                        Min. Order: <strong><?php echo esc_html( $moq ?: '1' ); ?> <?php echo esc_html( $unit ?: 'Unit' ); ?></strong>
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="vh-product-actions">
                                    <a href="<?php the_permalink(); ?>" class="vh-btn vh-btn-primary" style="width: 100%;">
                                        <?php _e('Detail Lengkap', 'vendorhub'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="vh-pagination" style="margin-top: 4rem; text-align: center;">
                    <?php the_posts_pagination(); ?>
                </div>

            <?php else : ?>
                <div class="vh-card" style="text-align: center; padding: 5rem;">
                    <span class="dashicons dashicons-search" style="font-size: 3rem; width: 3rem; height: 3rem; color: #cbd5e1; margin-bottom: 15px;"></span>
                    <p><?php _e('Produk tidak ditemukan.', 'niagahub-theme'); ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php get_footer(); ?>
