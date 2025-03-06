
<?php
/**
 * ヘッダーテンプレート
 *
 * @package News_Portal
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (is_singular() && pings_open(get_queried_object())) : ?>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'news-portal' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="container header-container">
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) :
                    the_custom_logo();
                else :
                ?>
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php
                    $news_portal_description = get_bloginfo( 'description', 'display' );
                    if ( $news_portal_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $news_portal_description; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div><!-- .site-branding -->

            <nav id="site-navigation" class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span><?php esc_html_e( 'Menu', 'news-portal' ); ?></span>
                    <span class="menu-icon"></span>
                </button>
                <div class="menu-container">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'fallback_cb'    => 'news_portal_default_menu'
                        )
                    );
                    ?>
                    <?php get_search_form(); ?>
                </div>
            </nav><!-- #site-navigation -->
        </div><!-- .container -->
        
        <?php if (is_front_page() && get_theme_mod('homepage_featured_slider', true)) : ?>
        <div class="featured-slider-container">
            <?php news_portal_featured_slider(); ?>
        </div>
        <?php endif; ?>
    </header><!-- #masthead -->

    <div id="content" class="site-content">
        <div class="container">
            <?php if (!is_front_page()) : ?>
                <?php news_portal_breadcrumb(); ?>
            <?php endif; ?>
