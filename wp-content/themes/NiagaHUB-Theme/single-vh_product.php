<?php
/**
 * Single Product Template for NiagaHUB
 */

get_header(); ?>

<div class="nh-container" style="padding: 2rem 1.5rem;">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        $product_id = get_the_ID();
        $moq = get_post_meta( $product_id, '_vh_moq', true );
        $unit = get_post_meta( $product_id, '_vh_unit', true );
        $vendor_id = get_post_field( 'post_author', $product_id );
        $vendor_name = get_the_author_meta( 'display_name', $vendor_id );
        $location = get_user_meta( $vendor_id, 'vh_location', true );
        $is_verified = get_user_meta( $vendor_id, 'vh_verified', true );
    ?>
        
        <!-- Breadcrumbs -->
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>

        <div class="vh-grid" style="grid-template-columns: 1fr 1.2fr; gap: 3rem; align-items: start;">
            <!-- Product Gallery -->
            <div class="vh-product-gallery">
                <div class="vh-main-image vh-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
                    <?php else : ?>
                        <div style="aspect-ratio: 1/1; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #cbd5e1;">
                            <span class="dashicons dashicons-format-image"></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="vh-product-details">
                <div class="vh-vendor-badge" style="margin-bottom: 1rem;">
                    <?php if ( $is_verified === '1' ) : ?>
                        <span class="vh-badge vh-badge-verified" style="font-size: 14px; padding: 6px 12px;"><?php _e('Vendor Terverifikasi', 'niagahub-theme'); ?></span>
                    <?php endif; ?>
                </div>

                <h1 style="font-size: 2.5rem; margin-top: 0; line-height: 1.2;"><?php the_title(); ?></h1>
                
                <div class="vh-meta-row" style="display: flex; gap: 2rem; margin: 1.5rem 0; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <div class="vh-meta-item">
                        <span class="text-muted" style="display: block; font-size: 13px;"><?php _e('VENDOR', 'niagahub-theme'); ?></span>
                        <strong style="font-size: 1.1rem; color: var(--secondary-color);"><?php echo esc_html( $vendor_name ); ?></strong>
                    </div>
                    <div class="vh-meta-item">
                        <span class="text-muted" style="display: block; font-size: 13px;"><?php _e('LOKASI', 'niagahub-theme'); ?></span>
                        <strong style="font-size: 1.1rem;"><?php echo esc_html( $location ?: '-' ); ?></strong>
                    </div>
                </div>

                <div class="vh-b2b-specs vh-card" style="background: #f8fafc; border: none; margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <span class="text-muted"><?php _e('Min. Order (MOQ)', 'niagahub-theme'); ?></span>
                            <p style="font-size: 1.2rem; font-weight: 700; margin: 0.2rem 0;"><?php echo $moq ?: '1'; ?> <?php echo $unit ?: 'Unit'; ?></p>
                        </div>
                        <div>
                            <span class="text-muted"><?php _e('Harga Satuan', 'niagahub-theme'); ?></span>
                            <?php $price = get_post_meta($product_id, '_vh_price', true); ?>
                            <p style="font-size: 1.2rem; font-weight: 700; margin: 0.2rem 0; color: var(--primary-color);">
                                <?php echo $price ? 'Rp ' . number_format($price, 0, ',', '.') : __('Hubungi Vendor', 'niagahub-theme'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="vh-actions" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <a href="#rfq-section" class="vh-btn vh-btn-action" style="flex: 1.5; padding: 1.2rem; font-size: 1.1rem; text-align: center;">
                        <span class="dashicons dashicons-email-alt" style="margin-right: 8px;"></span>
                        <?php _e('Minta Penawaran Harga', 'niagahub-theme'); ?>
                    </a>
                    <button class="vh-btn vh-btn-primary vh-enquiry-btn" data-vendor="<?php echo $vendor_id; ?>" data-product="<?php the_title(); ?>" style="flex: 1; padding: 1.2rem; font-size: 1.1rem;">
                        <span class="dashicons dashicons-format-chat" style="margin-right: 8px;"></span>
                        <?php _e('Chat Vendor', 'niagahub-theme'); ?>
                    </button>
                    <a href="#" class="vh-btn" style="border: 2px solid var(--border-color); padding: 1.2rem; display: flex; align-items: center; justify-content: center;">
                        <span class="dashicons dashicons-share"></span>
                    </a>
                </div>

                <div class="vh-description">
                    <h3><?php _e('Deskripsi Produk', 'niagahub-theme'); ?></h3>
                    <div class="vh-content" style="line-height: 1.8; color: #475569;">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- RFQ Section for this product -->
        <div id="rfq-section" style="margin-top: 5rem; max-width: 800px; margin-left: auto; margin-right: auto;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2><?php _e('Tertarik dengan produk ini?', 'niagahub-theme'); ?></h2>
                <p class="text-muted"><?php _e('Kirimkan detail kebutuhan Anda kepada vendor dan dapatkan kutipan harga terbaik.', 'niagahub-theme'); ?></p>
            </div>
            
            <?php echo do_shortcode('[vh_rfq_form]'); ?>
        </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
