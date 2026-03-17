<?php
/**
 * Search Results Template - NiagaHUB
 */

get_header(); ?>

<div class="nh-container" style="padding: 3rem 1.5rem;">
    <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
    <header class="search-header" style="margin-bottom: 3rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
        <h1 style="font-size: 2.2rem;">
            <?php printf( esc_html__( 'Hasil Pencarian: %s', 'niagahub-theme' ), '<span style="color: var(--primary-color);">"' . get_search_query() . '"</span>' ); ?>
        </h1>
        <p class="text-muted"><?php printf( _n( 'Kami menemukan %d hasil yang cocok.', 'Kami menemukan %d hasil yang cocok.', $wp_query->found_posts, 'niagahub-theme' ), $wp_query->found_posts ); ?></p>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="vh-search-results">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="vh-card" style="margin-bottom: 2rem; flex-direction: row; gap: 2rem; align-items: flex-start;">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail" style="width: 200px; flex-shrink: 0; aspect-ratio: 1/1; overflow: hidden; border-radius: 8px;">
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                        </div>
                    <?php endif; ?>

                    <div class="search-item-content" style="flex: 1;">
                        <span class="vh-badge" style="background: #e2e8f0; color: #475569; font-size: 10px; margin-bottom: 10px; display: inline-block;">
                            <?php 
                                $post_type = get_post_type_object( get_post_type() );
                                echo esc_html( $post_type->labels->singular_name );
                            ?>
                        </span>
                        <h2 style="font-size: 1.4rem; margin: 0 0 10px 0;">
                            <a href="<?php the_permalink(); ?>" style="color: var(--secondary-color);"><?php the_title(); ?></a>
                        </h2>
                        <div class="text-muted" style="margin-bottom: 1.5rem;">
                            <?php echo wp_trim_words( get_the_excerpt(), 30 ); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="btn-dashboard" style="font-size: 14px; color: var(--primary-color); font-weight: 700;">
                            <?php _e('Lihat Detail →', 'niagahub-theme'); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="vh-pagination" style="margin-top: 4rem;">
            <?php the_posts_pagination(); ?>
        </div>

    <?php else : ?>
        <div class="vh-card" style="text-align: center; padding: 6rem 2rem; background: #fff;">
            <div style="background: #f1f5f9; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                <span class="dashicons dashicons-search" style="font-size: 40px; width: 40px; height: 40px; color: #94a3b8;"></span>
            </div>
            <h2><?php _e('Tidak ada hasil yang ditemukan', 'niagahub-theme'); ?></h2>
            <p class="text-muted"><?php _e('Maaf, kami tidak menemukan konten yang sesuai dengan kata kunci pencarian Anda. Silakan coba kata kunci lain.', 'niagahub-theme'); ?></p>
            
            <div style="max-width: 500px; margin: 2rem auto;">
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="nh-search-container">
                        <input type="search" placeholder="<?php _e('Coba cari yang lain...', 'niagahub-theme'); ?>" name="s" value="<?php echo get_search_query(); ?>" style="width: 100%;">
                        <button type="submit" class="nh-search-btn"><span class="dashicons dashicons-search"></span></button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
