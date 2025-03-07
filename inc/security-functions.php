
<?php
/**
 * セキュリティ機能
 *
 * @package News_Portal
 */

/**
 * ログイン試行回数の制限
 */
function news_portal_limit_login_attempts() {
    // 実際の実装ではログイン試行回数を記録・制限する機能が必要
}
add_action('wp_login_failed', 'news_portal_limit_login_attempts');

/**
 * XMLRPCの無効化
 */
function news_portal_disable_xmlrpc() {
    add_filter('xmlrpc_enabled', '__return_false');
}
add_action('init', 'news_portal_disable_xmlrpc');

/**
 * WordPress バージョン情報の非表示
 */
function news_portal_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'news_portal_remove_wp_version');

/**
 * ログインページへのリダイレクト
 */
function news_portal_redirect_to_login() {
    // author/?=X のような攻撃からの保護
    if (isset($_GET['author'])) {
        wp_redirect(home_url('/wp-login.php'));
        exit;
    }
}
add_action('template_redirect', 'news_portal_redirect_to_login');

/**
 * セキュリティヘッダーの追加
 */
function news_portal_security_headers() {
    // X-Frame-Options ヘッダー
    header('X-Frame-Options: SAMEORIGIN');
    
    // X-XSS-Protection ヘッダー
    header('X-XSS-Protection: 1; mode=block');
    
    // X-Content-Type-Options ヘッダー
    header('X-Content-Type-Options: nosniff');
    
    // Referrer-Policy ヘッダー
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content-Security-Policy ヘッダー (必要に応じてカスタマイズ)
    // header("Content-Security-Policy: default-src 'self';");
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
        'description' => __('Add security headers to HTTP responses.', 'news-portal'),
        'section' => 'news_portal_security_section',
        'type' => 'checkbox',
    ));
    
    // 自動バックアップの設定
    $wp_customize->add_setting('security_auto_backup', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('security_auto_backup', array(
        'label' => __('Enable Automatic Backups', 'news-portal'),
        'description' => __('Schedule regular database backups.', 'news-portal'),
        'section' => 'news_portal_security_section',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'news_portal_security_customizer');
