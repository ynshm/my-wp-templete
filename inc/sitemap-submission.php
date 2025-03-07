<?php
/**
 * サイトマップ送信機能
 *
 * @package News_Portal
 */

/**
 * サイトマップURLの自動送信
 */
function news_portal_submit_sitemap() {
    // サイトマップのURL（WordPressのデフォルトサイトマップまたはプラグインによるもの）
    $sitemap_url = home_url( '/sitemap.xml' );

    // Googleへのサイトマップ送信（Search Console APIを使用する場合は認証が必要）
    // ここではシンプルに通知のみを行う
    $ping_url = 'https://www.google.com/ping?sitemap=' . urlencode( $sitemap_url );
    wp_remote_get( $ping_url );

    // Bingへのサイトマップ送信
    $ping_url = 'https://www.bing.com/ping?sitemap=' . urlencode( $sitemap_url );
    wp_remote_get( $ping_url );

    // Yandexへのサイトマップ送信
    $ping_url = 'https://webmaster.yandex.ru/ping?sitemap=' . urlencode( $sitemap_url );
    wp_remote_get( $ping_url );

    // 最終送信日時を更新
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
 * 管理画面ページの表示
 */
function news_portal_sitemap_admin_page() {
    // 権限チェック
    if (!current_user_can('manage_options')) {
        return;
    }

    // サイトマップ送信処理
    if (isset($_POST['submit_sitemap']) && check_admin_referer('news_portal_submit_sitemap_nonce')) {
        news_portal_submit_sitemap();
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Sitemap submitted successfully!', 'news-portal') . '</p></div>';
    }

    // 最終送信日時の取得
    $last_submitted = get_option('news_portal_sitemap_last_submitted');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="">
            <?php wp_nonce_field('news_portal_submit_sitemap_nonce'); ?>

            <p><?php esc_html_e('Click the button below to submit your sitemap to search engines.', 'news-portal'); ?></p>

            <?php if ($last_submitted) : ?>
                <p><?php printf(esc_html__('Last submitted on: %s', 'news-portal'), $last_submitted); ?></p>
            <?php endif; ?>

            <p>
                <input type="submit" name="submit_sitemap" class="button button-primary" value="<?php esc_attr_e('Submit Sitemap', 'news-portal'); ?>">
            </p>
        </form>
    </div>
    <?php
}

/**
 * 新規投稿公開時にサイトマップを送信
 */
function news_portal_auto_submit_on_publish($new_status, $old_status, $post) {
    // 投稿が下書きから公開に変更された場合
    if ('publish' === $new_status && 'publish' !== $old_status && 'post' === $post->post_type) {
        // サイトマップの送信が1時間以内に行われていない場合のみ実行
        $last_submitted = get_option('news_portal_sitemap_last_submitted');
        if (!$last_submitted || (strtotime($last_submitted) < (time() - 3600))) {
            news_portal_submit_sitemap();
        }
    }
}
add_action('transition_post_status', 'news_portal_auto_submit_on_publish', 10, 3);

/**
 * 投稿保存時にサイトマップ送信
 */
function news_portal_submit_sitemap_on_save( $post_id ) {
    // 自動下書き保存や投稿の改訂では送信しない
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }

    // 投稿タイプが公開されるものかチェック
    $post_type = get_post_type( $post_id );
    if ( ! is_post_type_viewable( $post_type ) ) {
        return;
    }

    // 投稿ステータスが公開されているかチェック
    $post_status = get_post_status( $post_id );
    if ( 'publish' !== $post_status ) {
        return;
    }

    // サイトマップ送信
    news_portal_submit_sitemap();
}
add_action( 'save_post', 'news_portal_submit_sitemap_on_save' );

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

?>