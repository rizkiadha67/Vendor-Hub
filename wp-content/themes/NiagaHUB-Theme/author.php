<?php
/**
 * Vendor Profile Template (Author Page) - NiagaHUB
 */

get_header();

$vendor = get_queried_object();
$vendor_id = $vendor->ID;
$company_name = get_user_meta($vendor_id, 'vh_company_name', true) ?: $vendor->display_name;
$location = get_user_meta($vendor_id, 'vh_location', true) ?: 'Indonesia';
$is_verified = get_user_meta($vendor_id, 'vh_verified', true);
$nib = get_user_meta($vendor_id, 'vh_nib', true);
$description = $vendor->description ?: __('Vendor tepercaya di ekosistem NiagaHUB.', 'niagahub-theme');

$logo_id = get_user_meta($vendor_id, 'vh_vendor_logo', true);
$logo_url = '';
if ($logo_id) {
    if (is_numeric($logo_id)) {
        $logo_url = wp_get_attachment_image_url($logo_id, 'thumbnail');
    } else {
        $logo_url = $logo_id;
    }
}
?>

<div class="vh-vendor-profile-header" style="background: #f8fafc; border-bottom: 1px solid var(--border-color); padding: 4rem 0;">
    <div class="nh-container">
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
        <div style="display: flex; gap: 3rem; align-items: center;">
            <div class="vendor-logo-large" style="width: 150px; height: 150px; background: white; border: 1px solid var(--border-color); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0; overflow: hidden;">
                <?php if ($logo_url) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company_name); ?>" style="width: 100%; height: 100%; object-fit: contain; padding: 15px;">
                <?php else : ?>
                    <span class="dashicons dashicons-businessman" style="font-size: 80px; width: 80px; height: 80px; color: #cbd5e1;"></span>
                <?php endif; ?>
            </div>
            <div class="vendor-info-main">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <h1 style="margin: 0; font-size: 2.5rem; color: var(--vh-primary);"><?php echo esc_html($company_name); ?></h1>
                    <?php if ($is_verified === '1') : ?>
                        <span class="vh-badge vh-badge-verified" style="padding: 6px 15px; font-size: 14px;"><?php _e('Terverifikasi', 'niagahub-theme'); ?></span>
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 20px; color: var(--vh-text-muted); font-size: 1.1rem; margin-bottom: 1.5rem;">
                    <span><span class="dashicons dashicons-location"></span> <?php echo esc_html($location); ?></span>
                    <span><span class="dashicons dashicons-calendar-alt"></span> <?php _e('Bergabung: ', 'niagahub-theme'); ?><?php echo get_the_author_meta('user_registered', $vendor_id); ?></span>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button class="vh-btn vh-btn-primary vh-enquiry-btn" data-vendor="<?php echo $vendor_id; ?>" data-product="Profile Inquiry" style="padding: 10px 25px;">
                        <span class="dashicons dashicons-email-alt" style="margin-right: 8px;"></span>
                        <?php _e('Hubungi Perusahaan', 'niagahub-theme'); ?>
                    </button>
                    <button class="vh-btn" style="border: 1px solid var(--border-color); background: white; padding: 10px 20px;">
                        <span class="dashicons dashicons-share" style="margin-right: 8px;"></span>
                        <?php _e('Bagikan', 'niagahub-theme'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nh-container" style="padding: 4rem 1.5rem;">
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 4rem;">
        <!-- Left Column: Content -->
        <div class="vendor-profile-content">
            <section style="margin-bottom: 4rem;">
                <h2 style="border-bottom: 3px solid var(--primary-color); display: inline-block; padding-bottom: 5px; margin-bottom: 2rem;"><?php _e('Tentang Perusahaan', 'niagahub-theme'); ?></h2>
                <div style="font-size: 1.15rem; line-height: 1.8; color: #475569; margin-bottom: 3rem;">
                    <?php echo wpautop($description); ?>
                </div>

                <?php if (class_exists('VH_Rating')): ?>
                    <h2 style="border-bottom: 3px solid var(--primary-color); display: inline-block; padding-bottom: 5px; margin-bottom: 1rem;"><?php _e('Ulasan Pembeli', 'niagahub-theme'); ?></h2>
                    <?php echo VH_Rating::render_reviews(['user_id' => $vendor_id]); ?>
                <?php endif; ?>
            </section>

            <section>
                <h2 style="border-bottom: 3px solid var(--primary-color); display: inline-block; padding-bottom: 5px; margin-bottom: 2rem;"><?php _e('Katalog Produk', 'niagahub-theme'); ?></h2>
                
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'vh_product',
                    'author' => $vendor_id,
                    'posts_per_page' => 12,
                    'paged' => $paged
                );
                $products = new WP_Query($args);

                if ($products->have_posts()) : ?>
                    <div class="vh-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                        <?php while ($products->have_posts()) : $products->the_post(); 
                            $price = get_post_meta(get_the_ID(), '_vh_price', true);
                        ?>
                            <div class="vh-card">
                                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                    <div style="aspect-ratio: 1/1; overflow: hidden; border-radius: 8px; margin-bottom: 1rem; background: #f1f5f9;">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium', array('style' => 'width:100%; height:100%; object-fit:cover;')); ?>
                                        <?php else : ?>
                                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#cbd5e1;">
                                                <span class="dashicons dashicons-format-image" style="font-size: 40px; width:40px; height:40px;"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h4 style="margin: 0 0 10px 0; font-size: 0.95rem; line-height: 1.4; height: 2.6rem; overflow: hidden;"><?php the_title(); ?></h4>
                                    <strong style="color: var(--primary-color); display: block; margin-bottom: 10px;">
                                        <?php echo $price ? 'Rp ' . number_format($price, 0, ',', '.') : __('Hubungi Vendor', 'niagahub-theme'); ?>
                                    </strong>
                                    <span class="vh-btn vh-btn-primary" style="padding: 5px; font-size: 12px; width: 100%; text-align: center; border-radius: 4px;"><?php _e('Lihat Detail', 'niagahub-theme'); ?></span>
                                </a>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    
                    <div class="vh-pagination" style="margin-top: 3rem;">
                        <?php echo paginate_links(array('total' => $products->max_num_pages)); ?>
                    </div>
                <?php else : ?>
                    <div class="vh-card" style="text-align: center; padding: 4rem;">
                        <p class="text-muted"><?php _e('Vendor ini belum mengunggah produk.', 'niagahub-theme'); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="vendor-profile-sidebar">
            <div class="vh-card" style="padding: 2rem;">
                <h3 style="margin-top: 0; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem;"><?php _e('Informasi Legalitas', 'niagahub-theme'); ?></h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span class="text-muted"><?php _e('Tipe Bisnis', 'niagahub-theme'); ?></span>
                        <strong><?php _e('Manufaktur / Supplier', 'niagahub-theme'); ?></strong>
                    </div>
                    <?php if ($nib) : ?>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span class="text-muted"><?php _e('NIB / Izin', 'niagahub-theme'); ?></span>
                        <strong><?php echo esc_html($nib); ?></strong>
                    </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span class="text-muted"><?php _e('Tahun Berdiri', 'niagahub-theme'); ?></span>
                        <strong>2015</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span class="text-muted"><?php _e('Respon Rate', 'niagahub-theme'); ?></span>
                        <strong style="color: #059669;">98% (Sangat Cepat)</strong>
                    </div>
                </div>
                
                <div style="margin-top: 2.5rem; background: #fff9f2; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--primary-color);">
                    <h4 style="margin-top: 0; color: var(--vh-primary);"><?php _e('Ingin Penawaran Khusus?', 'niagahub-theme'); ?></h4>
                    <p style="font-size: 13px; color: #475569; margin-bottom: 1.5rem;"><?php _e('Kirimkan RFQ langsung untuk mendapatkan harga project khusus dari vendor ini.', 'niagahub-theme'); ?></p>
                    <a href="<?php echo site_url('/marketplace-produk'); ?>" class="vh-btn vh-btn-action" style="width: 100%; text-align: center;"><?php _e('Kirim RFQ Sekarang', 'niagahub-theme'); ?></a>
                </div>
            </div>
            
            <div class="vh-card" style="margin-top: 2rem; text-align: center;">
                <h3 style="font-size: 1.1rem; margin-top: 0;"><?php _e('Rating & Ulasan', 'niagahub-theme'); ?></h3>
                <?php 
                if (class_exists('VH_Rating')):
                    $avg = VH_Rating::get_average_rating($vendor_id);
                    $count = VH_Rating::get_review_count($vendor_id);
                    if ($avg):
                ?>
                    <div style="font-size: 3rem; font-weight: 800; color: var(--vh-primary); margin: 10px 0;"><?php echo $avg; ?></div>
                    <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 10px;"><?php echo VH_Rating::render_stars($avg); ?></div>
                    <p class="text-muted" style="font-size: 13px;"><?php printf(__('Berdasarkan %s ulasan', 'niagahub-theme'), $count); ?></p>
                <?php else: ?>
                    <div style="font-size: 3rem; font-weight: 800; color: #cbd5e1; margin: 10px 0;">—</div>
                    <p class="text-muted" style="font-size: 13px;"><?php _e('Belum ada ulasan', 'niagahub-theme'); ?></p>
                <?php endif; endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
