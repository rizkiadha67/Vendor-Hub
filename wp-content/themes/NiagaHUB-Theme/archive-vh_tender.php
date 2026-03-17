<?php
/**
 * Tender Archive Template - NiagaHUB
 */

get_header(); ?>

<div class="nh-tender-header" style="background: var(--secondary-color); color: white; padding: 4rem 0;">
    <div class="nh-container" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><?php _e('Pusat Tender & Proyek', 'niagahub-theme'); ?></h1>
            <p style="opacity: 0.8; font-size: 1.1rem;"><?php _e('Lihat berbagai permintaan pengadaan dari perusahaan besar dan kirimkan proposal terbaik Anda.', 'niagahub-theme'); ?></p>
        </div>
        <div class="header-search-alt" style="width: 400px;">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="vh-search-form">
                <input type="hidden" name="post_type" value="vh_tender">
                <input type="search" placeholder="<?php _e('Cari proyek tender...', 'niagahub-theme'); ?>" name="s" value="<?php echo get_search_query(); ?>">
                <button type="submit" class="vh-btn vh-btn-primary"><span class="dashicons dashicons-search"></span></button>
            </form>
        </div>
    </div>
</div>

<div class="nh-container" style="padding: 3rem 1.5rem;">
    <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
    <div class="vh-tender-layout" style="display: grid; grid-template-columns: 300px 1fr; gap: 3rem;">
        
        <!-- Tender Sidebar Filters -->
        <aside class="vh-tender-sidebar">
            <div class="vh-card">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;"><?php _e('Status Proyek', 'niagahub-theme'); ?></h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;"><label><input type="checkbox" checked> <?php _e('Terbuka', 'niagahub-theme'); ?></label></li>
                    <li style="margin-bottom: 8px;"><label><input type="checkbox"> <?php _e('Sedang Berjalan', 'niagahub-theme'); ?></label></li>
                    <li style="margin-bottom: 8px;"><label><input type="checkbox"> <?php _e('Selesai', 'niagahub-theme'); ?></label></li>
                </ul>
            </div>

            <div class="vh-card" style="margin-top: 1.5rem; background: #fff9f2; border-color: var(--primary-color);">
                <h4><?php _e('Butuh Sesuatu?', 'niagahub-theme'); ?></h4>
                <p style="font-size: 13px;"><?php _e('Buat pengumuman tender Anda sendiri agar vendor kami dapat menemukan Anda.', 'niagahub-theme'); ?></p>
                <a href="<?php echo site_url('/buat-tender'); ?>" class="btn-rfq-outline" style="display: block; text-align: center;"><?php _e('Buat Tender Baru', 'niagahub-theme'); ?></a>
            </div>
        </aside>

        <!-- Tender List -->
        <main class="vh-tender-list">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                $budget = get_post_meta( get_the_ID(), '_vh_tender_budget', true );
                $deadline = get_post_meta( get_the_ID(), '_vh_tender_deadline', true );
            ?>
                <div class="vh-card" style="margin-bottom: 1.5rem; padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h2 style="margin: 0 0 10px 0; font-size: 1.5rem;"><a href="<?php the_permalink(); ?>" style="color: var(--vh-text-dark); text-decoration: none;"><?php the_title(); ?></a></h2>
                            <div style="display: flex; gap: 15px; font-size: 14px; color: var(--vh-text-muted);">
                                <span><span class="dashicons dashicons-calendar-alt"></span> <?php echo get_the_date(); ?></span>
                                <span><span class="dashicons dashicons-businessman"></span> <?php the_author(); ?></span>
                            </div>
                        </div>
                        <div class="vh-tender-status vh-badge vh-badge-verified" style="background: #ecfdf5; color: #059669; border: 1px solid #10b981;">
                            <?php _e('OPEN', 'niagahub-theme'); ?>
                        </div>
                    </div>

                    <div style="margin: 1.5rem 0; font-size: 15px; color: #475569;">
                        <?php echo wp_trim_words( get_the_excerpt(), 30 ); ?>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <div style="display: flex; gap: 2rem;">
                            <div>
                                <small class="text-muted" style="display: block;"><?php _e('ESTIMASI BUDGET', 'niagahub-theme'); ?></small>
                                <strong style="color: var(--vh-secondary); font-size: 1.1rem;"><?php echo $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : __('Tanya Buyer', 'niagahub-theme'); ?></strong>
                            </div>
                            <div>
                                <small class="text-muted" style="display: block;"><?php _e('DEADLINE', 'niagahub-theme'); ?></small>
                                <strong style="color: #ef4444;"><?php echo $deadline ?: '-'; ?></strong>
                            </div>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="vh-btn vh-btn-action"><?php _e('Kirim Proposal', 'niagahub-theme'); ?></a>
                    </div>
                </div>
            <?php endwhile; else : ?>
                <p><?php _e('Belum ada tender terbuka saat ini.', 'niagahub-theme'); ?></p>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php get_footer(); ?>
