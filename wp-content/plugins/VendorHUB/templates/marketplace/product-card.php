<?php
/**
 * Product Card Template
 */
$price        = get_post_meta( get_the_ID(), '_vh_price', true );
$moq          = get_post_meta( get_the_ID(), '_vh_moq',   true );
$unit         = get_post_meta( get_the_ID(), '_vh_unit',  true );

$vendor_id    = get_the_author_meta('ID');
$is_verified  = get_user_meta($vendor_id, 'vh_verified', true) === '1';
$location     = get_user_meta($vendor_id, 'vh_location', true);
$company_name = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_the_author_meta('display_name');

$terms     = get_the_terms( get_the_ID(), 'vh_industry' );
$cat_name  = ! empty($terms) && ! is_wp_error($terms) ? $terms[0]->name : __('Umum', 'vendorhub');
?>
<article class="vh-product-card" style="background: white; border-radius: 12px; border: 1px solid #edf2f7; overflow: hidden; height: 100%; transition: all 0.3s ease; display: flex; flex-direction: column; position: relative;">
    <style>
        @media (max-width: 768px) {
            .vh-product-card .vh-product-image { height: 200px !important; }
            .vh-product-card .vh-product-content { padding: 1rem !important; }
            .vh-product-card h3 { font-size: 15px !important; height: auto !important; margin-bottom: 6px !important; -webkit-line-clamp: 2 !important; }
            .vh-product-card .vh-product-price-val { font-size: 16px !important; }
            .vh-product-card .vh-product-footer { margin-top: 12px !important; padding-top: 10px !important; }
            .vh-product-card .vh-btn { padding: 6px 16px !important; font-size: 12px !important; }
        }
        .vh-product-card .vh-btn-outline:hover {
            background: var(--primary) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(255, 128, 0, 0.3) !important;
            transform: translateY(-1px);
        }
    </style>
    <div class="vh-product-image" style="height: 160px; overflow: hidden; position: relative; flex-shrink: 0;">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('medium_large', ['style' => 'width: 100%; height: 100%; object-fit: cover;']); ?>
        <?php else: ?>
            <div style="width: 100%; height: 100%; background: #f7fafc; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                <span class="dashicons dashicons-format-image" style="font-size: 40px; width: 40px; height: 40px;"></span>
            </div>
        <?php endif; ?>
        <div style="position: absolute; top: 12px; left: 12px;">
            <span style="background: var(--primary); padding: 4px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; color: white; box-shadow: 0 4px 10px rgba(255, 128, 0, 0.4); text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;"><?php echo esc_html($cat_name); ?></span>
        </div>
    </div>
    
    <div class="vh-product-content" style="padding: 0.85rem; flex-grow: 1; display: flex; flex-direction: column;">
        <h3 style="font-size: 16px; margin: 0 0 4px 0; line-height: 1.35; height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #1e293b; font-weight: 700;"><?php the_title(); ?></a>
        </h3>
        
        <div class="vh-product-vendor vh-product-meta-item" style="margin-bottom: 2px; font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
            <span class="dashicons dashicons-store" style="font-size: 12px; width: 12px; height: 12px;"></span>
            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;"><?php echo esc_html($company_name); ?></span>
            <?php if ($is_verified): ?>
                <span style="color: #0e9f6e; display: flex; align-items: center; gap: 2px; font-weight: 700; font-size: 10px;">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 12px; width: 12px; height: 12px;"></span> Terverifikasi
                </span>
            <?php endif; ?>
        </div>

        <?php 
        if (class_exists('VH_Rating')):
            $avg = VH_Rating::get_average_rating($vendor_id);
            $count = VH_Rating::get_review_count($vendor_id);
            if ($avg):
        ?>
        <div class="vh-rating-display" style="display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
            <span style="color: #f59e0b; font-size: 12px;"><?php echo VH_Rating::render_stars($avg); ?></span>
            <span style="font-size: 11px; font-weight: 700; color: #1e293b;"><?php echo $avg; ?></span>
            <span style="font-size: 10px; color: #94a3b8;">(<?php echo $count; ?>)</span>
        </div>
        <?php endif; endif; ?>

        <div class="vh-product-price" style="margin-top: 4px; margin-bottom: 2px;">
            <div class="vh-product-price-val" style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 0px;">
                <?php echo $price ? 'Rp ' . number_format($price, 0, ',', '.') : __('Hubungi Penjual', 'vendorhub'); ?>
            </div>
            <div class="vh-product-meta-item" style="font-size: 10px; color: #94a3b8; font-weight: 600;">
                <?php if ($moq): ?>
                    <?php printf(__('Min. Order: %s %s', 'vendorhub'), $moq, $unit); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="vh-product-footer" style="margin-top: auto; padding-top: 10px; border-top: 1px dashed #f1f5f9; display: flex; gap: 8px; align-items: center;">
            <div style="flex: 1;">
                <span class="vh-product-meta-item" style="font-size: 10px; color: #94a3b8; display: flex; align-items: center; gap: 3px;">
                    <span class="dashicons dashicons-location" style="font-size: 11px; width: 11px; height: 11px;"></span>
                    <?php echo esc_html($location ?: 'Nasional'); ?>
                </span>
            </div>
            <div style="display: flex; gap: 6px;">
                <a href="<?php echo add_query_arg(['rfq_product_id' => get_the_ID()], site_url('/minta-penawaran')); ?>" class="vh-btn" style="font-size: 11px; padding: 6px 12px; border-radius: 50px; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-weight: 700; transition: all 0.2s;"><?php _e('RFQ', 'vendorhub'); ?></a>
                <a href="<?php the_permalink(); ?>" class="vh-btn vh-btn-outline" style="font-size: 11px; padding: 6px 14px; border-radius: 50px; border: 1.5px solid var(--primary); color: var(--primary); font-weight: 700; background: transparent; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: inline-flex; align-items: center; justify-content: center;"><?php _e('Detail', 'vendorhub'); ?></a>
            </div>
        </div>
    </div>
</article>
