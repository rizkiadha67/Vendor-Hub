<?php
/**
 * Custom Search Form - NiagaHUB
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="nh-search-container">
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Cari produk atau vendor...', 'placeholder', 'niagahub-theme' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <button type="submit" class="nh-search-btn">
            <span class="dashicons dashicons-search"></span>
        </button>
    </div>
</form>
