<?php
/**
 * Generic Archive Template - NiagaHUB
 */

get_header(); ?>

<div class="nh-container" style="padding: 3rem 1.5rem;">
    <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
    <header class="archive-header" style="margin-bottom: 3rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<div class="archive-description text-muted">', '</div>'); ?>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="vh-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" class="vh-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail" style="margin-bottom: 1.5rem; aspect-ratio: 16/9; overflow: hidden; border-radius: 8px;">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="entry-header">
                        <span class="text-muted" style="font-size: 12px; text-transform: uppercase;"><?php echo get_the_date(); ?></span>
                        <h2 style="font-size: 1.3rem; margin: 0.5rem 0;">
                            <a href="<?php the_permalink(); ?>" style="color: var(--secondary-color);"><?php the_title(); ?></a>
                        </h2>
                    </div>

                    <div class="entry-content text-muted" style="font-size: 15px; margin-bottom: 1.5rem;">
                        <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="btn-rfq-outline" style="text-align: center; display: block;">
                        <?php _e('Baca Selengkapnya', 'niagahub-theme'); ?>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="vh-pagination" style="margin-top: 4rem;">
            <?php the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '&laquo; Prev', 'niagahub-theme' ),
                'next_text' => __( 'Next &raquo;', 'niagahub-theme' ),
            ) ); ?>
        </div>

    <?php else : ?>
        <div class="vh-card" style="text-align: center; padding: 5rem 2rem;">
            <span class="dashicons dashicons-search" style="font-size: 4rem; width: 4rem; height: 4rem; color: #cbd5e1; margin-bottom: 1.5rem;"></span>
            <h2><?php _e('Tidak Ada Konten Ditemukan', 'niagahub-theme'); ?></h2>
            <p class="text-muted"><?php _e('Maaf, kami tidak dapat menemukan apa yang Anda cari di kategori ini.', 'niagahub-theme'); ?></p>
            <a href="<?php echo home_url(); ?>" class="vh-btn vh-btn-primary" style="margin-top: 2rem;"><?php _e('Kembali ke Beranda', 'niagahub-theme'); ?></a>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
