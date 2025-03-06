
<?php
/**
 * API連携機能
 * 外部APIと連携してリアルタイムデータを取得
 *
 * @package News_Portal
 */

/**
 * API連携のスケジュールを設定
 */
function news_portal_setup_api_schedule() {
    if (!wp_next_scheduled('news_portal_api_refresh_event')) {
        wp_schedule_event(time(), 'hourly', 'news_portal_api_refresh_event');
    }
}
add_action('wp', 'news_portal_setup_api_schedule');

/**
 * 設定した間隔でAPIデータを更新
 */
function news_portal_refresh_api_data() {
    // API連携コンテンツを取得
    $api_contents = get_posts(array(
        'post_type' => 'api_content',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    if (empty($api_contents)) {
        return;
    }
    
    foreach ($api_contents as $content) {
        $post_id = $content->ID;
        $endpoint = get_post_meta($post_id, '_api_endpoint', true);
        $method = get_post_meta($post_id, '_api_method', true);
        $refresh = intval(get_post_meta($post_id, '_api_refresh', true));
        $params_json = get_post_meta($post_id, '_api_params', true);
        
        // リフレッシュ間隔をチェック
        $last_update = get_post_meta($post_id, '_api_last_update', true);
        if ($last_update) {
            $hours_since_update = (time() - strtotime($last_update)) / 3600;
            if ($hours_since_update < $refresh) {
                continue; // まだリフレッシュの時間でない
            }
        }
        
        // APIリクエストを実行
        $api_data = news_portal_fetch_api_data($endpoint, $method, $params_json);
        
        if ($api_data) {
            // APIデータを保存
            update_post_meta($post_id, '_api_data', $api_data);
            update_post_meta($post_id, '_api_last_update', current_time('mysql'));
            
            // 自動投稿コンテンツの更新
            news_portal_update_api_content($post_id, $api_data);
        }
    }
}
add_action('news_portal_api_refresh_event', 'news_portal_refresh_api_data');

/**
 * APIデータをフェッチする
 */
function news_portal_fetch_api_data($endpoint, $method = 'GET', $params_json = '') {
    if (empty($endpoint)) {
        return false;
    }
    
    $args = array(
        'timeout' => 30,
        'headers' => array(
            'Accept' => 'application/json',
        ),
    );
    
    // POSTパラメータの設定
    if ($method === 'POST' && !empty($params_json)) {
        $params = json_decode($params_json, true);
        if (is_array($params)) {
            $args['body'] = $params;
        }
    }
    
    // GETパラメータの設定
    if ($method === 'GET' && !empty($params_json)) {
        $params = json_decode($params_json, true);
        if (is_array($params)) {
            $endpoint = add_query_arg($params, $endpoint);
        }
    }
    
    // APIリクエスト
    $response = ($method === 'GET') 
        ? wp_remote_get($endpoint, $args) 
        : wp_remote_post($endpoint, $args);
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    return $data;
}

/**
 * APIデータを使ってコンテンツを更新
 */
function news_portal_update_api_content($post_id, $api_data) {
    // コンテンツのフォーマット設定を取得
    $content_format = get_post_meta($post_id, '_api_content_format', true);
    
    if (empty($content_format)) {
        $content_format = 'default'; // デフォルトフォーマット
    }
    
    $content = '';
    
    switch ($content_format) {
        case 'table':
            $content = news_portal_format_api_data_as_table($api_data);
            break;
            
        case 'list':
            $content = news_portal_format_api_data_as_list($api_data);
            break;
            
        case 'chart':
            $content = news_portal_format_api_data_as_chart($api_data);
            break;
            
        default:
            $content = news_portal_format_api_data_default($api_data);
            break;
    }
    
    // 元の投稿コンテンツを取得
    $post = get_post($post_id);
    $original_content = $post->post_content;
    
    // <!-- API_DATA_START --> と <!-- API_DATA_END --> の間のコンテンツを置き換え
    $pattern = '/<!-- API_DATA_START -->.*?<!-- API_DATA_END -->/s';
    $replacement = "<!-- API_DATA_START -->\n{$content}\n<!-- API_DATA_END -->";
    
    if (preg_match($pattern, $original_content)) {
        $new_content = preg_replace($pattern, $replacement, $original_content);
    } else {
        $new_content = $original_content . "\n\n" . $replacement;
    }
    
    // 投稿を更新
    wp_update_post(array(
        'ID' => $post_id,
        'post_content' => $new_content,
    ));
    
    // LLM用のJSONデータも保存
    update_post_meta($post_id, '_llm_api_data', json_encode($api_data));
}

/**
 * APIデータをテーブル形式でフォーマット
 */
function news_portal_format_api_data_as_table($data) {
    if (!is_array($data) || empty($data)) {
        return '<p>' . __('No data available', 'news-portal') . '</p>';
    }
    
    $output = '<div class="api-data-table-container">';
    $output .= '<table class="api-data-table">';
    
    // テーブルヘッダー
    $output .= '<thead><tr>';
    $first_item = reset($data);
    if (is_array($first_item)) {
        foreach (array_keys($first_item) as $key) {
            $output .= '<th>' . esc_html($key) . '</th>';
        }
    } else {
        $output .= '<th>' . __('Key', 'news-portal') . '</th>';
        $output .= '<th>' . __('Value', 'news-portal') . '</th>';
    }
    $output .= '</tr></thead>';
    
    // テーブルボディ
    $output .= '<tbody>';
    if (is_array($first_item)) {
        // 配列の配列の場合
        foreach ($data as $item) {
            $output .= '<tr>';
            foreach ($item as $value) {
                $output .= '<td>' . (is_array($value) ? json_encode($value) : esc_html($value)) . '</td>';
            }
            $output .= '</tr>';
        }
    } else {
        // 単純な連想配列の場合
        foreach ($data as $key => $value) {
            $output .= '<tr>';
            $output .= '<td>' . esc_html($key) . '</td>';
            $output .= '<td>' . (is_array($value) ? json_encode($value) : esc_html($value)) . '</td>';
            $output .= '</tr>';
        }
    }
    $output .= '</tbody>';
    
    $output .= '</table>';
    $output .= '</div>';
    
    return $output;
}

/**
 * APIデータをリスト形式でフォーマット
 */
function news_portal_format_api_data_as_list($data) {
    if (!is_array($data) || empty($data)) {
        return '<p>' . __('No data available', 'news-portal') . '</p>';
    }
    
    $output = '<div class="api-data-list-container">';
    $output .= '<ul class="api-data-list">';
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $output .= '<li><strong>' . esc_html($key) . ':</strong>';
            $output .= '<ul>';
            foreach ($value as $sub_key => $sub_value) {
                if (is_array($sub_value)) {
                    $output .= '<li><strong>' . esc_html($sub_key) . ':</strong> ' . json_encode($sub_value) . '</li>';
                } else {
                    $output .= '<li><strong>' . esc_html($sub_key) . ':</strong> ' . esc_html($sub_value) . '</li>';
                }
            }
            $output .= '</ul></li>';
        } else {
            $output .= '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
        }
    }
    
    $output .= '</ul>';
    $output .= '</div>';
    
    return $output;
}

