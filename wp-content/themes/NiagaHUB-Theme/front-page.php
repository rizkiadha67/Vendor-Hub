<?php get_header(); ?>

<!-- Hero Section Premium Slider -->
<section class="nh-hero-premium">
    <div class="nh-container">
        <div class="nh-hero-layout">
            <!-- Sidebar Categories -->
            <aside class="nh-hero-sidebar">
                <nav class="nh-category-nav">
                    <div class="nh-cat-header"><?php _e('Semua Kategori', 'niagahub-theme'); ?></div>
                    <ul>
                        <li><a href="#"><span class="dashicons dashicons-hammer"></span> <?php _e('Konstruksi & Perkakas', 'niagahub-theme'); ?></a></li>
                        <li><a href="#"><span class="dashicons dashicons-desktop"></span> <?php _e('Komputer & Teknologi', 'niagahub-theme'); ?></a></li>
                        <li><a href="#"><span class="dashicons dashicons-admin-generic"></span> <?php _e('Mesin Industri', 'niagahub-theme'); ?></a></li>
                        <li><a href="#"><span class="dashicons dashicons-car"></span> <?php _e('Otomotif & Transportasi', 'niagahub-theme'); ?></a></li>
                        <li><a href="#"><span class="dashicons dashicons-cart"></span> <?php _e('Kebutuhan Kantor', 'niagahub-theme'); ?></a></li>
                        <li><a href="#"><span class="dashicons dashicons-lightbulb"></span> <?php _e('Energi & Listrik', 'niagahub-theme'); ?></a></li>
                    </ul>
                    <a href="<?php echo site_url('/marketplace-produk'); ?>" class="nh-view-all-cats"><?php _e('Lihat Semua Kategori', 'niagahub-theme'); ?> &raquo;</a>
                </nav>
            </aside>

            <!-- Main Slider Area -->
            <div class="nh-main-slider">
                <div class="nh-slider-wrapper">
                    <div class="nh-slide active" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner1.png');">
                        <div class="nh-slide-overlay">
                            <div class="nh-slide-content">
                                <span class="badge-premium"><?php _e('Solusi Manufaktur', 'niagahub-theme'); ?></span>
                                <h2><?php _e('Penyedia Mesin Industri Terlengkap', 'niagahub-theme'); ?></h2>
                                <p><?php _e('Dapatkan penawaran harga grosir langsung dari distributor resmi.', 'niagahub-theme'); ?></p>
                                <a href="<?php echo site_url('/marketplace-produk'); ?>" class="vh-btn vh-btn-action"><?php _e('Belanja Sekarang', 'niagahub-theme'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="nh-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner2.png');">
                        <div class="nh-slide-overlay">
                            <div class="nh-slide-content">
                                <span class="badge-premium"><?php _e('Logistik & Supply', 'niagahub-theme'); ?></span>
                                <h2><?php _e('Pengadaan Barang Kantor Skala Besar', 'niagahub-theme'); ?></h2>
                                <p><?php _e('Efisienkan proses procurement perusahaan Anda bersama NiagaHUB.', 'niagahub-theme'); ?></p>
                                <a href="<?php echo site_url('/tender'); ?>" class="vh-btn vh-btn-primary"><?php _e('Buka Tender', 'niagahub-theme'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="nh-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner3.png');">
                        <div class="nh-slide-overlay">
                            <div class="nh-slide-content">
                                <span class="badge-premium"><?php _e('Verifikasi Vendor', 'niagahub-theme'); ?></span>
                                <h2><?php _e('Bermitra dengan Vendor Terverifikasi', 'niagahub-theme'); ?></h2>
                                <p><?php _e('Jaminan keamanan dan kualitas untuk setiap transaksi bisnis Anda.', 'niagahub-theme'); ?></p>
                                <a href="<?php echo site_url('/auth'); ?>" class="vh-btn vh-btn-action"><?php _e('Jadi Vendor', 'niagahub-theme'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="nh-slider-dots">
                    <span class="dot active" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                </div>
            </div>
        </div>

        <!-- Quick Access Features -->
        <div class="nh-hero-features-bar">
            <div class="feature-item">
                <span class="dashicons dashicons-clipboard"></span>
                <div class="text">
                    <strong><?php _e('Minta Penawaran (RFQ)', 'niagahub-theme'); ?></strong>
                    <p><?php _e('Respon dalam 24 jam', 'niagahub-theme'); ?></p>
                </div>
            </div>
            <div class="feature-item">
                <span class="dashicons dashicons-businessman"></span>
                <div class="text">
                    <strong><?php _e('Buka Tender', 'niagahub-theme'); ?></strong>
                    <p><?php _e('Dapatkan harga kompetitif', 'niagahub-theme'); ?></p>
                </div>
            </div>
            <div class="feature-item">
                <span class="dashicons dashicons-shield"></span>
                <div class="text">
                    <strong><?php _e('Vendor Terverifikasi', 'niagahub-theme'); ?></strong>
                    <p><?php _e('Jaminan kualitas 100%', 'niagahub-theme'); ?></p>
                </div>
            </div>
            <div class="feature-item">
                <span class="dashicons dashicons-admin-generic"></span>
                <div class="text">
                    <strong><?php _e('Suku Cadang Asli', 'niagahub-theme'); ?></strong>
                    <p><?php _e('Langsung dari pabrik', 'niagahub-theme'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Industries Carousel -->
<section class="nh-industries" style="padding: 4rem 0; background: white; overflow: hidden;">
    <div class="nh-container">
        <div class="nh-section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800;"><?php _e('Industri Populer', 'niagahub-theme'); ?></h2>
                <p class="text-muted" style="margin: 0.5rem 0 0; font-size: 15px;"><?php _e('Jelajahi vendor berdasarkan sektor industri pilihan.', 'niagahub-theme'); ?></p>
            </div>
            <div class="nh-carousel-controls" style="display: flex; gap: 0.75rem;">
                <button class="nh-carousel-btn prev" style="width: 44px; height: 44px; border-radius: 50%; border: 1px solid #e2e8f0; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                </button>
                <button class="nh-carousel-btn next" style="width: 44px; height: 44px; border-radius: 50%; border: 1px solid #e2e8f0; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </button>
            </div>
        </div>

        <div class="nh-industry-carousel-wrapper" style="position: relative; margin: 0 -1.5rem; padding: 0 1.5rem;">
            <div class="nh-industry-carousel" id="industryCarousel" style="display: flex; gap: 1.5rem; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth; padding-bottom: 1rem;">
                <style>
                    #industryCarousel::-webkit-scrollbar { display: none; }
                    .nh-industry-card-new {
                        flex: 0 0 calc(25% - 1.25rem);
                        min-width: 260px;
                        border-radius: 20px;
                        overflow: hidden;
                        position: relative;
                        scroll-snap-align: start;
                        text-decoration: none;
                        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                        /* 16:9 aspect ratio */
                        aspect-ratio: 16 / 9;
                    }
                    .nh-industry-card-new:hover { transform: translateY(-8px) scale(1.01); }
                    .nh-industry-card-new img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; position: absolute; inset: 0; }
                    .nh-industry-card-new:hover img { transform: scale(1.1); }
                    .nh-industry-overlay {
                        position: absolute;
                        inset: 0;
                        background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 50%, transparent 100%);
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                        padding: 1.5rem;
                        color: white;
                    }
                    .nh-industry-overlay h4 { color: white; margin: 0; font-size: 1.1rem; font-weight: 700; line-height: 1.2; }
                    .nh-industry-overlay .industry-count { font-size: 12px; opacity: 0.8; margin-bottom: 6px; font-weight: 500; background: rgba(255,255,255,0.15); display: inline-block; padding: 2px 8px; border-radius: 20px; }
                    
                    @media (max-width: 1024px) { .nh-industry-card-new { flex: 0 0 calc(33.333% - 1rem); } }
                    @media (max-width: 768px) { .nh-industry-card-new { flex: 0 0 calc(50% - 0.75rem); min-width: 200px; } }
                    @media (max-width: 480px) { .nh-industry-card-new { flex: 0 0 80%; } }
                </style>
                
                <?php
                $terms = get_terms( array(
                    'taxonomy'   => 'vh_industry',
                    'hide_empty' => false,
                ) );

                $img_map = array(
                    'konstruksi-perkakas'    => 'industry_construction.png',
                    'komputer-teknologi'     => 'industry_tech.png',
                    'mesin-industri'         => 'industry_industrial.png',
                    'otomotif-transportasi'  => 'industry_automotive.png',
                    'kebutuhan-kantor'       => 'industry_office.png',
                    'energi-listrik'         => 'industry_energy.png'
                );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                    foreach ( $terms as $term ) : 
                        $slug = $term->slug;
                        $img_name = isset($img_map[$slug]) ? $img_map[$slug] : 'banner1.png';
                        $img_url = get_theme_file_uri('/assets/images/' . $img_name);
                        
                        // Fallback if file doesn't exist locally (check on disk)
                        $file_path = get_template_directory() . '/assets/images/' . $img_name;
                        if (!file_exists($file_path)) {
                            $img_url = get_theme_file_uri('/assets/images/banner1.png');
                        }
                        ?>
                        <!-- Industry Card: <?php echo $slug; ?> -->
                        <a href="<?php echo get_term_link( $term ); ?>" class="nh-industry-card-new">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($term->name); ?>" loading="lazy">
                            <div class="nh-industry-overlay">
                                <div class="industry-count"><?php echo sprintf(__('%d Vendor', 'niagahub-theme'), $term->count); ?></div>
                                <h4><?php echo esc_html($term->name); ?></h4>
                            </div>
                        </a>
                    <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('industryCarousel');
    const prevBtn = document.querySelector('.nh-carousel-btn.prev');
    const nextBtn = document.querySelector('.nh-carousel-btn.next');

    if (carousel && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -carousel.offsetWidth * 0.8, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: carousel.offsetWidth * 0.8, behavior: 'smooth' });
        });

        // Toggle btn opacity based on scroll
        const toggleButtons = () => {
            prevBtn.style.opacity = carousel.scrollLeft <= 5 ? '0.3' : '1';
            prevBtn.style.pointerEvents = carousel.scrollLeft <= 5 ? 'none' : 'auto';
            
            const maxScroll = carousel.scrollWidth - carousel.offsetWidth;
            nextBtn.style.opacity = carousel.scrollLeft >= maxScroll - 5 ? '0.3' : '1';
            nextBtn.style.pointerEvents = carousel.scrollLeft >= maxScroll - 5 ? 'none' : 'auto';
        };

        carousel.addEventListener('scroll', toggleButtons);
        window.addEventListener('resize', toggleButtons);
        toggleButtons();
    }
});
</script>

