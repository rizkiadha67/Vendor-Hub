<?php
/**
 * Template Name: Artikel Listing
 */

get_header(); ?>

<?php
// Banner Header
$bg_url = function_exists('vh_get_industry_bg') ? vh_get_industry_bg('default') : '';
?>
<div class="nh-marketplace-header" style="background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.85)), url('<?php echo esc_url($bg_url); ?>') no-repeat center center; background-size: cover; padding: 4rem 0; color: white;">
    <div class="nh-container">
        <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs('light'); ?>
        <div style="max-width: 800px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: white; line-height: 1.2;"><?php _e('Berita & Wawasan Artikel', 'niagahub-theme'); ?></h1>
            <p style="font-size: 1.1rem; opacity: 0.9; margin: 0; line-height: 1.6;"><?php _e('Temukan panduan, tren industri, dan tips bisnis terbaru untuk mengoptimalkan procurement Anda.', 'niagahub-theme'); ?></p>
        </div>
    </div>
</div>

<div class="nh-container" style="padding: 4rem 1.5rem;">
    <div class="vh-marketplace-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 3rem;">
        
        <!-- Sidebar Filters (Categories) -->
        <aside class="vh-sidebar">
            <style>
                .vh-filter-group h3 {
                    font-size: 0.9rem !important;
                    font-weight: 800 !important;
                    color: #1e293b;
                    margin-bottom: 1.25rem !important;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .vh-filter-group h3::after {
                    content: '';
                    flex: 1;
                    height: 1px;
                    background: #f1f5f9;
                }
                .vh-cat-list { list-style: none; padding: 0; margin: 0; }
                .vh-cat-item { margin-bottom: 0.5rem; }
                .vh-cat-link {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 15px;
                    background: white;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    font-size: 14px;
                    color: #475569;
                    transition: all 0.2s;
                }
                .vh-cat-link:hover {
                    border-color: var(--primary);
                    color: var(--primary);
                    background: #fff8f0;
                    transform: translateX(5px);
                }
                .vh-cat-count {
                    font-size: 11px;
                    font-weight: 700;
                    background: #f1f5f9;
                    color: #94a3b8;
                    padding: 2px 8px;
                    border-radius: 20px;
                }
            </style>
            
            <div class="vh-filter-group" style="margin-bottom: 2.5rem;">
                <h3><?php _e('Kategori Artikel', 'niagahub-theme'); ?></h3>
                <ul class="vh-cat-list">
                    <li class="vh-cat-item">
                        <a href="<?php echo site_url('/artikel'); ?>" class="vh-cat-link">
                            <span><?php _e('Semua Artikel', 'niagahub-theme'); ?></span>
                        </a>
                    </li>
                    <?php
                    $cats = get_categories();
                    foreach ($cats as $cat) : ?>
                        <li class="vh-cat-item">
                            <a href="<?php echo get_category_link($cat->term_id); ?>" class="vh-cat-link">
                                <span><?php echo $cat->name; ?></span>
                                <span class="vh-cat-count"><?php echo $cat->count; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="vh-filter-group">
                <h3><?php _e('Berlangganan', 'niagahub-theme'); ?></h3>
                <div style="background: var(--dark); color: white; padding: 1.5rem; border-radius: 16px; font-size: 13px;">
                    <p style="margin-bottom: 1rem; opacity: 0.8;"><?php _e('Dapatkan update terbaru seputar industri langsung di email Anda.', 'niagahub-theme'); ?></p>
                    <input type="email" placeholder="Email Anda..." style="width: 100%; padding: 10px; border-radius: 8px; border: none; margin-bottom: 10px;">
                    <button class="vh-btn vh-btn-action" style="width: 100%; border-radius: 8px; font-size: 13px;"><?php _e('Daftar Newsletter', 'niagahub-theme'); ?></button>
                </div>
            </div>
        </aside>

        <!-- Article Grid Column -->
        <main class="vh-main-marketplace">
            <div class="vh-grid nh-article-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $articles_query = new WP_Query(array(
                    'post_type'      => 'post',
                    'posts_per_page' => 8,
                    'paged'          => $paged
                ));

                if ($articles_query->have_posts()) : while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                    <article class="nh-article-card">
                        <a href="<?php the_permalink(); ?>" class="nh-article-thumb">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <div style="width: 100%; height: 100%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                     <span class="dashicons dashicons-welcome-write-blog" style="font-size: 40px; width: 40px; height: 40px;"></span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="nh-article-content">
                            <div class="nh-article-meta"><?php echo get_the_date(); ?> &nbsp;&bull;&nbsp; <?php the_author(); ?></div>
                            <h3 class="nh-article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="nh-article-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                            <a href="<?php the_permalink(); ?>" class="nh-article-more"><?php _e('Baca Selengkapnya', 'niagahub-theme'); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="vh-pagination">
                <?php
                echo paginate_links(array(
                    'total'   => $articles_query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => __('&laquo; Prev', 'niagahub-theme'),
                    'next_text' => __('Next &raquo;', 'niagahub-theme'),
                ));
                ?>
            </div>

            <?php wp_reset_postdata(); else : ?>
                <p><?php _e('Belum ada artikel yang diterbitkan.', 'niagahub-theme'); ?></p>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
@media (max-width: 992px) {
    .vh-marketplace-layout { grid-template-columns: 1fr !important; gap: 2rem !important; }
    .vh-sidebar { order: 2; }
    .vh-main-marketplace { order: 1; }
}
</style>

<?php get_footer(); ?>