/**
 * APIデータをチャート形式でフォーマット
 */
function news_portal_format_api_data_as_chart($data) {
    if (!is_array($data) || empty($data)) {
        return '<p>' . __('No data available', 'news-portal') . '</p>';
    }
    
    // チャートの生成に必要なデータを準備
    $chart_id = 'api-chart-' . uniqid();
    $chart_data = array();
    $chart_labels = array();
    
    // シンプルな配列構造を想定
    foreach ($data as $key => $value) {
        if (is_numeric($value)) {
            $chart_labels[] = $key;
            $chart_data[] = $value;
        }
    }
    
    if (empty($chart_data)) {
        return '<p>' . __('No chart data available', 'news-portal') . '</p>';
    }
    
    // Chart.js用のコード生成
    $output = '<div class="api-data-chart-container">';
    $output .= '<canvas id="' . esc_attr($chart_id) . '" width="400" height="200"></canvas>';
    $output .= '<script>';
    $output .= 'document.addEventListener("DOMContentLoaded", function() {';
    $output .= 'var ctx = document.getElementById("' . esc_js($chart_id) . '").getContext("2d");';
    $output .= 'var apiChart = new Chart(ctx, {';
    $output .= '    type: "bar",';
    $output .= '    data: {';
    $output .= '        labels: ' . json_encode($chart_labels) . ',';
    $output .= '        datasets: [{';
    $output .= '            label: "' . esc_js(__('API Data', 'news-portal')) . '",';
    $output .= '            data: ' . json_encode($chart_data) . ',';
    $output .= '            backgroundColor: "rgba(0, 123, 255, 0.5)",';
    $output .= '            borderColor: "rgba(0, 123, 255, 1)",';
    $output .= '            borderWidth: 1';
    $output .= '        }]';
    $output .= '    },';
    $output .= '    options: {';
    $output .= '        responsive: true,';
    $output .= '        scales: {';
    $output .= '            y: {';
    $output .= '                beginAtZero: true';
    $output .= '            }';
    $output .= '        }';
    $output .= '    }';
    $output .= '});';
    $output .= '});';
    $output .= '</script>';
    $output .= '</div>';
    
    return $output;
}

/**
 * APIデータのデフォルトフォーマット
 */
function news_portal_format_api_data_default($data) {
    if (!is_array($data)) {
        return '<p>' . __('No data available', 'news-portal') . '</p>';
    }
    
    // データの概要をテキストで表示
    $output = '<div class="api-data-default">';
    $output .= '<h3>' . __('API Data Summary', 'news-portal') . '</h3>';
    
    // データの構造に基づいて表示を生成
    $count = count($data);
    $output .= '<p>' . sprintf(__('Retrieved %d items from API.', 'news-portal'), $count) . '</p>';
    
    // 最初の数項目を表示
    $output .= '<div class="api-data-preview">';
    $i = 0;
    foreach ($data as $key => $value) {
        if ($i >= 5) break; // 最初の5項目のみ表示
        
        $output .= '<div class="api-data-item">';
        if (is_array($value)) {
            $output .= '<h4>' . esc_html($key) . '</h4>';
            $output .= '<pre class="api-data-json">' . esc_html(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
        } else {
            $output .= '<p><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</p>';
        }
        $output .= '</div>';
        
        $i++;
    }
    
    if ($count > 5) {
        $output .= '<p class="api-data-more">' . __('... and more items', 'news-portal') . '</p>';
    }
    
    $output .= '</div>'; // .api-data-preview
    $output .= '</div>'; // .api-data-default
    
    return $output;
}

/**
 * Chart.jsライブラリの読み込み
 */
function news_portal_enqueue_chart_js() {
    // 特定の投稿タイプでのみ読み込み
    if (is_singular('api_content')) {
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js',
            array(),
            '3.7.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'news_portal_enqueue_chart_js');
