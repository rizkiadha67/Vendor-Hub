<?php
/**
 * Vendor Card Template
 */
$location     = get_user_meta( $vendor->ID, 'vh_location',     true );
$is_verified  = get_user_meta( $vendor->ID, 'vh_verified',     true );
$description  = get_user_meta( $vendor->ID, 'description',     true );
$company_name = get_user_meta( $vendor->ID, 'vh_company_name', true ) ?: $vendor->display_name;
$logo_id      = get_user_meta( $vendor->ID, 'vh_vendor_logo',  true );
$logo_url     = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';

// Industry Badge logic
$industry_slug = get_user_meta( $vendor->ID, 'vh_industry', true );
$industry_name = '';
if ($industry_slug) {
    $term_obj = get_term_by('slug', $industry_slug, 'vh_industry');
    if ($term_obj) $industry_name = $term_obj->name;
}

// Banner fallback
$banner_url   = get_template_directory_uri() . '/assets/images/banner1.png';
?>
<div class="vh-vendor-card<?php echo $is_verified === '1' ? ' is-verified' : ''; ?>">
    <style>
        .vh-vendor-card {
            background: white; 
            border-radius: 20px; 
            border: 1px solid #f1f5f9; 
            overflow: hidden; 
            height: 100%; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            display: flex; 
            flex-direction: column; 
            position: relative; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
        }
        .vh-vendor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
            border-color: #e2e8f0;
        }
        .vh-vendor-banner {
            width: 100%; 
            padding-top: 40%; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
            background-image: url('<?php echo esc_url($banner_url); ?>'); 
            background-size: cover; 
            background-position: center; 
            position: relative;
        }
        .vh-vendor-industry-badge {
            position: absolute; 
            top: 15px; 
            left: 15px;
            background: rgba(15, 23, 42, 0.4); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
            padding: 5px 14px; 
            border-radius: 50px; 
            font-size: 10px; 
            font-weight: 800; 
            color: white; 
            border: 1px solid rgba(255,255,255,0.2); 
            text-transform: uppercase; 
            letter-spacing: 0.8px; 
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .vh-vendor-header {
            padding: 0 1.25rem; 
            text-align: left; 
            margin-top: -30px; 
            display: flex; 
            align-items: flex-end; 
            gap: 1rem; 
            position: relative; 
            z-index: 2;
        }
        .vh-vendor-logo {
            width: 65px; 
            height: 65px; 
            border-radius: 18px; 
            background: white; 
            border: 4px solid white; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
            overflow: hidden; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        .vh-vendor-card:hover .vh-vendor-logo { transform: scale(1.05); }
        
        .vh-vendor-meta { padding-bottom: 5px; flex-grow: 1; min-width: 0; }
        .vh-vendor-name {
            font-size: 17px; 
            margin: 0 0 4px 0; 
            color: #0f172a; 
            font-weight: 800; 
            line-height: 1.2; 
            letter-spacing: -0.02em;
        }
        .vh-verified-badge {
            font-size: 9px; 
            background: #f0fdf4; 
            color: #166534; 
            padding: 2px 10px; 
            border-radius: 50px; 
            font-weight: 800; 
            display: inline-flex; 
            align-items: center; 
            gap: 4px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            border: 1px solid #dcfce7;
        }
        .vh-vendor-content { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
        .vh-vendor-desc {
            font-size: 13.5px; 
            color: #475569; 
            line-height: 1.6; 
            margin-bottom: 1.25rem; 
            flex-grow: 1;
            font-weight: 500;
        }
        .vh-vendor-stats {
            display: flex; 
            gap: 1rem; 
            margin-bottom: 1.25rem; 
            padding-top: 1rem; 
            border-top: 1px solid #f1f5f9;
        }
        .vh-stat-item {
            display: flex; 
            align-items: center; 
            gap: 6px; 
            font-size: 12px; 
            color: #64748b; 
            font-weight: 700;
        }
        .vh-stat-item .dashicons { font-size: 14px; width: 14px; height: 14px; }
        .vh-btn-profile {
            width: 100%; 
            border-radius: 14px; 
            padding: 12px; 
            font-weight: 800; 
            background: var(--primary); 
            border: none; 
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); 
            font-size: 13px; 
            display: block; 
            text-align: center; 
            color: white; 
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .vh-btn-profile:hover {
            background: #ea580c;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .vh-vendor-card { border-radius: 16px; }
            .vh-vendor-banner { padding-top: 45%; }
            .vh-vendor-header { margin-top: -35px; gap: 0.75rem; }
            .vh-vendor-logo { width: 60px; height: 60px; border-width: 3px; border-radius: 14px; }
            .vh-vendor-name { font-size: 15px; }
            .vh-vendor-desc { font-size: 13px; -webkit-line-clamp: 3; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 1rem; }
            .vh-btn-profile { padding: 10px; font-size: 12px; }
        }
    </style>
    
    <div class="vh-vendor-banner">
        <?php if ($industry_name) : ?>
            <div class="vh-vendor-industry-badge">
                <?php echo esc_html($industry_name); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="vh-vendor-header">
        <div class="vh-vendor-logo">
            <?php if ( $logo_url ) : ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company_name); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else : ?>
                <span class="dashicons dashicons-businessman" style="font-size: 24px; width: 24px; height: 24px; color: #cbd5e1;"></span>
            <?php endif; ?>
        </div>
        <div class="vh-vendor-meta">
            <h3 class="vh-vendor-name"><?php echo esc_html($company_name); ?></h3>
            <?php if ( $is_verified === '1' ) : ?>
                <span class="vh-verified-badge">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e('Verified', 'vendorhub'); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="vh-vendor-content">
        <p class="vh-vendor-desc">
            <?php echo wp_trim_words( $description ?: __('Penyedia solusi industri terpercaya yang bergabung dengan ekosistem NiagaHUB.', 'vendorhub'), 14 ); ?>
        </p>
        
        <div class="vh-vendor-stats">
            <div class="vh-stat-item">
                <span class="dashicons dashicons-location"></span>
                <?php echo esc_html( $location ?: 'Nasional' ); ?>
            </div>
            <div class="vh-stat-item">
                <span class="dashicons dashicons-star-filled" style="color: #f59e0b;"></span>
                <?php 
                $avg = class_exists('VH_Rating') ? VH_Rating::get_average_rating($vendor->ID) : null;
                echo $avg ?: '—';
                ?>
            </div>
        </div>
        
        <a href="<?php echo get_author_posts_url($vendor->ID); ?>" class="vh-btn-profile"><?php _e('Visit Profile', 'vendorhub'); ?></a>
    </div>
</div>
