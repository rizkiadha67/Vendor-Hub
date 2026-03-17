<?php
/**
 * Tender Card Template
 */
$budget = get_post_meta( get_the_ID(), '_vh_tender_budget', true );
$deadline = get_post_meta( get_the_ID(), '_vh_tender_deadline', true );
$buyer_id = get_the_author_meta('ID');
$company_name = get_user_meta( $buyer_id, 'vh_company_name', true ) ?: get_the_author();
?>
<div class="vh-tender-card<?php echo has_post_thumbnail() ? ' has-thumbnail' : ''; ?>" style="background: white; border-radius: 16px; overflow: hidden; border: 1px solid #edf2f7; display: flex; align-items: stretch; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 1.5rem; padding: 0 !important;">
    <style>
        .vh-tender-card .vh-tender-image-col { width: 200px; height: 200px; flex-shrink: 0; overflow: hidden; margin: 0; padding: 0; position: relative; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
        .vh-tender-card .vh-tender-image-col img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .vh-tender-card .vh-tender-body { flex: 1; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; }
        .vh-tender-card .vh-tender-content-left { flex: 1; min-width: 0; }
        .vh-tender-card .vh-tender-meta-right { text-align: right; min-width: 200px; padding-left: 2rem; border-left: 1px solid #f1f5f9; }
        .vh-tender-card .vh-tc-row { display: flex; gap: 10px; margin-bottom: 6px; align-items: center; flex-wrap: wrap; }
        .vh-tender-card .vh-tc-deadline { font-size: 11px; color: #ef4444; font-weight: 600; display: flex; align-items: center; justify-content: flex-end; gap: 5px; }
        .vh-tender-card .vh-tender-meta-right strong { font-size: 1.3rem; }
        
        /* Mobile Overlay Badge - Hidden by default */
        .vh-tender-card .vh-mobile-badge { display: none; }

        @media (max-width: 768px) {
            .vh-tender-card { flex-direction: column !important; }
            .vh-tender-card .vh-tender-image-col { width: 100% !important; height: 200px !important; }
            .vh-tender-card .vh-tender-body { flex-direction: column !important; padding: 1.25rem !important; gap: 1rem !important; align-items: flex-start !important; }
            .vh-tender-card .vh-tender-meta-right { text-align: left !important; padding-left: 0 !important; border-left: none !important; border-top: 1px solid #f1f5f9 !important; padding-top: 1rem !important; width: 100% !important; min-width: 0 !important; }
            .vh-tender-card .vh-tc-deadline { justify-content: flex-start !important; }
            .vh-tender-card .vh-tender-meta-right strong { font-size: 1.2rem !important; }
            
            /* Show Mobile Overlay Badge, Hide Desktop Category */
            .vh-tender-card .vh-mobile-badge { display: block !important; }
            .vh-tender-card .vh-desktop-cat { display: none !important; }
        }
    </style>
    
    <div class="vh-tender-image-col">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('medium_large'); ?>
        <?php else: ?>
            <span class="dashicons dashicons-clipboard" style="font-size: 40px; width: 40px; height: 40px; color: #cbd5e1;"></span>
        <?php endif; ?>
        
        <?php 
        $terms = get_the_terms(get_the_ID(), 'vh_industry');
        if ($terms && !is_wp_error($terms)) : ?>
            <div class="vh-mobile-badge" style="position: absolute; top: 12px; left: 12px; z-index: 5;">
                <span style="background: var(--primary); color: white; font-size: 10px; padding: 4px 12px; border-radius: 50px; font-weight: 800; border: 1px solid rgba(255,255,255,0.2); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(4px);">
                    <?php echo esc_html($terms[0]->name); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <div class="vh-tender-body">
        <div class="vh-tender-content-left">
            <div class="vh-tc-row">
                <span class="vh-badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 10px; padding: 4px 12px; border-radius: 50px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 4px; text-transform: uppercase;">
                    <span class="dashicons dashicons-unlock" style="font-size: 12px; width: 12px; height: 12px; line-height: 1;"></span> 
                    <?php _e('TERBUKA', 'vendorhub'); ?>
                </span>
                <?php if ($terms && !is_wp_error($terms)) : ?>
                    <span class="vh-desktop-cat" style="background: #f1f5f9; color: #475569; font-size: 10px; padding: 4px 12px; border-radius: 50px; font-weight: 700; border: 1px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?php echo esc_html($terms[0]->name); ?>
                    </span>
                <?php endif; ?>
                <span style="font-size: 11px; color: #64748b; font-weight: 600;">
                    <span class="dashicons dashicons-building" style="font-size: 13px; width: 13px; height: 13px; vertical-align: text-top;"></span> 
                    <?php echo esc_html($company_name); ?>
                </span>
            </div>
            
            <h3 style="margin: 0 0 8px 0; font-size: 16px; line-height: 1.35;">
                <a href="<?php the_permalink(); ?>" style="color: #1e293b; text-decoration: none; font-weight: 800;"><?php the_title(); ?></a>
            </h3>
            
            <p class="text-muted" style="margin: 0; font-size: 14px; line-height: 1.6; color: #64748b;">
                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
            </p>
        </div>
        
        <div class="vh-tender-meta-right">
            <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;"><?php _e('Nilai Proyek', 'vendorhub'); ?></div>
            <strong style="color: var(--vh-primary); display: block; margin-bottom: 12px;">
                <?php echo $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : __('Hubungi Buyer', 'vendorhub'); ?>
            </strong>
            
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php if ($deadline): ?>
                <div class="vh-tc-deadline">
                    <span class="dashicons dashicons-clock" style="font-size: 12px; width: 12px; height: 12px;"></span>
                    Ditutup: <?php echo date_i18n('d M Y', strtotime($deadline)); ?>
                </div>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="vh-btn vh-btn-action" style="padding: 10px 20px; font-size: 12px; width: 100%; text-align: center; border-radius: 50px; justify-content: center; font-weight: 700; background: var(--primary); color: white; border: none; box-shadow: 0 4px 12px rgba(255, 128, 0, 0.2); transition: all 0.3s ease; display: inline-flex; align-items: center;">
                    <?php _e('Lihat Detail', 'vendorhub'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
