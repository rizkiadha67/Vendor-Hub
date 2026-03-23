<?php
/**
 * Service List Template
 * Shortcode: [vh_marketplace_services]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$paged    = max(1, get_query_var('paged'));
$industry = isset($_GET['industry']) ? sanitize_text_field($_GET['industry']) : '';
$search   = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$location = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : '';

$args = [
    'post_type'      => 'vh_service',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'post_status'    => 'publish',
];

if ($search) $args['s'] = $search;
if ($industry) $args['tax_query'] = [['taxonomy' => 'vh_industry', 'field' => 'slug', 'terms' => $industry]];

$services = new WP_Query($args);
$industries = get_terms(['taxonomy' => 'vh_industry', 'hide_empty' => true]);
?>

<div class="vh-container vh-service-marketplace">

    <!-- Header -->
    <div style="margin-bottom:2rem;">
        <h2 style="font-size:1.75rem;font-weight:700;margin:0 0 0.5rem;">🛠 Marketplace Jasa</h2>
        <p style="color:#6b7280;margin:0;">Temukan jasa profesional dari vendor terpercaya di seluruh Indonesia</p>
    </div>

    <!-- Filters -->
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:2rem;background:#f9fafb;padding:1.25rem;border-radius:12px;">
        <input type="hidden" name="page_id" value="<?= get_the_ID() ?>">
        
        <input type="text" name="s" value="<?= esc_attr($search) ?>" placeholder="🔍 Cari jasa..." 
               style="flex:1;min-width:200px;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
        
        <select name="industry" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;min-width:160px;">
            <option value="">Semua Industri</option>
            <?php foreach ($industries as $term): ?>
            <option value="<?= esc_attr($term->slug) ?>" <?= selected($industry, $term->slug, false) ?>><?= esc_html($term->name) ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="text" name="location" value="<?= esc_attr($location) ?>" placeholder="📍 Lokasi"
               style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;min-width:140px;">
        
        <button type="submit" class="vh-btn vh-btn-action" style="padding:10px 20px;">Cari</button>
        <?php if ($search || $industry || $location): ?>
        <a href="?" style="padding:10px 16px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;color:#6b7280;text-decoration:none;line-height:1.4;">✕ Reset</a>
        <?php endif; ?>
    </form>

    <!-- Results Count -->
    <div style="margin-bottom:1rem;color:#6b7280;font-size:0.9rem;">
        <?= $services->found_posts ?> jasa ditemukan
    </div>

    <!-- Grid -->
    <?php if ($services->have_posts()): ?>
    <div class="vh-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;margin-bottom:2rem;">
        <?php while ($services->have_posts()): $services->the_post();
            $author_id    = get_the_author_meta('ID');
            $company_name = get_user_meta($author_id, 'vh_company_name', true) ?: get_the_author();
            $verified     = get_user_meta($author_id, 'vh_verified', true) === '1';
            $logo_id      = get_user_meta($author_id, 'vh_vendor_logo', true);
            $logo_url     = $logo_id ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';
            $location_v   = get_user_meta($author_id, 'vh_location', true);
            $terms        = get_the_terms(get_the_ID(), 'vh_industry');
            $industry_label = $terms ? $terms[0]->name : '';
        ?>
        <div class="vh-card" style="padding:0;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            
            <!-- Thumbnail -->
            <?php if (has_post_thumbnail()): ?>
            <a href="<?= get_permalink() ?>">
                <div style="height:180px;overflow:hidden;">
                    <?= get_the_post_thumbnail(get_the_ID(), 'medium', ['style'=>'width:100%;height:100%;object-fit:cover;']) ?>
                </div>
            </a>
            <?php else: ?>
            <div style="height:120px;background:linear-gradient(135deg,#7e3af2,#1a56db);display:flex;align-items:center;justify-content:center;">
                <span style="font-size:3rem;">🛠</span>
            </div>
            <?php endif; ?>

            <div style="padding:1.25rem;">
                <!-- Industry tag -->
                <?php if ($industry_label): ?>
                <span style="background:#ede9fe;color:#5b21b6;padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;"><?= esc_html($industry_label) ?></span>
                <?php endif; ?>

                <h3 style="margin:0.75rem 0 0.5rem;font-size:1rem;line-height:1.4;">
                    <a href="<?= get_permalink() ?>" style="color:inherit;text-decoration:none;"><?= get_the_title() ?></a>
                </h3>

                <p style="color:#6b7280;font-size:0.85rem;margin:0 0 1rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= wp_trim_words(get_the_excerpt(), 15) ?></p>

                <!-- Vendor info -->
                <div style="display:flex;align-items:center;gap:0.5rem;border-top:1px solid #f3f4f6;padding-top:0.75rem;">
                    <?php if ($logo_url): ?>
                    <img src="<?= esc_url($logo_url) ?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" alt="">
                    <?php endif; ?>
                    <div style="flex:1;min-width:0;">
                        <p style="margin:0;font-size:0.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= esc_html($company_name) ?>
                            <?php if ($verified): ?><span style="color:#0e9f6e;">✓</span><?php endif; ?>
                        </p>
                        <?php if ($location_v): ?><p style="margin:0;font-size:0.75rem;color:#9ca3af;">📍 <?= esc_html($location_v) ?></p><?php endif; ?>
                    </div>
                    <a href="<?= get_permalink() ?>" class="vh-btn" style="background:#7e3af2;color:white;font-size:0.8rem;padding:5px 12px;white-space:nowrap;">Detail</a>
                </div>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <!-- Pagination -->
    <?php 
    $pagination = paginate_links(['total' => $services->max_num_pages, 'current' => $paged, 'type' => 'array']);
    if ($pagination): ?>
    <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
        <?php foreach ($pagination as $link): ?>
        <span style="<?= strpos($link,'current') ? 'background:#7e3af2;color:white;' : 'background:#f3f4f6;color:#374151;' ?>padding:8px 14px;border-radius:8px;font-size:0.9rem;"><?= $link ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:4rem 2rem;color:#9ca3af;">
        <div style="font-size:4rem;">🔍</div>
        <h3 style="margin:1rem 0 0.5rem;color:#374151;">Tidak ada jasa ditemukan</h3>
        <p>Coba ubah filter pencarian Anda</p>
    </div>
    <?php endif; ?>

</div>
