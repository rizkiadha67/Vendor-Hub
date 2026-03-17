<?php
/**
 * The template for displaying all single pages
 */

get_header(); ?>

<?php
while ( have_posts() ) :
    the_post();
    $is_archive_page = is_page(array('marketplace-produk', 'marketplace-vendor', 'pusat-tender', 'tender', 11));
    $main_classes = $is_archive_page ? 'nh-main-content' : 'nh-main-content nh-container';
    $main_styles  = $is_archive_page ? 'min-height: 60vh;' : 'padding: 2rem 1.5rem 4rem; min-height: 60vh;';
    ?>
    <main class="<?php echo esc_attr($main_classes); ?>" style="<?php echo esc_attr($main_styles); ?>">
        
        <?php if (!is_front_page() && !is_page('dashboard') && !$is_archive_page) : ?>
            <div class="nh-container">
                <?php if ( function_exists('vh_breadcrumbs') ) vh_breadcrumbs(); ?>
            </div>
        <?php endif; ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if (!$is_archive_page) : ?>
            <header class="entry-header" style="text-align: center; margin-bottom: 3rem;">
                <?php the_title( '<h1 class="entry-title" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--vh-text-dark);">', '</h1>' ); ?>
            </header>
            <?php endif; ?>

            <div class="entry-content">
                <?php
                // Force do_shortcode just in case standard filters aren't firing
                echo do_shortcode(get_the_content());
                ?>
            </div>
        </article>
    </main>
    <?php
endwhile; // End of the loop.
?>

<?php get_footer(); ?>
