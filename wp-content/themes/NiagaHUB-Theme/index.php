<?php get_header(); ?>

<main class="nh-main-content nh-container" style="padding: 2rem 1.5rem;">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
