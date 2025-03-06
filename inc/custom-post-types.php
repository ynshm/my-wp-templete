
<?php
/**
 * カスタム投稿タイプの定義
 * LLM対応のコンテンツ構造化を実現
 *
 * @package News_Portal
 */

/**
 * LLM対応のカスタム投稿タイプを登録
 */
function news_portal_register_custom_post_types() {
    // ナレッジベース（FAQ・用語集）
    register_post_type('knowledge', array(
        'labels' => array(
            'name' => __('Knowledge Base', 'news-portal'),
            'singular_name' => __('Knowledge Item', 'news-portal'),
            'add_new' => __('Add New', 'news-portal'),
            'add_new_item' => __('Add New Knowledge Item', 'news-portal'),
            'edit_item' => __('Edit Knowledge Item', 'news-portal'),
            'new_item' => __('New Knowledge Item', 'news-portal'),
            'view_item' => __('View Knowledge Item', 'news-portal'),
            'search_items' => __('Search Knowledge Base', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'knowledge'),
        'show_in_rest' => true, // Gutenbergエディタサポート
    ));
    
    // データレポート（機械可読データを含む記事）
    register_post_type('data_report', array(
        'labels' => array(
            'name' => __('Data Reports', 'news-portal'),
            'singular_name' => __('Data Report', 'news-portal'),
            'add_new' => __('Add New', 'news-portal'),
            'add_new_item' => __('Add New Data Report', 'news-portal'),
            'edit_item' => __('Edit Data Report', 'news-portal'),
            'new_item' => __('New Data Report', 'news-portal'),
            'view_item' => __('View Data Report', 'news-portal'),
            'search_items' => __('Search Data Reports', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-chart-bar',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'data'),
        'show_in_rest' => true,
    ));
    
    // API連携コンテンツ
    register_post_type('api_content', array(
        'labels' => array(
            'name' => __('API Contents', 'news-portal'),
            'singular_name' => __('API Content', 'news-portal'),
            'add_new' => __('Add New', 'news-portal'),
            'add_new_item' => __('Add New API Content', 'news-portal'),
            'edit_item' => __('Edit API Content', 'news-portal'),
            'new_item' => __('New API Content', 'news-portal'),
            'view_item' => __('View API Content', 'news-portal'),
            'search_items' => __('Search API Contents', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-rest-api',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'api'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'news_portal_register_custom_post_types');

/**
 * LLM対応のカスタムタクソノミーを登録
 */
function news_portal_register_custom_taxonomies() {
    // ナレッジ分類
    register_taxonomy('knowledge_type', array('knowledge'), array(
        'labels' => array(
            'name' => __('Knowledge Types', 'news-portal'),
            'singular_name' => __('Knowledge Type', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'knowledge-type'),
        'show_in_rest' => true,
    ));
    
    // データソース（データの出所）
    register_taxonomy('data_source', array('data_report', 'api_content'), array(
        'labels' => array(
            'name' => __('Data Sources', 'news-portal'),
            'singular_name' => __('Data Source', 'news-portal'),
        ),
        'hierarchical' => false,
        'rewrite' => array('slug' => 'data-source'),
        'show_in_rest' => true,
    ));
    
    // 信頼性レベル
    register_taxonomy('reliability_level', array('post', 'knowledge', 'data_report', 'api_content'), array(
        'labels' => array(
            'name' => __('Reliability Levels', 'news-portal'),
            'singular_name' => __('Reliability Level', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'reliability'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'news_portal_register_custom_taxonomies');

/**
 * カスタム投稿タイプのメタボックスを追加
 */
function news_portal_add_meta_boxes() {
    // ナレッジベース用メタボックス
    add_meta_box(
        'knowledge_details',
        __('Knowledge Details', 'news-portal'),
        'news_portal_knowledge_meta_box_callback',
        'knowledge',
        'normal',
        'high'
    );
    
    // データレポート用メタボックス
    add_meta_box(
        'data_report_details',
        __('Data Report Details', 'news-portal'),
        'news_portal_data_report_meta_box_callback',
        'data_report',
        'normal',
        'high'
    );
    
    // API連携用メタボックス
    add_meta_box(
        'api_content_details',
        __('API Configuration', 'news-portal'),
        'news_portal_api_content_meta_box_callback',
        'api_content',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'news_portal_add_meta_boxes');

/**
 * ナレッジベースのメタボックスコールバック
 */
function news_portal_knowledge_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');
    
    $llm_summary = get_post_meta($post->ID, '_llm_summary', true);
    $llm_keywords = get_post_meta($post->ID, '_llm_keywords', true);
    $relevance_score = get_post_meta($post->ID, '_relevance_score', true);
    
    ?>
    <p>
        <label for="llm_summary"><?php _e('LLM Optimized Summary:', 'news-portal'); ?></label><br />
        <textarea id="llm_summary" name="llm_summary" style="width:100%;height:80px;"><?php echo esc_textarea($llm_summary); ?></textarea>
        <span class="description"><?php _e('A concise summary optimized for LLM comprehension (max 200 characters)', 'news-portal'); ?></span>
    </p>
    <p>
        <label for="llm_keywords"><?php _e('LLM Keywords:', 'news-portal'); ?></label><br />
        <input type="text" id="llm_keywords" name="llm_keywords" style="width:100%" value="<?php echo esc_attr($llm_keywords); ?>" />
        <span class="description"><?php _e('Comma-separated keywords to help LLMs understand the content', 'news-portal'); ?></span>
    </p>
    <p>
        <label for="relevance_score"><?php _e('Relevance Score (1-10):', 'news-portal'); ?></label><br />
        <input type="number" id="relevance_score" name="relevance_score" min="1" max="10" value="<?php echo esc_attr($relevance_score ? $relevance_score : 5); ?>" />
        <span class="description"><?php _e('How relevant is this item for search queries (10 = highly relevant)', 'news-portal'); ?></span>
    </p>
    <?php
}

/**
 * データレポートのメタボックスコールバック
 */
function news_portal_data_report_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');
    
    $data_source_url = get_post_meta($post->ID, '_data_source_url', true);
    $data_date = get_post_meta($post->ID, '_data_date', true);
    $data_format = get_post_meta($post->ID, '_data_format', true);
    $data_json = get_post_meta($post->ID, '_data_json', true);
    
    ?>
    <p>
        <label for="data_source_url"><?php _e('Data Source URL:', 'news-portal'); ?></label><br />
        <input type="url" id="data_source_url" name="data_source_url" style="width:100%" value="<?php echo esc_url($data_source_url); ?>" />
    </p>
    <p>
        <label for="data_date"><?php _e('Data Collection Date:', 'news-portal'); ?></label><br />
        <input type="date" id="data_date" name="data_date" value="<?php echo esc_attr($data_date); ?>" />
    </p>
    <p>
        <label for="data_format"><?php _e('Data Format:', 'news-portal'); ?></label><br />
        <select id="data_format" name="data_format">
            <option value="json" <?php selected($data_format, 'json'); ?>>JSON</option>
            <option value="csv" <?php selected($data_format, 'csv'); ?>>CSV</option>
            <option value="xml" <?php selected($data_format, 'xml'); ?>>XML</option>
            <option value="other" <?php selected($data_format, 'other'); ?>>Other</option>
        </select>
    </p>
    <p>
        <label for="data_json"><?php _e('Machine-Readable Data (JSON):', 'news-portal'); ?></label><br />
        <textarea id="data_json" name="data_json" style="width:100%;height:150px;font-family:monospace;"><?php echo esc_textarea($data_json); ?></textarea>
        <span class="description"><?php _e('Paste structured data in JSON format for LLM consumption', 'news-portal'); ?></span>
    </p>
    <?php
}

/**
 * API連携コンテンツのメタボックスコールバック
 */
function news_portal_api_content_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');
    
    $api_endpoint = get_post_meta($post->ID, '_api_endpoint', true);
    $api_method = get_post_meta($post->ID, '_api_method', true);
    $api_refresh = get_post_meta($post->ID, '_api_refresh', true);
    $api_params = get_post_meta($post->ID, '_api_params', true);
    
    ?>
    <p>
        <label for="api_endpoint"><?php _e('API Endpoint URL:', 'news-portal'); ?></label><br />
        <input type="url" id="api_endpoint" name="api_endpoint" style="width:100%" value="<?php echo esc_url($api_endpoint); ?>" />
    </p>
    <p>
        <label for="api_method"><?php _e('Request Method:', 'news-portal'); ?></label><br />
        <select id="api_method" name="api_method">
            <option value="GET" <?php selected($api_method, 'GET'); ?>>GET</option>
            <option value="POST" <?php selected($api_method, 'POST'); ?>>POST</option>
        </select>
    </p>
    <p>
        <label for="api_refresh"><?php _e('Auto-Refresh Interval (hours):', 'news-portal'); ?></label><br />
        <input type="number" id="api_refresh" name="api_refresh" min="1" max="168" value="<?php echo esc_attr($api_refresh ? $api_refresh : 24); ?>" />
        <span class="description"><?php _e('How often should this data be refreshed from the API', 'news-portal'); ?></span>
    </p>
    <p>
        <label for="api_params"><?php _e('API Parameters (JSON):', 'news-portal'); ?></label><br />
        <textarea id="api_params" name="api_params" style="width:100%;height:100px;font-family:monospace;"><?php echo esc_textarea($api_params); ?></textarea>
        <span class="description"><?php _e('Enter parameters as JSON object', 'news-portal'); ?></span>
    </p>
    <?php
}

/**
 * メタボックスのデータを保存
 */
function news_portal_save_meta_box_data($post_id) {
    if (!isset($_POST['news_portal_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['news_portal_meta_box_nonce'], 'news_portal_save_meta_box_data')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // ナレッジベースのメタデータ保存
    if (isset($_POST['llm_summary'])) {
        update_post_meta($post_id, '_llm_summary', sanitize_textarea_field($_POST['llm_summary']));
    }
    
    if (isset($_POST['llm_keywords'])) {
        update_post_meta($post_id, '_llm_keywords', sanitize_text_field($_POST['llm_keywords']));
    }
    
    if (isset($_POST['relevance_score'])) {
        update_post_meta($post_id, '_relevance_score', intval($_POST['relevance_score']));
    }
    
    // データレポートのメタデータ保存
    if (isset($_POST['data_source_url'])) {
        update_post_meta($post_id, '_data_source_url', esc_url_raw($_POST['data_source_url']));
    }
    
    if (isset($_POST['data_date'])) {
        update_post_meta($post_id, '_data_date', sanitize_text_field($_POST['data_date']));
    }
    
    if (isset($_POST['data_format'])) {
        update_post_meta($post_id, '_data_format', sanitize_text_field($_POST['data_format']));
    }
    
    if (isset($_POST['data_json'])) {
        update_post_meta($post_id, '_data_json', $_POST['data_json']); // JSON形式を維持するためサニタイズせず
    }
    
    // API連携のメタデータ保存
    if (isset($_POST['api_endpoint'])) {
        update_post_meta($post_id, '_api_endpoint', esc_url_raw($_POST['api_endpoint']));
    }
    
    if (isset($_POST['api_method'])) {
        update_post_meta($post_id, '_api_method', sanitize_text_field($_POST['api_method']));
    }
    
    if (isset($_POST['api_refresh'])) {
        update_post_meta($post_id, '_api_refresh', intval($_POST['api_refresh']));
    }
    
    if (isset($_POST['api_params'])) {
        update_post_meta($post_id, '_api_params', $_POST['api_params']); // JSON形式を維持するためサニタイズせず
    }
}
add_action('save_post', 'news_portal_save_meta_box_data');
