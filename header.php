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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        <div class="header-container container">
            <div class="logo-header-container">
                <div class="site-branding">
                    <?php
                    the_custom_logo();
                    if ( is_front_page() && is_home() ) :
                        ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                        <?php
                    else :
                        ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                        <?php
                    endif;
                    $news_portal_description = get_bloginfo( 'description', 'display' );
                    if ( $news_portal_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $news_portal_description; // phpcs:ignore. ?></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->
                
                <div class="header-mobile-buttons">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                        <span><?php esc_html_e( 'Menu', 'news-portal' ); ?></span>
                    </button>
                    <button class="search-toggle" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle search', 'news-portal'); ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="navi-menu-container">
                <nav id="site-navigation" class="main-navigation">
                    <div class="menu-container">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'menu-1',
                                'menu_id'        => 'primary-menu',
                                'container' => 'div',
                                'container_class' => 'navi',
                            )
                        );
                        ?>
                    </div>
                </nav><!-- #site-navigation -->
                
                <div class="header-actions">
                    <button class="search-toggle" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle search', 'news-portal'); ?>">
                        <i class="fas fa-search"></i>
                    </button>

                    <div class="theme-switch-wrapper">
                        <button id="theme-switch" class="theme-switch" aria-label="<?php esc_attr_e('Switch theme', 'news-portal'); ?>">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="header-search-form">
                <?php get_search_form(); ?>
            </div>
        </div>
    </header><!-- #masthead -->

    <div id="content" class="site-content">
        <div class="container">
            <?php if (!is_front_page()) : ?>
                <?php news_portal_breadcrumb(); ?>
            <?php endif; ?>