<!-- Featured Vendors -->
<section class="nh-featured-vendors" style="padding: 4rem 0; background: #fff; border-top: 1px solid var(--border-color);">
    <div class="nh-container">
        <div class="vh-section-header">
            <div class="vh-section-titles">
                <h2 class="vh-section-title"><?php _e('Vendor Pilihan', 'niagahub-theme'); ?></h2>
                <p class="vh-section-sub"><?php _e('Perusahaan terverifikasi dengan reputasi terbaik.', 'niagahub-theme'); ?></p>
            </div>
            <a href="<?php echo site_url('/marketplace-vendor'); ?>" class="vh-section-link"><?php _e('Cari Vendor →', 'niagahub-theme'); ?></a>
        </div>

        <?php echo do_shortcode('[vh_marketplace_vendor]'); ?>
    </div>
</section>

<!-- Latests Products Marketplace -->
<section class="nh-latest-products" style="padding: 4rem 0; background: var(--bg-color);">
    <div class="nh-container">
        <div class="nh-section-header" style="margin-bottom: 2rem;">
            <h2 style="margin: 0;"><?php _e('Produk Terbaru', 'niagahub-theme'); ?></h2>
            <p class="text-muted" style="margin: 0.5rem 0 0;"><?php _e('Penawaran terbaik dari vendor terverifikasi.', 'niagahub-theme'); ?></p>
        </div>

        <?php echo do_shortcode('[vh_marketplace_products]'); ?>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo site_url('/marketplace-produk'); ?>" class="btn-rfq" style="background: transparent; color: var(--primary-color); border: 2px solid var(--primary-color); padding: 0.8rem 2.5rem; border-radius: 50px; font-weight: 700; transition: all 0.3s ease; display: inline-block;">
                <?php _e('Lihat Semua Produk', 'niagahub-theme'); ?>
            </a>
        </div>
    </div>
