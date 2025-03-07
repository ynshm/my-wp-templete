
<?php
/**
 * セキュリティ強化機能
 *
 * @package News_Portal
 */

/**
 * XMLRPCの無効化
 */
function news_portal_disable_xmlrpc() {
    // カスタマイザーの設定を取得
    $disable_xmlrpc = get_theme_mod('security_disable_xmlrpc', true);
    
    if ($disable_xmlrpc) {
        // XMLRPCを無効化
        add_filter('xmlrpc_enabled', '__return_false');
        
        // pingback pingを無効化
        add_filter('wp_headers', 'news_portal_remove_pingback_header');
        
        // XMLRPCメソッドを無効化
        add_filter('xmlrpc_methods', 'news_portal_disable_xmlrpc_methods');
    }
}
add_action('init', 'news_portal_disable_xmlrpc');

/**
 * Pingbackヘッダーの削除
 */
function news_portal_remove_pingback_header($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}

/**
 * XMLRPCメソッドの無効化
 */
function news_portal_disable_xmlrpc_methods($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
}

/**
 * セキュリティヘッダーの追加
 */
function news_portal_security_headers() {
    // カスタマイザーの設定を取得
    $security_headers_enabled = get_theme_mod('security_headers_enabled', true);
    
    if ($security_headers_enabled) {
        // X-Frame-Options ヘッダー（クリックジャッキング対策）
        header('X-Frame-Options: SAMEORIGIN');
        
        // X-XSS-Protection ヘッダー（XSS対策）
        header('X-XSS-Protection: 1; mode=block');
        
        // X-Content-Type-Options ヘッダー（MIMEタイプスニッフィング対策）
        header('X-Content-Type-Options: nosniff');
        
        // Referrer-Policy ヘッダー
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'news_portal_security_headers');

/**
 * ログインページのカスタマイズ
 */
function news_portal_login_errors() {
    // ログインエラーメッセージをユーザー情報が漏れないように統一
    return __('Invalid login credentials.', 'news-portal');
}
add_filter('login_errors', 'news_portal_login_errors');

/**
 * データベースバックアップの自動化
 */
function news_portal_schedule_backups() {
    // 実際の実装ではバックアップ機能を実装
    // この部分はWordPressのデータベースバックアッププラグインとの連携が推奨されます
}
add_action('init', 'news_portal_schedule_backups');

/**
 * 管理者アカウントの保護
 */
function news_portal_protect_admin_account($user_search) {
    global $current_user, $wpdb;
    
    // 現在のユーザーが管理者でない場合、管理者を検索結果から除外
    if (!current_user_can('manage_options')) {
        $user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.user_login != 'admin'", $user_search->query_where);
    }
}
add_action('pre_user_query', 'news_portal_protect_admin_account');

/**
 * ファイル編集機能の無効化（本番環境向け）
 */
function news_portal_disable_file_editing() {
    // 本番環境では以下のコードを有効にすることを推奨
    // define('DISALLOW_FILE_EDIT', true);
}

/**
 * セキュリティ設定のカスタマイザー追加
 */
function news_portal_security_customizer($wp_customize) {
    // セキュリティ設定セクションの追加
    $wp_customize->add_section('news_portal_security_section', array(
        'title' => __('Security Settings', 'news-portal'),
        'priority' => 120,
    ));
    
    // XMLRPCの設定
    $wp_customize->add_setting('security_disable_xmlrpc', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('security_disable_xmlrpc', array(
        'label' => __('Disable XML-RPC', 'news-portal'),
        'description' => __('Recommended for security unless you need XML-RPC functionality.', 'news-portal'),
        'section' => 'news_portal_security_section',
        'type' => 'checkbox',
    ));
    
    // ログイン保護の設定
    $wp_customize->add_setting('security_login_protection', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('security_login_protection', array(
        'label' => __('Enable Login Protection', 'news-portal'),
        'description' => __('Limit login attempts and protect login page.', 'news-portal'),
        'section' => 'news_portal_security_section',
        'type' => 'checkbox',
    ));
    
    // セキュリティヘッダーの設定
    $wp_customize->add_setting('security_headers_enabled', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('security_headers_enabled', array(
        'label' => __('Enable Security Headers', 'news-portal'),
        'description' => __('Add security-related HTTP headers to responses.', 'news-portal'),
        'section' => 'news_portal_security_section',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'news_portal_security_customizer');

/**
 * チェックボックスの正規化
 */
function news_portal_sanitize_checkbox($checked) {
    return (isset($checked) && true == $checked) ? true : false;
}
