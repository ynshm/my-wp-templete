
<?php
/**
 * LLM対応機能
 * LLMがコンテンツを理解しやすくするための機能
 *
 * @package News_Portal
 */

/**
 * LLMのためのJSON-LDを追加（投稿内容を構造化）
 */
function news_portal_add_llm_jsonld() {
    if (!is_singular()) {
        return;
    }
    
    global $post;
    $post_id = $post->ID;
    $post_type = get_post_type();
    
    // LLM用のJSONデータを生成
    $llm_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name'),
        ),
    );
    
    // 投稿タイプに応じて追加情報
    switch ($post_type) {
        case 'knowledge':
            $llm_data['@type'] = 'TechArticle';
            $llm_data['llmOptimizedSummary'] = get_post_meta($post_id, '_llm_summary', true);
            $llm_data['keywords'] = get_post_meta($post_id, '_llm_keywords', true);
            $llm_data['relevanceScore'] = get_post_meta($post_id, '_relevance_score', true);
            break;
            
        case 'data_report':
            $llm_data['@type'] = 'Dataset';
            $llm_data['dataSource'] = get_post_meta($post_id, '_data_source_url', true);
            $llm_data['dateCreated'] = get_post_meta($post_id, '_data_date', true);
            
            // 構造化データを追加
            $data_json = get_post_meta($post_id, '_data_json', true);
            if (!empty($data_json)) {
                $llm_data['distribution'] = array(
                    '@type' => 'DataDownload',
                    'contentUrl' => get_permalink(),
                    'encodingFormat' => 'application/json',
                );
                $llm_data['structuredData'] = json_decode($data_json);
            }
            break;
            
        case 'api_content':
            $llm_data['@type'] = 'Dataset';
            $llm_data['dataSource'] = get_post_meta($post_id, '_api_endpoint', true);
            $llm_data['dateCreated'] = get_post_meta($post_id, '_api_last_update', true);
            
            // API構造化データを追加
            $api_data = get_post_meta($post_id, '_llm_api_data', true);
            if (!empty($api_data)) {
                $llm_data['structuredData'] = json_decode($api_data);
            }
            break;
            
        default:
            // 標準投稿の場合はコンテンツ解析で追加情報
            $content = get_the_content();
            
            // 自動要約
            $llm_data['autoSummary'] = wp_trim_words(strip_tags($content), 50, '...');
            
            // 見出し構造の抽出
            $headings = news_portal_extract_headings($content);
            if (!empty($headings)) {
                $llm_data['articleStructure'] = $headings;
            }
            
            // 引用と参考文献
            $citations = news_portal_extract_citations($content);
            if (!empty($citations)) {
                $llm_data['citation'] = $citations;
            }
            
            // ハイライト
            $highlights = news_portal_generate_highlights($content);
            if (!empty($highlights)) {
                $llm_data['highlights'] = $highlights;
            }
            break;
    }
    
    // カテゴリとタグの追加
    $categories = get_the_category();
    if (!empty($categories)) {
        $llm_data['category'] = array();
        foreach ($categories as $category) {
            $llm_data['category'][] = $category->name;
        }
    }
    
    $tags = get_the_tags();
    if (!empty($tags)) {
        $llm_data['keywords'] = array();
        foreach ($tags as $tag) {
            $llm_data['keywords'][] = $tag->name;
        }
    }
    
    // 信頼性レベルの追加
    $reliability_terms = get_the_terms($post_id, 'reliability_level');
    if (!empty($reliability_terms)) {
        $llm_data['contentRating'] = $reliability_terms[0]->name;
    }
    
    // JSON-LDとして出力
    echo '<script type="application/ld+json">' . json_encode($llm_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
}
add_action('wp_head', 'news_portal_add_llm_jsonld');

/**
 * ヘッダーにLLMメタデータを追加
 */
