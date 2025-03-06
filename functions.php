
<?php
/**
 * Theme functions and definitions
 */

if (!function_exists('custom_theme_setup')) :
  /**
   * Sets up theme defaults and registers support for various WordPress features.
   */
  function custom_theme_setup() {
    // テキストドメインの読み込み
    load_theme_textdomain('news-portal', get_template_directory() . '/languages');
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in multiple locations.
    register_nav_menus(
      array(
        'menu-1' => esc_html__('Primary', 'news-portal'),
        'footer-menu' => esc_html__('Footer Menu', 'news-portal'),
        'social-menu' => esc_html__('Social Links', 'news-portal'),
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
      'name'          => esc_html__('Sidebar', 'news-portal'),
      'id'            => 'sidebar-1',
      'description'   => esc_html__('Add widgets here.', 'news-portal'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
  
  // フッターウィジェットエリア
  register_sidebar(
    array(
      'name'          => esc_html__('Footer 1', 'news-portal'),
      'id'            => 'footer-1',
      'description'   => esc_html__('First footer widget area', 'news-portal'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
  
  register_sidebar(
    array(
      'name'          => esc_html__('Footer 2', 'news-portal'),
      'id'            => 'footer-2',
      'description'   => esc_html__('Second footer widget area', 'news-portal'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
  
  register_sidebar(
    array(
      'name'          => esc_html__('Footer 3', 'news-portal'),
      'id'            => 'footer-3',
      'description'   => esc_html__('Third footer widget area', 'news-portal'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
  
  // SEO対策としてのホームページウィジェットエリア
  register_sidebar(
    array(
      'name'          => esc_html__('Homepage Top', 'news-portal'),
      'id'            => 'homepage-top',
      'description'   => esc_html__('Appears at the top of the homepage', 'news-portal'),
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
  wp_enqueue_style('news-portal-style', get_stylesheet_uri(), array(), '1.0.0');
  wp_enqueue_script('news-portal-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true);

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
add_action('wp_enqueue_scripts', 'custom_theme_scripts');

/**
 * パフォーマンス向上のためのスクリプト読み込み最適化
 */
function news_portal_optimize_scripts() {
    // jQueryをフッターに移動
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), false, null, true);
        wp_enqueue_script('jquery');
    }
    
    // Google Fontsのプリロード
    add_filter('style_loader_tag', 'news_portal_preload_fonts', 10, 2);
    
    // モダンなJavaScriptの追加
    wp_enqueue_script('news-portal-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.1', true);
    
    // 無限スクロール用JS
    if (get_theme_mod('enable_infinite_scroll', false) && (is_home() || is_archive() || is_search())) {
        wp_enqueue_script('news-portal-infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', array('jquery'), '1.0.0', true);
        
        // 無限スクロール用の設定をJSに渡す
        wp_localize_script('news-portal-infinite-scroll', 'newsPortalInfiniteScroll', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('news-portal-infinite-scroll-nonce'),
            'posts_container' => '#posts-container',
            'post_selector' => '.news-article',
            'pagination' => '.pagination',
            'next_selector' => '.pagination .next',
            'loading_text' => __('Loading...', 'news-portal'),
            'finished_text' => __('No more posts to load', 'news-portal'),
        ));
    }
    
    // 画像遅延読み込み
    wp_enqueue_script('news-portal-lazy-load', get_template_directory_uri() . '/js/lazy-load.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'news_portal_optimize_scripts', 1);

function news_portal_preload_fonts($html, $handle) {
    if (strpos($handle, 'google-fonts') !== false) {
        return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
    }
    return $html;
}

/**
 * クリティカルCSSの追加
 */
function news_portal_critical_css() {
    ob_start();
    ?>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #f97316;
            --text-color: #374151;
            --bg-color: #f9fafb;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
        }
        body {
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            background-color: var(--bg-color);
        }
        .site-header {
            background-color: var(--bg-card);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .site-content {
            padding: 2rem 0 4rem;
        }
    </style>
    <?php
    $critical_css = ob_get_clean();
    echo $critical_css;
}
add_action('wp_head', 'news_portal_critical_css', 1);

/**
 * リソースのプリロード
 */
function news_portal_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        // Google Fontsへの接続を事前に確立
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin',
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
        // Font Awesomeへの接続を事前に確立
        $urls[] = array(
            'href' => 'https://cdnjs.cloudflare.com',
            'crossorigin',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'news_portal_resource_hints', 10, 2);

/**
 * 拡張された構造化データの追加（LLM対応）
 */
function news_portal_structured_data() {
    if (is_single()) {
        global $post;
        $author_id = $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id);
        $content = get_the_content();
        
        // 自動要約の生成（最初の150文字を取得し、文章の区切りで終わるように調整）
        $auto_summary = wp_trim_words(strip_tags($content), 30, '...');
        
        // キーワード抽出（タグや特定の単語をベースに）
        $keywords = array();
        $tags = get_the_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
        }
        
        // ブログ記事の拡張構造化データ（LLM対応）
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title(),
            'description' => get_the_excerpt(),
            'abstract' => $auto_summary, // LLM用の要約
            'keywords' => implode(', ', $keywords), // LLM用のキーワード
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name,
                'url' => get_author_posts_url($author_id)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            ),
            'mainEntityOfPage' => get_permalink(),
            'articleBody' => strip_tags($content) // LLMが本文を理解しやすいように
        );
        
        // コンテンツの信頼性データ
        $schema['contentRating'] = 'G';
        
        // 引用情報の追加（もし引用があれば）
        $citations = news_portal_extract_citations($content);
        if (!empty($citations)) {
            $schema['citation'] = $citations;
        }
        
        // アイキャッチ画像がある場合
        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_url = wp_get_attachment_image_src($image_id, 'full');
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $image_url[0],
                'width' => $image_url[1],
                'height' => $image_url[2],
                'caption' => get_post_meta($image_id, '_wp_attachment_image_alt', true) // 代替テキストをキャプションとして使用
            );
        }
        
        // 見出し構造を抽出してLLMに提供
        $headings = news_portal_extract_headings($content);
        if (!empty($headings)) {
            $schema['articleSection'] = $headings;
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
        
        // ハイライトデータのメタ情報（OGPタグ拡張）
        $highlights = news_portal_generate_highlights($content);
        if (!empty($highlights)) {
            echo '<meta property="og:highlights" content="' . esc_attr(implode(' | ', $highlights)) . '" />';
        }
        
    } elseif (is_home() || is_front_page()) {
        // 拡張されたWebサイトの構造化データ
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ),
            // サイト全体の知識グラフ連携データ
            'about' => array(
                '@type' => 'Thing',
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'url' => home_url('/about/') // 運営者情報ページへのリンク
            )
        );
        
        // カテゴリー一覧を知識グラフとして提供
        $categories = get_categories(array('hide_empty' => false));
        if (!empty($categories)) {
            $knowledge_graph = array();
            foreach ($categories as $category) {
                $knowledge_graph[] = array(
                    '@type' => 'Thing',
                    'name' => $category->name,
                    'description' => $category->description,
                    'url' => get_category_link($category->term_id)
                );
            }
            $schema['knowsAbout'] = $knowledge_graph;
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}

/**
 * コンテンツから引用情報を抽出
 */
function news_portal_extract_citations($content) {
    $citations = array();
    
    // blockquoteタグから引用を探す
    preg_match_all('/<blockquote.*?>(.*?)<\/blockquote>/s', $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $citation) {
            $citations[] = array(
                '@type' => 'CreativeWork',
                'text' => strip_tags($citation)
            );
        }
    }
    
    return $citations;
}

/**
 * コンテンツから見出し構造を抽出
 */
function news_portal_extract_headings($content) {
    $headings = array();
    
    // h2, h3タグから見出しを探す
    preg_match_all('/<h([2-3]).*?>(.*?)<\/h[2-3]>/s', $content, $matches, PREG_SET_ORDER);
    
    if (!empty($matches)) {
        foreach ($matches as $match) {
            $level = $match[1];
            $text = strip_tags($match[2]);
            
            $headings[] = array(
                'level' => $level,
                'text' => $text
            );
        }
    }
    
    return $headings;
}

/**
 * コンテンツからハイライトを生成
 */
function news_portal_generate_highlights($content) {
    $highlights = array();
    
    // 記事の重要ポイントを抽出（強調タグや箇条書きから）
    preg_match_all('/<(strong|em|b|i|li).*?>(.*?)<\/(strong|em|b|i|li)>/s', $content, $matches, PREG_SET_ORDER);
    
    $count = 0;
    if (!empty($matches)) {
        foreach ($matches as $match) {
            if ($count >= 5) break; // 最大5つまで
            
            $text = strip_tags($match[2]);
            // 短すぎる強調は除外
            if (strlen($text) > 15) {
                $highlights[] = $text;
                $count++;
            }
        }
    }
    
    return $highlights;
}
add_action('wp_head', 'news_portal_structured_data', 10);

/**
 * OGPメタタグを追加
 */
function news_portal_add_ogp_meta() {
    global $post;
    
    if (is_singular()) {
        // タイトル
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
        // 種類
        echo '<meta property="og:type" content="article" />' . "\n";
        // URL
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
        // サイト名
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        // 説明
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags(get_the_excerpt())) . '" />' . "\n";
        
        // アイキャッチ画像
        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_url = wp_get_attachment_image_src($image_id, 'full');
            echo '<meta property="og:image" content="' . esc_url($image_url[0]) . '" />' . "\n";
        }
    } else {
        // タイトル
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        // 種類
        echo '<meta property="og:type" content="website" />' . "\n";
        // URL
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '" />' . "\n";
        // サイト名
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        // 説明
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '" />' . "\n";
    }
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
}
add_action('wp_head', 'news_portal_add_ogp_meta');

/**
 * デフォルトメニューを表示する関数
 */
function news_portal_default_menu() {
    echo '<ul id="primary-menu" class="menu">';
    echo '<li class="menu-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'news-portal') . '</a></li>';
    echo '</ul>';
}

/**
 * テキストドメインを変更
 */
function news_portal_text_domain() {
    load_theme_textdomain('news-portal', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'news_portal_text_domain');

/**
 * 追加機能モジュールの読み込み
 */
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/security-functions.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/api-functions.php';
require_once get_template_directory() . '/inc/llm-functions.php';

/**
 * コードの最適化とクリーン化
 */
function news_portal_clean_head() {
    // 不要なWPリソースを削除
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
}
add_action('init', 'news_portal_clean_head');

/**
 * サムネイル表示関数
 */
function news_portal_post_thumbnail() {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php else : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'medium_large',
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                        'class' => 'featured-image',
                        'loading' => 'lazy',
                    )
                );
                ?>
            </a>
        </div>
    <?php
    endif;
}

/**
 * ブレッドクラム表示関数
 */
function news_portal_breadcrumb() {
    // パンくずリストのマークアップを開始
    echo '<div class="breadcrumbs-container" itemscope itemtype="http://schema.org/BreadcrumbList">';
    
    // ホームへのリンク
    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    echo '<a href="' . esc_url(home_url('/')) . '" itemprop="item"><span itemprop="name">' . esc_html__('Home', 'news-portal') . '</span></a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</span>';
    
    // 現在のページ種別に応じた処理
    if (is_category() || is_single()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        
        // 投稿の場合はカテゴリを表示
        if (is_single()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" itemprop="item"><span itemprop="name">' . esc_html($categories[0]->name) . '</span></a>';
                echo '<meta itemprop="position" content="2" />';
                echo '</span>';
                echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
                
                // 現在の投稿タイトル
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<span itemprop="name">' . get_the_title() . '</span>';
                echo '<meta itemprop="position" content="3" />';
                echo '</span>';
            }
        } else {
            // カテゴリーアーカイブの場合
            $cat = get_queried_object();
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<span itemprop="name">' . esc_html($cat->name) . '</span>';
            echo '<meta itemprop="position" content="2" />';
            echo '</span>';
        }
    } elseif (is_page()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        
        // 親ページがある場合は階層を表示
        if ($post->post_parent) {
            $parents = get_post_ancestors($post->ID);
            $parents = array_reverse($parents);
            $position = 2;
            
            foreach ($parents as $parent_id) {
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a href="' . esc_url(get_permalink($parent_id)) . '" itemprop="item"><span itemprop="name">' . get_the_title($parent_id) . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</span>';
                echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
                $position++;
            }
            
            // 現在のページ
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<span itemprop="name">' . get_the_title() . '</span>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</span>';
        } else {
            // 親ページがない場合
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<span itemprop="name">' . get_the_title() . '</span>';
            echo '<meta itemprop="position" content="2" />';
            echo '</span>';
        }
    } elseif (is_search()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html__('Search Results for: ', 'news-portal') . get_search_query() . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</span>';
    } elseif (is_tag()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . single_tag_title('', false) . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</span>';
    } elseif (is_author()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . get_the_author() . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</span>';
    } elseif (is_archive()) {
        echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . get_the_archive_title() . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</span>';
    }
    
    echo '</div>';
}

/**
 * フロントページ用のフィーチャードスライダー
 */
function news_portal_featured_slider() {
    $slider_category = get_theme_mod('homepage_featured_category', '');
    
    if (empty($slider_category)) {
        // カテゴリーが設定されていない場合は最新記事を表示
        $slider_args = array(
            'posts_per_page' => 5,
            'post_type' => 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
        );
    } else {
        // 特定のカテゴリーの記事を表示
        $slider_args = array(
            'posts_per_page' => 5,
            'cat' => absint($slider_category),
            'post_type' => 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
        );
    }
    
    $slider_query = new WP_Query($slider_args);
    
    if ($slider_query->have_posts()) :
    ?>
    <div class="featured-slider">
        <div class="slider-wrapper">
            <?php while ($slider_query->have_posts()) : $slider_query->the_post(); ?>
            <div class="slider-item">
                <?php if (has_post_thumbnail()) : ?>
                <div class="slider-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
                <?php endif; ?>
                <div class="slider-content">
                    <?php
                    // カテゴリー表示
                    $categories = get_the_category();
                    if (!empty($categories)) :
                        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="slider-category">' . esc_html($categories[0]->name) . '</a>';
                    endif;
                    ?>
                    <h2 class="slider-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="slider-meta">
                        <span class="slider-date"><i class="far fa-calendar-alt" aria-hidden="true"></i> <?php echo get_the_date(); ?></span>
                    </div>
                    <div class="slider-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></div>
                    <a href="<?php the_permalink(); ?>" class="slider-readmore"><?php esc_html_e('Read More', 'news-portal'); ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="slider-nav">
            <button class="slider-prev" aria-label="<?php esc_attr_e('Previous slide', 'news-portal'); ?>"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
            <div class="slider-dots"></div>
            <button class="slider-next" aria-label="<?php esc_attr_e('Next slide', 'news-portal'); ?>"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
        </div>
    </div>
    <?php
    endif;
    wp_reset_postdata();
}

/**
 * 関連記事表示関数
 */
function news_portal_related_posts() {
    global $post;
    
    // 現在の投稿のカテゴリーを取得
    $categories = get_the_category($post->ID);
    if (empty($categories)) {
        return;
    }
    
    $category_ids = array();
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }
    
    // 関連記事のクエリを設定
    $args = array(
        'category__in' => $category_ids,
        'post__not_in' => array($post->ID),
        'posts_per_page' => 3,
        'orderby' => 'rand',
    );
    
    $related_query = new WP_Query($args);
    
    if ($related_query->have_posts()) :
    ?>
    <div class="related-posts">
        <h3 class="related-title"><?php esc_html_e('Related Posts', 'news-portal'); ?></h3>
        <div class="related-posts-container">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
            <article class="related-post">
                <?php if (has_post_thumbnail()) : ?>
                <div class="related-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                    </a>
                </div>
                <?php endif; ?>
                <h4 class="related-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
                <div class="related-date">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo get_the_date(); ?>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    endif;
    wp_reset_postdata();
}

/**
 * 著者情報ボックス表示関数
 */
function news_portal_author_box() {
    if (!get_the_author_meta('description')) {
        return;
    }
    ?>
    <div class="author-box">
        <div class="author-avatar">
            <?php echo get_avatar(get_the_author_meta('user_email'), 80); ?>
        </div>
        <div class="author-info">
            <h3 class="author-name"><?php the_author_posts_link(); ?></h3>
            <div class="author-description">
                <?php the_author_meta('description'); ?>
            </div>
            <?php if (get_the_author_meta('user_url')) : ?>
            <div class="author-website">
                <a href="<?php echo esc_url(get_the_author_meta('user_url')); ?>" target="_blank" rel="nofollow">
                    <i class="fas fa-globe" aria-hidden="true"></i> <?php esc_html_e('Website', 'news-portal'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * 抜粋の最適化
 */
function news_portal_custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'news_portal_custom_excerpt_length', 999);

function news_portal_custom_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'news_portal_custom_excerpt_more');
