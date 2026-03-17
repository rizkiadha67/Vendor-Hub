<?php
/**
 * Single Tender Template - NiagaHUB
 */

get_header(); ?>

<div class="nh-container" style="padding: 3rem 1.5rem;">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        $budget = get_post_meta( get_the_ID(), '_vh_tender_budget', true );
        $deadline = get_post_meta( get_the_ID(), '_vh_tender_deadline', true );
        $buyer_id = get_post_field( 'post_author', get_the_ID() );
    ?>
        
        <!-- Breadcrumbs -->
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>

        <div class="vh-grid" style="grid-template-columns: 1fr 350px; gap: 3rem; align-items: start;">
            <!-- Main Content -->
            <div class="vh-tender-main">
                <div class="vh-card" style="padding: 3rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h1 style="margin: 0; font-size: 2.5rem;"><?php the_title(); ?></h1>
                        <span class="vh-badge" style="background: #ecfdf5; color: #059669; border: 1px solid #10b981; padding: 8px 16px; font-weight: 700;">
                            <?php _e('OPEN TENDER', 'niagahub-theme'); ?>
                        </span>
                    </div>

                    <div style="display: flex; gap: 2rem; margin-bottom: 3rem; padding: 1.5rem; background: #f8fafc; border-radius: 12px;">
                        <div>
                            <span class="text-muted" style="display: block; font-size: 12px; text-transform: uppercase;"><?php _e('Diumumkan Pada', 'niagahub-theme'); ?></span>
                            <strong><?php echo get_the_date(); ?></strong>
                        </div>
                        <div style="width: 1px; background: var(--border-color);"></div>
                        <div>
                            <span class="text-muted" style="display: block; font-size: 12px; text-transform: uppercase;"><?php _e('Lokasi Proyek', 'niagahub-theme'); ?></span>
                            <strong><?php echo get_user_meta($buyer_id, 'vh_location', true) ?: 'Nasional'; ?></strong>
                        </div>
                    </div>

                    <div class="vh-description">
                        <h3 style="border-bottom: 2px solid var(--primary-color); display: inline-block; margin-bottom: 1.5rem;"><?php _e('Rincian Kebutuhan & Spesifikasi', 'niagahub-theme'); ?></h3>
                        <div style="line-height: 1.8; color: #475569; font-size: 1.1rem;">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <!-- Proposal Submission Section -->
                <div id="submit-proposal" class="vh-card" style="margin-top: 2rem; padding: 3rem;">
                    <h3><?php _e('Kirim Proposal Penawaran', 'niagahub-theme'); ?></h3>
                    <?php if ( is_user_logged_in() && vh_is_vendor() ) : ?>
                        <p class="text-muted"><?php _e('Gunakan formulir di bawah ini untuk mengirimkan dokumen proposal dan harga penawaran Anda.', 'niagahub-theme'); ?></p>
                        <form style="margin-top: 2rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Harga Penawaran (IDR)', 'niagahub-theme'); ?></label>
                                <input type="number" placeholder="Contoh: 50000000" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                            </div>
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Dokumen Proposal (PDF/Excel)', 'niagahub-theme'); ?></label>
                                <input type="file" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                            </div>
                            <div style="margin-bottom: 2rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Catatan Tambahan', 'niagahub-theme'); ?></label>
                                <textarea rows="4" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;"></textarea>
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" class="vh-btn vh-btn-action" style="flex: 2; padding: 15px; font-size: 1.1rem;">
                                    <?php _e('Submit Proposal Sekarang', 'niagahub-theme'); ?>
                                </button>
                                <button type="button" class="vh-btn vh-btn-primary vh-enquiry-btn" data-vendor="<?php echo $buyer_id; ?>" data-product="<?php _e('Tender: ', 'niagahub-theme'); ?><?php the_title(); ?>" style="flex: 1; padding: 15px; font-size: 1.1rem;">
                                    <span class="dashicons dashicons-format-chat" style="margin-right: 8px;"></span>
                                    <?php _e('Tanya Buyer', 'niagahub-theme'); ?>
                                </button>
                            </div>
                        </form>
                    <?php elseif ( ! is_user_logged_in() ) : ?>
                        <div style="text-align: center; padding: 2rem; background: #fff1f2; border-radius: 12px; border: 1px solid #fecaca;">
                            <p style="color: #991b1b; font-weight: 600;"><?php _e('Anda harus masuk sebagai Vendor untuk mengirim proposal.', 'niagahub-theme'); ?></p>
                            <a href="<?php echo site_url('/auth'); ?>" class="vh-btn vh-btn-primary" style="margin-top: 1rem;"><?php _e('Masuk / Daftar', 'niagahub-theme'); ?></a>
                        </div>
                    <?php else : ?>
                        <div style="text-align: center; padding: 2rem; background: #fef9c3; border-radius: 12px; border: 1px solid #fde047;">
                            <p style="color: #854d0e; font-weight: 600;"><?php _e('Hanya akun Vendor yang dapat melihat dan mengirim proposal tender.', 'niagahub-theme'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Info -->
            <aside class="vh-tender-sidebar">
                <div class="vh-card" style="padding: 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <span class="text-muted" style="display: block; font-size: 12px; text-transform: uppercase;"><?php _e('Estimasi Budget', 'niagahub-theme'); ?></span>
                        <strong style="font-size: 1.8rem; color: var(--vh-secondary);"><?php echo $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : __('Tanya Buyer', 'niagahub-theme'); ?></strong>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <span class="text-muted" style="display: block; font-size: 12px; text-transform: uppercase;"><?php _e('Batas Waktu (Deadline)', 'niagahub-theme'); ?></span>
                        <div style="display: flex; align-items: center; gap: 8px; color: #ef4444; font-weight: 700; font-size: 1.2rem;">
                            <span class="dashicons dashicons-clock"></span>
                            <?php echo $deadline ?: 'Segera'; ?>
                        </div>
                    </div>

                    <div style="padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <span class="text-muted" style="display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 1rem;"><?php _e('Informasi Perusahaan', 'niagahub-theme'); ?></span>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <span class="dashicons dashicons-businessman" style="color: #94a3b8;"></span>
                            </div>
                            <div>
                                <strong style="display: block;"><?php the_author(); ?></strong>
                                <span class="vh-badge" style="font-size: 10px;"><?php _e('Verified Buyer', 'niagahub-theme'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="vh-card" style="margin-top: 2rem; background: var(--secondary-color); color: white; border: none; text-align: center;">
                    <h4 style="color: var(--primary-color);"><?php _e('Butuh Bantuan?', 'niagahub-theme'); ?></h4>
                    <p style="font-size: 13px; opacity: 0.8;"><?php _e('Tim kami siap membantu Anda menyusun proposal pemenang.', 'niagahub-theme'); ?></p>
                    <a href="#" class="vh-btn" style="background: white; color: var(--secondary-color); width: 100%;"><?php _e('Hubungi Support', 'niagahub-theme'); ?></a>
                </div>
            </aside>
        </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
