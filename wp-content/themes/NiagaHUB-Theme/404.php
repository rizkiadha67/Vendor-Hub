<?php
/**
 * 404 Error Template - NiagaHUB
 */

get_header(); ?>

<div class="nh-container" style="padding: 6rem 1.5rem; text-align: center; min-height: 70vh; display: flex; flex-direction: column; justify-content: center;">
    <div style="margin-bottom: 3rem;">
        <h1 style="font-size: 8rem; color: #e2e8f0; margin: 0; line-height: 1;">404</h1>
        <h2 style="font-size: 2.5rem; margin-top: -20px; color: var(--secondary-color);"><?php _e('Halaman Tidak Ditemukan', 'niagahub-theme'); ?></h2>
    </div>

    <p style="font-size: 1.2rem; color: var(--vh-text-muted); max-width: 600px; margin: 0 auto 3rem;">
        <?php _e('Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan. Gunakan kotak pencarian di bawah untuk mencari apa yang Anda butuhkan.', 'niagahub-theme'); ?>
    </p>

    <div style="max-width: 500px; margin: 0 auto 3rem; width: 100%;">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <div class="nh-search-container">
                <input type="search" placeholder="<?php _e('Cari produk, vendor, atau tender...', 'niagahub-theme'); ?>" name="s" style="width: 100%;">
                <button type="submit" class="nh-search-btn"><span class="dashicons dashicons-search"></span></button>
            </div>
        </form>
    </div>

    <div class="nh-404-actions" style="display: flex; gap: 1rem; justify-content: center;">
        <a href="<?php echo home_url(); ?>" class="vh-btn vh-btn-primary" style="padding: 1rem 2rem;">
            <span class="dashicons dashicons-admin-home" style="margin-right: 8px;"></span>
            <?php _e('Ke Beranda', 'niagahub-theme'); ?>
        </a>
        <a href="<?php echo site_url('/marketplace-produk'); ?>" class="vh-btn vh-btn-action" style="padding: 1rem 2rem;">
            <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span>
            <?php _e('Lihat Marketplace', 'niagahub-theme'); ?>
        </a>
    </div>

    <div style="margin-top: 5rem; padding-top: 3rem; border-top: 1px solid var(--border-color);">
        <p class="text-muted"><?php _e('Perlu bantuan pengadaan? Hubungi tim support kami.', 'niagahub-theme'); ?></p>
        <a href="#" style="color: var(--primary-color); font-weight: 700; text-decoration: underline;"><?php _e('Buka Pusat Bantuan', 'niagahub-theme'); ?></a>
    </div>
</div>

<?php get_footer(); ?>
