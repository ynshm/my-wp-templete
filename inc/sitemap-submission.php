<?php
/**
 * サイトマップ送信機能
 *
 * @package News_Portal
 */

/**
 * 検索エンジンにサイトマップを送信
 */
function news_portal_submit_sitemap() {
    $sitemap_url = esc_url(home_url('/sitemap.xml'));

    // Googleへのサイトマップ送信
    $google_url = 'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url);
    wp_remote_get($google_url);

    // Bingへのサイトマップ送信
    $bing_url = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url);
    wp_remote_get($bing_url);

    update_option('news_portal_sitemap_last_submitted', current_time('mysql'));

    return true;
}

/**
 * 管理画面に送信ボタンを追加
 */
function news_portal_sitemap_admin_menu() {
    add_management_page(
        __('Sitemap Submission', 'news-portal'),
        __('Sitemap Submission', 'news-portal'),
        'manage_options',
        'sitemap-submission',
        'news_portal_sitemap_admin_page'
    );
}
add_action('admin_menu', 'news_portal_sitemap_admin_menu');

/**
 * サイトマップ送信管理ページ
 */
function news_portal_sitemap_admin_page() {
    // 送信処理
    if (isset($_POST['submit_sitemap']) && current_user_can('manage_options')) {
        check_admin_referer('news_portal_submit_sitemap');

        $result = news_portal_submit_sitemap();
        if ($result) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Sitemap successfully submitted to search engines.', 'news-portal') . '</p></div>';
        }
    }

    $last_submitted = get_option('news_portal_sitemap_last_submitted');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Sitemap Submission', 'news-portal'); ?></h1>

        <p><?php echo esc_html__('Use this page to manually submit your sitemap to search engines.', 'news-portal'); ?></p>

        <?php if ($last_submitted) : ?>
            <p><?php echo sprintf(__('Last submitted: %s', 'news-portal'), $last_submitted); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('news_portal_submit_sitemap'); ?>
            <p>
                <input type="submit" name="submit_sitemap" class="button button-primary" value="<?php echo esc_attr__('Submit Sitemap Now', 'news-portal'); ?>">
            </p>
        </form>
    </div>
    <?php
}

/**
 * 投稿保存時に自動でサイトマップを送信
 */
function news_portal_auto_submit_sitemap($post_id) {
    // 自動下書き保存の場合は実行しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 投稿タイプが公開されていない場合は実行しない
    if (get_post_status($post_id) !== 'publish') {
        return;
    }

    // 前回の送信から24時間経過していない場合は実行しない
    $last_submitted = get_option('news_portal_sitemap_last_submitted');
    if ($last_submitted) {
        $last_time = strtotime($last_submitted);
        $current_time = current_time('timestamp');

        if (($current_time - $last_time) < DAY_IN_SECONDS) {
            return;
        }
    }

    news_portal_submit_sitemap();
}
add_action('save_post', 'news_portal_auto_submit_sitemap');

/**
 * Display admin notice after manual submission
 */
function news_portal_sitemap_admin_notice() {
    if (isset($_GET['page']) && $_GET['page'] === 'sitemap-submission' && isset($_GET['submitted']) && $_GET['submitted'] === '1') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Sitemap successfully submitted to search engines.', 'news-portal'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'news_portal_sitemap_admin_notice');