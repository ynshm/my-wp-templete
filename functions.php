
<?php
/**
 * Theme functions and definitions
 */

if (!function_exists('custom_theme_setup')) :
  /**
   * Sets up theme defaults and registers support for various WordPress features.
   */
  function custom_theme_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
      array(
        'menu-1' => esc_html__('Primary', 'custom-theme'),
      )
    );

    // Switch default core markup to output valid HTML5.
    add_theme_support(
      'html5',
      array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
      )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
      'custom-background',
      apply_filters(
        'custom_theme_custom_background_args',
        array(
          'default-color' => 'ffffff',
          'default-image' => '',
        )
      )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo.
    add_theme_support(
      'custom-logo',
      array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
      )
    );
  }
endif;
add_action('after_setup_theme', 'custom_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function custom_theme_content_width() {
  $GLOBALS['content_width'] = apply_filters('custom_theme_content_width', 640);
}
add_action('after_setup_theme', 'custom_theme_content_width', 0);

/**
 * Register widget area.
 */
function custom_theme_widgets_init() {
  register_sidebar(
    array(
      'name'          => esc_html__('Sidebar', 'custom-theme'),
      'id'            => 'sidebar-1',
      'description'   => esc_html__('Add widgets here.', 'custom-theme'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
}
add_action('widgets_init', 'custom_theme_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function custom_theme_scripts() {
  wp_enqueue_style('custom-theme-style', get_stylesheet_uri(), array(), '1.0.0');
  wp_enqueue_script('custom-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true);

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
add_action('wp_enqueue_scripts', 'custom_theme_scripts');
