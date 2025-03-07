<?php
/**
 * ニュース専用サイトマップの生成
 *
 * @package News_Portal
 */

/**
 * ニュースサイトマップの作成
 */
function news_portal_generate_news_sitemap() {
    // ニュースサイトマップのエンドポイントを登録
    add_rewrite_rule( 'news-sitemap\.xml$', 'index.php?news_sitemap=1', 'top' );
}
add_action( 'init', 'news_portal_generate_news_sitemap' );

/**
 * クエリ変数の登録
 */
function news_portal_add_news_sitemap_query_var( $vars ) {
    $vars[] = 'news_sitemap';
    return $vars;
}
add_filter( 'query_vars', 'news_portal_add_news_sitemap_query_var' );

/**
 * ニュースサイトマップ出力
 */
function news_portal_display_news_sitemap() {
    global $wp_query;

    if ( isset( $wp_query->query_vars['news_sitemap'] ) && '1' === $wp_query->query_vars['news_sitemap'] ) {
        header( 'Content-Type: application/xml; charset=UTF-8' );
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

        // 名前空間の定義
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
               xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        // 過去2日間のニュース記事を取得
        $args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 1000, // 最大1000件まで
            'date_query'     => array(
                array(
                    'after'  => '2 days ago',
                ),
            ),
        );

        $news_posts = new WP_Query( $args );

        if ( $news_posts->have_posts() ) {
            while ( $news_posts->have_posts() ) {
                $news_posts->the_post();
                $post_categories = get_the_category();
                $category_name   = '';

                if ( ! empty( $post_categories ) ) {
                    $category_name = $post_categories[0]->name;
                }

                echo '<url>' . "\n";
                echo '  <loc>' . get_permalink() . '</loc>' . "\n";
                echo '  <news:news>' . "\n";
                echo '    <news:publication>' . "\n";
                echo '      <news:name>' . esc_html( get_bloginfo( 'name' ) ) . '</news:name>' . "\n";
                echo '      <news:language>' . esc_html( get_locale() ) . '</news:language>' . "\n";
                echo '    </news:publication>' . "\n";
                echo '    <news:publication_date>' . get_the_date( 'c' ) . '</news:publication_date>' . "\n";
                echo '    <news:title>' . esc_html( get_the_title() ) . '</news:title>' . "\n";

                if ( ! empty( $category_name ) ) {
                    echo '    <news:keywords>' . esc_html( $category_name ) . '</news:keywords>' . "\n";
                }

                echo '  </news:news>' . "\n";
                echo '</url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';
        exit;
    }
}
add_action( 'template_redirect', 'news_portal_display_news_sitemap' );

/**
 * ニュースサイトマップのURLをサイトマップインデックスに追加
 */
function news_portal_add_news_sitemap_to_index( $sitemap_urls ) {
    $sitemap_urls[] = array(
        'loc'     => home_url( '/news-sitemap.xml' ),
        'lastmod' => date( 'c' ),
    );
    return $sitemap_urls;
}
// WordPress 5.5以降のサイトマップ機能と統合する場合は以下を有効化
// add_filter( 'wp_sitemaps_index_entry', 'news_portal_add_news_sitemap_to_index' );