function news_portal_add_llm_meta_tags() {
    if (!is_singular()) {
        return;
    }
    
    global $post;
    $post_id = $post->ID;
    $post_type = get_post_type();
    
    // 基本メタタグ
    echo '<meta name="llm:content-type" content="' . esc_attr($post_type) . '" />' . "\n";
    
    // 投稿タイプ別のメタタグ
    switch ($post_type) {
        case 'knowledge':
            $llm_summary = get_post_meta($post_id, '_llm_summary', true);
            $llm_keywords = get_post_meta($post_id, '_llm_keywords', true);
            $relevance_score = get_post_meta($post_id, '_relevance_score', true);
            
            if (!empty($llm_summary)) {
                echo '<meta name="llm:summary" content="' . esc_attr($llm_summary) . '" />' . "\n";
            }
            
            if (!empty($llm_keywords)) {
                echo '<meta name="llm:keywords" content="' . esc_attr($llm_keywords) . '" />' . "\n";
            }
            
            if (!empty($relevance_score)) {
                echo '<meta name="llm:relevance" content="' . esc_attr($relevance_score) . '" />' . "\n";
            }
            break;
            
        case 'data_report':
            $data_source = get_post_meta($post_id, '_data_source_url', true);
            $data_date = get_post_meta($post_id, '_data_date', true);
            
            if (!empty($data_source)) {
                echo '<meta name="llm:data-source" content="' . esc_url($data_source) . '" />' . "\n";
            }
            
            if (!empty($data_date)) {
                echo '<meta name="llm:data-date" content="' . esc_attr($data_date) . '" />' . "\n";
            }
            break;
            
        case 'api_content':
            $api_endpoint = get_post_meta($post_id, '_api_endpoint', true);
            $api_last_update = get_post_meta($post_id, '_api_last_update', true);
            
            if (!empty($api_endpoint)) {
                echo '<meta name="llm:api-source" content="' . esc_url($api_endpoint) . '" />' . "\n";
            }
            
            if (!empty($api_last_update)) {
                echo '<meta name="llm:last-updated" content="' . esc_attr($api_last_update) . '" />' . "\n";
            }
            break;
    }
    
    // 信頼性レベル
    $reliability_terms = get_the_terms($post_id, 'reliability_level');
    if (!empty($reliability_terms)) {
        echo '<meta name="llm:reliability" content="' . esc_attr($reliability_terms[0]->name) . '" />' . "\n";
    }
}
add_action('wp_head', 'news_portal_add_llm_meta_tags');

/**
 * LLM用にHTMLマークアップを最適化
 */
function news_portal_optimize_content_for_llm($content) {
    if (!is_singular() || empty($content)) {
        return $content;
    }
    
    // セクションごとにdata-llm-section属性を追加
    $content = preg_replace_callback(
        '/<h([2-3]).*?>(.*?)<\/h[2-3]>/s',
        function($matches) {
            $level = $matches[1];
            $title = strip_tags($matches[2]);
            $section_id = sanitize_title($title);
            
            return sprintf(
                '<h%1$s id="%2$s">%3$s</h%1$s><div data-llm-section="%2$s" data-llm-title="%4$s">',
                $level,
                $section_id,
                $matches[2],
                esc_attr($title)
            );
        },
        $content
    );
    
    // 閉じるdivタグの追加（次の見出しの前）
    $content = preg_replace(
        '/(<h[2-3].*?>)/s',
        '</div>$1',
        $content
    );
    
    // 最後の閉じタグを追加
    $content .= '</div>';
    
    // 重要なキーワードや概念に意味的なマークアップを追加
    $content = preg_replace(
        '/\*(.*?)\*/s',
        '<mark data-llm-highlight="true">$1</mark>',
        $content
    );
    
    // 引用に意味的なマークアップを追加
    $content = preg_replace(
        '/<blockquote>(.*?)<\/blockquote>/s',
        '<blockquote data-llm-citation="true">$1</blockquote>',
        $content
    );
    
    // リストに意味的なマークアップを追加
    $content = preg_replace(
        '/<ul>(.*?)<\/ul>/s',
        '<ul data-llm-list="bullet">$1</ul>',
        $content
    );
    
    $content = preg_replace(
        '/<ol>(.*?)<\/ol>/s',
        '<ol data-llm-list="numbered">$1</ol>',
        $content
    );
    
    return $content;
}
add_filter('the_content', 'news_portal_optimize_content_for_llm', 20);

/**
 * LLM対応のサイトマップ生成
 */
