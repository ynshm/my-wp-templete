
<?php
/**
 * News Portal Theme Customizer
 *
 * @package News_Portal
 */

/**
 * カスタマイザーの設定
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function news_portal_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector' => '.site-title a',
                'render_callback' => 'news_portal_customize_partial_blogname',
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector' => '.site-description',
                'render_callback' => 'news_portal_customize_partial_blogdescription',
            )
        );
    }
    
    // ロゴのサイズ調整
    $wp_customize->add_setting('custom_logo_width', array(
        'default' => 250,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('custom_logo_width', array(
        'label' => __('Logo Width (px)', 'news-portal'),
        'section' => 'title_tagline',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 50,
            'max' => 500,
            'step' => 5,
        ),
    ));
    
    // ヘッダー設定セクション
    $wp_customize->add_section('news_portal_header_section', array(
        'title' => __('Header Options', 'news-portal'),
        'priority' => 30,
    ));
    
    // ヘッダースタイルの選択
    $wp_customize->add_setting('header_style', array(
        'default' => 'style1',
        'sanitize_callback' => 'news_portal_sanitize_select',
    ));
    
    $wp_customize->add_control('header_style', array(
        'label' => __('Header Style', 'news-portal'),
        'section' => 'news_portal_header_section',
        'type' => 'select',
        'choices' => array(
            'style1' => __('Style 1 (Default)', 'news-portal'),
            'style2' => __('Style 2 (Centered)', 'news-portal'),
            'style3' => __('Style 3 (With Ad Space)', 'news-portal'),
        ),
    ));
    
    // サイトカラーセクション
    $wp_customize->add_section('news_portal_colors_section', array(
        'title' => __('Site Colors', 'news-portal'),
        'priority' => 40,
    ));
    
    // プライマリーカラー
    $wp_customize->add_setting('primary_color', array(
        'default' => '#0066cc',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Primary Color', 'news-portal'),
        'section' => 'news_portal_colors_section',
    )));
    
    // セカンダリーカラー
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#ff6600',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Secondary Color', 'news-portal'),
        'section' => 'news_portal_colors_section',
    )));
    
    // トップページ設定セクション
    $wp_customize->add_section('news_portal_homepage_section', array(
        'title' => __('Homepage Layout', 'news-portal'),
        'priority' => 50,
    ));
    
    // トップページレイアウト
    $wp_customize->add_setting('homepage_layout', array(
        'default' => 'standard',
        'sanitize_callback' => 'news_portal_sanitize_select',
    ));
    
    $wp_customize->add_control('homepage_layout', array(
        'label' => __('Homepage Layout', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'type' => 'select',
        'choices' => array(
            'standard' => __('Standard Blog Layout', 'news-portal'),
            'grid' => __('Grid Layout', 'news-portal'),
            'magazine' => __('Magazine Layout', 'news-portal'),
        ),
    ));
    
    // フィーチャースライダーの設定
    $wp_customize->add_setting('homepage_featured_slider', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('homepage_featured_slider', array(
        'label' => __('Display Featured Slider', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'type' => 'checkbox',
    ));
    
    // フィーチャースライダーのカテゴリー選択
    $wp_customize->add_setting('homepage_featured_category', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Category_Control($wp_customize, 'homepage_featured_category', array(
        'label' => __('Featured Slider Category', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'description' => __('Select a category for featured slider. Leave empty to display latest posts.', 'news-portal'),
    )));
    
    // 無限スクロールの有効化
    $wp_customize->add_setting('enable_infinite_scroll', array(
        'default' => false,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_infinite_scroll', array(
        'label' => __('Enable Infinite Scroll', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'type' => 'checkbox',
        'description' => __('Enable infinite scroll for blog and archive pages.', 'news-portal'),
    ));
    
    // 投稿一覧レイアウト
    $wp_customize->add_setting('posts_layout_style', array(
        'default' => 'grid',
        'sanitize_callback' => 'news_portal_sanitize_select',
    ));
    
    $wp_customize->add_control('posts_layout_style', array(
        'label' => __('Posts Layout Style', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'type' => 'select',
        'choices' => array(
            'grid' => __('Grid Layout', 'news-portal'),
            'list' => __('List Layout', 'news-portal'),
            'masonry' => __('Masonry Layout', 'news-portal'),
        ),
    ));
    
    // 記事カード表示オプション
    $wp_customize->add_setting('card_style', array(
        'default' => 'shadow',
        'sanitize_callback' => 'news_portal_sanitize_select',
    ));
    
    $wp_customize->add_control('card_style', array(
        'label' => __('Card Style', 'news-portal'),
        'section' => 'news_portal_homepage_section',
        'type' => 'select',
        'choices' => array(
            'shadow' => __('Shadow', 'news-portal'),
            'border' => __('Border', 'news-portal'),
            'flat' => __('Flat', 'news-portal'),
        ),
    ));
    
    // パフォーマンス設定セクション
    $wp_customize->add_section('news_portal_performance_section', array(
        'title' => __('Performance Options', 'news-portal'),
        'priority' => 95,
    ));
    
    // 遅延読み込みの有効化
    $wp_customize->add_setting('enable_lazy_loading', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_lazy_loading', array(
        'label' => __('Enable Lazy Loading for Images', 'news-portal'),
        'section' => 'news_portal_performance_section',
        'type' => 'checkbox',
    ));
    
    // プリロードの有効化
    $wp_customize->add_setting('enable_preload', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_preload', array(
        'label' => __('Enable Resource Preloading', 'news-portal'),
        'section' => 'news_portal_performance_section',
        'type' => 'checkbox',
    ));
    
    // ソーシャルシェアセクション
    $wp_customize->add_section('news_portal_social_section', array(
        'title' => __('Social Sharing', 'news-portal'),
        'priority' => 70,
    ));
    
    // ソーシャルシェアの有効化
    $wp_customize->add_setting('enable_social_share', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_social_share', array(
        'label' => __('Enable Social Sharing', 'news-portal'),
        'section' => 'news_portal_social_section',
        'type' => 'checkbox',
    ));
    
    // 各SNSの有効化
    $social_networks = array(
        'twitter' => __('Twitter', 'news-portal'),
        'facebook' => __('Facebook', 'news-portal'),
        'pinterest' => __('Pinterest', 'news-portal'),
        'linkedin' => __('LinkedIn', 'news-portal'),
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('enable_share_' . $network, array(
            'default' => true,
            'sanitize_callback' => 'news_portal_sanitize_checkbox',
        ));
        
        $wp_customize->add_control('enable_share_' . $network, array(
            'label' => $label,
            'section' => 'news_portal_social_section',
            'type' => 'checkbox',
        ));
    }
    
    // フッター設定セクション
    $wp_customize->add_section('news_portal_footer_section', array(
        'title' => __('Footer Options', 'news-portal'),
        'priority' => 90,
    ));
    
    // フッターウィジェット列数
    $wp_customize->add_setting('footer_widget_columns', array(
        'default' => 3,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('footer_widget_columns', array(
        'label' => __('Footer Widget Columns', 'news-portal'),
        'section' => 'news_portal_footer_section',
        'type' => 'select',
        'choices' => array(
            1 => __('1 Column', 'news-portal'),
            2 => __('2 Columns', 'news-portal'),
            3 => __('3 Columns', 'news-portal'),
            4 => __('4 Columns', 'news-portal'),
        ),
    ));
    
    // コピーライトテキスト
    $wp_customize->add_setting('footer_copyright', array(
        'default' => sprintf(__('Copyright © %s %s. All Rights Reserved.', 'news-portal'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('footer_copyright', array(
        'label' => __('Copyright Text', 'news-portal'),
        'section' => 'news_portal_footer_section',
        'type' => 'textarea',
    ));
    
    // 記事詳細ページ設定セクション
    $wp_customize->add_section('news_portal_single_post_section', array(
        'title' => __('Single Post Options', 'news-portal'),
        'priority' => 60,
    ));
    
    // 投稿者情報の表示
    $wp_customize->add_setting('single_post_author_box', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('single_post_author_box', array(
        'label' => __('Display Author Info Box', 'news-portal'),
        'section' => 'news_portal_single_post_section',
        'type' => 'checkbox',
    ));
    
    // 関連記事の表示
    $wp_customize->add_setting('single_post_related', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('single_post_related', array(
        'label' => __('Display Related Posts', 'news-portal'),
        'section' => 'news_portal_single_post_section',
        'type' => 'checkbox',
    ));
    
    // 投稿ナビゲーションの表示
    $wp_customize->add_setting('single_post_navigation', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('single_post_navigation', array(
        'label' => __('Display Post Navigation', 'news-portal'),
        'section' => 'news_portal_single_post_section',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'news_portal_customize_register');

/**
 * パーシャルリフレッシュのための関数
 */
