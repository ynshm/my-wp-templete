
<?php
/**
 * ニュースサイトマップ機能
 *
 * @package News_Portal
 */

/**
 * ニュースサイトマップ用のリライトルールを追加
 */
function news_portal_add_news_sitemap_rewrite_rules() {
    add_rewrite_rule(
        '^news-sitemap\.xml$',
        'index.php?news_sitemap=1',
        'top'
    );
}
add_action('init', 'news_portal_add_news_sitemap_rewrite_rules');

/**
 * クエリ変数を追加
 */
function news_portal_add_news_sitemap_query_vars($vars) {
    $vars[] = 'news_sitemap';
    return $vars;
}
add_filter('query_vars', 'news_portal_add_news_sitemap_query_vars');

/**
 * サイトマップのテンプレートを設定
 */
function news_portal_news_sitemap_template($template) {
    global $wp_query;
    
    if (isset($wp_query->query_vars['news_sitemap']) && '1' === $wp_query->query_vars['news_sitemap']) {
        // 出力をキャッシュしない
        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow', true);
        
        // XMLのヘッダー
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
        
        // 過去2日間のニュース記事を取得
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'date_query' => array(
                array(
                    'after' => '2 days ago',
                ),
            ),
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // 記事のカテゴリーを取得
                $category_name = '';
                $post_categories = get_the_category();
                if (!empty($post_categories)) {
                    $category_name = $post_categories[0]->name;
                }
                
                echo '<url>';
                echo '<loc>' . get_permalink() . '</loc>';
                echo '<news:news>';
                echo '<news:publication>';
                echo '<news:name>' . esc_html(get_bloginfo('name')) . '</news:name>';
                echo '<news:language>' . esc_html(get_locale()) . '</news:language>';
                echo '</news:publication>';
                echo '<news:publication_date>' . get_the_date('c') . '</news:publication_date>';
                echo '<news:title>' . esc_html(get_the_title()) . '</news:title>';
                
                if (!empty($category_name)) {
                    echo '<news:keywords>' . esc_html($category_name) . '</news:keywords>';
                }
                
                echo '</news:news>';
                echo '</url>';
            }
            wp_reset_postdata();
        }
        
        echo '</urlset>';
        exit();
    }
    
    return $template;
}
add_action('template_include', 'news_portal_news_sitemap_template');

/**
 * WordPressのデフォルトサイトマップにニュースサイトマップを追加
 */
function news_portal_add_news_sitemap_to_wp_sitemaps($provider, $name) {
    if ('index' === $name) {
        add_filter('wp_sitemaps_index_entry', function($entry) {
            if ('sitemaps' === $entry['name']) {
                $news_sitemap_url = home_url('/news-sitemap.xml');
                return array(
                    'loc' => $news_sitemap_url,
                );
            }
            return $entry;
        });
    }
    
    return $provider;
}
add_filter('wp_sitemaps_add_provider', 'news_portal_add_news_sitemap_to_wp_sitemaps', 10, 2);