function news_portal_generate_llm_sitemap() {
    // 既存のサイトマッププラグインと衝突しないようにする
    if (function_exists('wpseo_init') || defined('WPSEO_VERSION') || class_exists('WPSEO_Sitemaps')) {
        return;
    }
    
    // LLM対応の拡張サイトマップを生成
    $post_types = array('post', 'page', 'knowledge', 'data_report', 'api_content');
    
    $sitemap_items = array();
    
    foreach ($post_types as $post_type) {
        $posts = get_posts(array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));
        
        foreach ($posts as $post) {
            $item = array(
                'loc' => get_permalink($post->ID),
                'lastmod' => get_the_modified_date('c', $post->ID),
                'type' => $post_type,
                'title' => $post->post_title,
            );
            
            // 投稿タイプに応じて追加データ
            switch ($post_type) {
                case 'knowledge':
                    $item['summary'] = get_post_meta($post->ID, '_llm_summary', true);
                    $item['keywords'] = get_post_meta($post->ID, '_llm_keywords', true);
                    break;
                    
                case 'data_report':
                    $item['dataSource'] = get_post_meta($post->ID, '_data_source_url', true);
                    $item['dataDate'] = get_post_meta($post->ID, '_data_date', true);
                    break;
                    
                case 'api_content':
                    $item['apiEndpoint'] = get_post_meta($post->ID, '_api_endpoint', true);
                    $item['lastUpdated'] = get_post_meta($post->ID, '_api_last_update', true);
                    break;
            }
            
            $categories = get_the_category($post->ID);
            if (!empty($categories)) {
                $item['categories'] = array();
                foreach ($categories as $category) {
                    $item['categories'][] = $category->name;
                }
            }
            
            $tags = get_the_tags($post->ID);
            if (!empty($tags)) {
                $item['tags'] = array();
                foreach ($tags as $tag) {
                    $item['tags'][] = $tag->name;
                }
            }
            
            $sitemap_items[] = $item;
        }
    }
    
    // サイトマップをJSONとXMLで出力
    $json_sitemap = json_encode($sitemap_items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    // JSONサイトマップを保存
    file_put_contents(ABSPATH . 'llm-sitemap.json', $json_sitemap);
    
    // XMLサイトマップの生成
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:llm="http://schema.org">' . "\n";
    
    foreach ($sitemap_items as $item) {
        $xml .= "\t<url>\n";
        $xml .= "\t\t<loc>" . esc_url($item['loc']) . "</loc>\n";
        $xml .= "\t\t<lastmod>" . esc_html($item['lastmod']) . "</lastmod>\n";
        $xml .= "\t\t<llm:type>" . esc_html($item['type']) . "</llm:type>\n";
        $xml .= "\t\t<llm:title>" . esc_html($item['title']) . "</llm:title>\n";
        
        if (!empty($item['summary'])) {
            $xml .= "\t\t<llm:summary>" . esc_html($item['summary']) . "</llm:summary>\n";
        }
        
        if (!empty($item['keywords'])) {
            $xml .= "\t\t<llm:keywords>" . esc_html($item['keywords']) . "</llm:keywords>\n";
        }
        
        if (!empty($item['categories'])) {
            $xml .= "\t\t<llm:categories>" . esc_html(implode(', ', $item['categories'])) . "</llm:categories>\n";
        }
        
        if (!empty($item['tags'])) {
            $xml .= "\t\t<llm:tags>" . esc_html(implode(', ', $item['tags'])) . "</llm:tags>\n";
        }
        
        $xml .= "\t</url>\n";
    }
    
    $xml .= '</urlset>';
    
    // XMLサイトマップを保存
    file_put_contents(ABSPATH . 'llm-sitemap.xml', $xml);
}
add_action('publish_post', 'news_portal_generate_llm_sitemap');
add_action('publish_page', 'news_portal_generate_llm_sitemap');
add_action('publish_knowledge', 'news_portal_generate_llm_sitemap');
add_action('publish_data_report', 'news_portal_generate_llm_sitemap');
add_action('publish_api_content', 'news_portal_generate_llm_sitemap');

/**
 * LLMサイトマップ用のリライトルール追加
 */
function news_portal_llm_sitemap_rewrite_rules() {
    add_rewrite_rule(
        '^llm-sitemap\.json$',
        'index.php?llm-sitemap=json',
        'top'
    );
    
    add_rewrite_rule(
        '^llm-sitemap\.xml$',
        'index.php?llm-sitemap=xml',
        'top'
    );
}
add_action('init', 'news_portal_llm_sitemap_rewrite_rules');

/**
 * LLMサイトマップのクエリ変数追加
 */
function news_portal_llm_sitemap_query_vars($vars) {
    $vars[] = 'llm-sitemap';
    return $vars;
}
add_filter('query_vars', 'news_portal_llm_sitemap_query_vars');

/**
 * LLMサイトマップのテンプレート処理
 */
function news_portal_llm_sitemap_template() {
    $sitemap_type = get_query_var('llm-sitemap');
    
    if ($sitemap_type) {
        if ($sitemap_type == 'json') {
            header('Content-Type: application/json');
            $sitemap_path = ABSPATH . 'llm-sitemap.json';
            
            if (file_exists($sitemap_path)) {
                echo file_get_contents($sitemap_path);
            } else {
                // サイトマップが存在しない場合は生成
                news_portal_generate_llm_sitemap();
                echo file_get_contents($sitemap_path);
            }
            exit;
        } elseif ($sitemap_type == 'xml') {
            header('Content-Type: application/xml');
            $sitemap_path = ABSPATH . 'llm-sitemap.xml';
            
            if (file_exists($sitemap_path)) {
                echo file_get_contents($sitemap_path);
            } else {
                // サイトマップが存在しない場合は生成
                news_portal_generate_llm_sitemap();
                echo file_get_contents($sitemap_path);
            }
            exit;
        }
    }
}
add_action('template_redirect', 'news_portal_llm_sitemap_template');