function news_portal_customize_partial_blogname() {
    bloginfo('name');
}

function news_portal_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * カスタマイザープレビューのためのJS読み込み
 */
function news_portal_customize_preview_js() {
    wp_enqueue_script('news-portal-customizer', get_template_directory_uri() . '/js/customizer.js', array('customize-preview'), _S_VERSION, true);
}
add_action('customize_preview_init', 'news_portal_customize_preview_js');

/**
 * カスタムCSSの出力
 */
function news_portal_customizer_css() {
    $primary_color = get_theme_mod('primary_color', '#0066cc');
    $secondary_color = get_theme_mod('secondary_color', '#ff6600');
    $custom_logo_width = get_theme_mod('custom_logo_width', 250);
    
    $css = "
        :root {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
        }
        
        .site-branding img.custom-logo {
            max-width: {$custom_logo_width}px;
        }
        
        .main-navigation,
        .main-navigation ul ul,
        .pagination .current,
        .page-links .current,
        .btn-primary,
        .widget-title:after,
        .tagcloud a:hover,
        .comment-respond .form-submit input,
        #scroll-up {
            background-color: var(--primary-color);
        }
        
        a:hover,
        a:focus,
        .site-title a:hover,
        .site-title a:focus,
        .main-navigation a:hover,
        .entry-title a:hover,
        .entry-meta a:hover,
        .entry-footer a:hover {
            color: var(--primary-color);
        }
        
        .cta-button,
        .btn-secondary,
        .tags-links a:hover,
        .cat-links a:hover {
            background-color: var(--secondary-color);
        }
        
        blockquote {
            border-left: 4px solid var(--primary-color);
        }
    ";
    
    wp_add_inline_style('news-portal-style', $css);
}
add_action('wp_enqueue_scripts', 'news_portal_customizer_css');