</section>

<!-- Latest Tenders -->
<section class="nh-latest-tenders" style="padding: 4rem 0; background: #f8fafc;">
    <div class="nh-container">
        
        <div class="vh-section-header">
            <div class="vh-section-titles">
                <h2 class="vh-section-title"><?php _e('Pusat Tender Terbaru', 'niagahub-theme'); ?></h2>
                <p class="vh-section-sub"><?php _e('Proyek pengadaan terbuka untuk vendor NiagaHUB.', 'niagahub-theme'); ?></p>
            </div>
            <a href="<?php echo site_url('/tender'); ?>" class="vh-section-link"><?php _e('Lihat Semua Tender →', 'niagahub-theme'); ?></a>
        </div>

        <div class="vh-tender-list" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 0 1.5rem;">
            <?php
            $tenders = new WP_Query(array(
                'post_type'      => 'vh_tender',
                'posts_per_page' => 3
            ));

            if ($tenders->have_posts()) : while ($tenders->have_posts()) : $tenders->the_post();
                if ( defined('VENDORHUB_PATH') ) {
                    include VENDORHUB_PATH . 'templates/marketplace/tender-card.php';
                }
            endwhile; wp_reset_postdata(); else : ?>
                <div class="vh-empty-state" style="text-align: center; padding: 3rem; border: 1px dashed #e2e8f0; border-radius: 16px; background: white;">
                    <span class="dashicons dashicons-clipboard" style="font-size: 32px; color: #94a3b8; margin-bottom: 10px;"></span>
                    <p style="color: #64748b;"><?php _e('Belum ada tender terbuka saat ini.', 'niagahub-theme'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
 
<!-- Latest Articles (Blog) -->
<section class="nh-latest-articles" style="padding: 4rem 0; background: white; border-top: 1px solid var(--border-color);">
    <div class="nh-container">
        <div class="vh-section-header">
            <div class="vh-section-titles">
                <h2 class="vh-section-title"><?php _e('Wawasan & Artikel Bisnis', 'niagahub-theme'); ?></h2>
                <p class="vh-section-sub"><?php _e('Informasi terbaru seputar industri dan tips procurement.', 'niagahub-theme'); ?></p>
            </div>
            <a href="<?php echo site_url('/artikel'); ?>" class="vh-section-link"><?php _e('Lihat Semua Artikel →', 'niagahub-theme'); ?></a>
        </div>

        <div class="vh-grid nh-article-grid" style="padding: 0 1.5rem;">
            <?php
            $latest_posts = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 3
            ));

            if ($latest_posts->have_posts()) : while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article class="nh-article-card">
                    <a href="<?php the_permalink(); ?>" class="nh-article-thumb">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large'); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner1.png" alt="Article Thumbnail">
                        <?php endif; ?>
                    </a>
                    <div class="nh-article-content">
                        <div class="nh-article-meta"><?php echo get_the_date(); ?></div>
                        <h3 class="nh-article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="nh-article-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        <a href="<?php the_permalink(); ?>" class="nh-article-more"><?php _e('Baca Selengkapnya', 'niagahub-theme'); ?></a>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p><?php _e('Belum ada artikel terbaru.', 'niagahub-theme'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section Premium -->
<!-- CTA Section Premium -->
<section class="nh-cta-premium">
    <div class="nh-container">
        <div class="nh-cta-card">
            
            <!-- Pattern Overlay (Business Grid) -->
            <div style="position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 30px 30px; opacity: 0.5;"></div>

            <!-- Business Ornaments (Floating Icons) -->
            <span class="dashicons dashicons-chart-bar cta-ornament-1"></span>
            <span class="dashicons dashicons-businessman cta-ornament-2"></span>
            <span class="dashicons dashicons-awards cta-ornament-3"></span>
            <span class="dashicons dashicons-building cta-ornament-4"></span>

            <!-- Decorative Glows -->
            <div class="cta-glow-1"></div>
            <div class="cta-glow-2"></div>

            <div class="nh-cta-text">
                <h2><?php _e('Siap Mengembangkan Bisnis B2B Anda?', 'niagahub-theme'); ?></h2>
                <p><?php _e('Bergabunglah dengan ribuan perusahaan terverifikasi di NiagaHUB. Mulai perluas relasi bisnis Anda sekarang juga.', 'niagahub-theme'); ?></p>
                <div class="nh-cta-btns">
                    <a href="<?php echo site_url('/auth'); ?>" class="vh-btn vh-btn-action">
                        <?php _e('Gabung Jadi Vendor', 'niagahub-theme'); ?>
                    </a>
                    <a href="<?php echo site_url('/marketplace-produk'); ?>" class="vh-btn btn-outline-white">
                        <?php _e('Jelajahi Marketplace', 'niagahub-theme'); ?>
                    </a>
                </div>
            </div>

            <div class="nh-cta-stats">
                <div class="cta-stat-item">
                    <div class="stat-num">5.000+</div>
                    <div class="stat-label"><?php _e('Vendor Terdaftar', 'niagahub-theme'); ?></div>
                </div>
                <div class="cta-stat-item">
                    <div class="stat-num">10rb+</div>
                    <div class="stat-label"><?php _e('Produk Premium', 'niagahub-theme'); ?></div>
                </div>
                <div class="cta-stat-item">
                    <div class="stat-num">Rp 500M+</div>
                    <div class="stat-label"><?php _e('Nilai Transaksi', 'niagahub-theme'); ?></div>
                </div>
                <div class="cta-stat-item">
                    <div class="stat-num">24/7</div>
                    <div class="stat-label"><?php _e('Support Bisnis', 'niagahub-theme'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.nh-cta-premium { padding: 6rem 0; background: white; }
.nh-cta-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 32px;
    padding: 4rem;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 3rem;
    color: white;
}

/* Ornaments */
.cta-ornament-1 { position: absolute; top: 10%; left: 5%; font-size: 100px; width: 100px; height: 100px; opacity: 0.03; color: white; transform: rotate(-15deg); pointer-events: none; }
.cta-ornament-2 { position: absolute; bottom: 10%; right: 40%; font-size: 80px; width: 80px; height: 80px; opacity: 0.04; color: white; transform: rotate(10deg); pointer-events: none; }
.cta-ornament-3 { position: absolute; top: -20px; right: 20%; font-size: 120px; width: 120px; height: 120px; opacity: 0.02; color: white; pointer-events: none; }
.cta-ornament-4 { position: absolute; bottom: -30px; left: 20%; font-size: 140px; width: 140px; height: 140px; opacity: 0.03; color: white; pointer-events: none; }

/* Glows */
.cta-glow-1 { position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 128, 0, 0.2) 0%, transparent 70%); border-radius: 50%; filter: blur(40px); }
.cta-glow-2 { position: absolute; bottom: -50px; left: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%); border-radius: 50%; filter: blur(30px); }

.nh-cta-text { position: relative; z-index: 2; max-width: 600px; text-align: left; }
.nh-cta-text h2 { color: white; font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.2; }
.nh-cta-text p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 2.5rem; line-height: 1.6; }
.nh-cta-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
.btn-outline-white { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); }

.nh-cta-stats { position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
.cta-stat-item { text-align: center; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(5px); }
.stat-num { font-size: 2rem; font-weight: 800; color: var(--primary-color); }
.stat-label { font-size: 14px; opacity: 0.7; }

@media (max-width: 1024px) {
    .nh-cta-card { padding: 3rem; }
}

@media (max-width: 992px) {
    .nh-cta-premium { padding: 4rem 0; }
    .nh-cta-card { flex-direction: column; text-align: center; padding: 3rem 2rem; gap: 2.5rem; }
    .nh-cta-text { max-width: 100% !important; text-align: center; }
    .nh-cta-text h2 { font-size: 2rem !important; }
    .nh-cta-btns { justify-content: center; }
    .nh-cta-stats { width: 100%; }
}

@media (max-width: 768px) {
    .nh-cta-premium { padding: 3rem 0; }
    .nh-cta-card { border-radius: 24px; padding: 2.5rem 1.5rem; }
    .nh-cta-text h2 { font-size: 1.75rem !important; }
    .nh-cta-text p { font-size: 1rem !important; margin-bottom: 2rem !important; }
    .nh-cta-btns { flex-direction: column; gap: 0.8rem; }
    .nh-cta-btns .vh-btn { width: 100%; justify-content: center; padding: 12px !important; }
    .nh-cta-stats { gap: 1rem !important; }
    .cta-stat-item { padding: 1.25rem 1rem !important; border-radius: 16px !important; }
    .stat-num { font-size: 1.5rem !important; }
    .nh-cta-premium span.dashicons { display: none; } /* Hide heavy ornaments on mobile */
}

@media (max-width: 480px) {
    .nh-cta-card { padding: 2rem 1.25rem; }
    .nh-cta-text h2 { font-size: 1.5rem !important; }
    .nh-cta-stats { grid-template-columns: 1fr !important; } /* Single column for very small phones */
}
</style>

<?php get_footer(); ?>
