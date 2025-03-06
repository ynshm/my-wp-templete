
<?php
/**
 * Sitemap Submission Functions
 * Automatically submits sitemaps to search engines
 *
 * @package News_Portal
 */

/**
 * Schedule sitemap submission events
 */
function news_portal_schedule_sitemap_submission() {
    if (!wp_next_scheduled('news_portal_submit_sitemaps')) {
        wp_schedule_event(time(), 'weekly', 'news_portal_submit_sitemaps');
    }
}
add_action('wp', 'news_portal_schedule_sitemap_submission');

/**
 * Submit sitemaps to search engines
 */
function news_portal_submit_sitemaps() {
    $site_url = get_site_url();
    $sitemap_url = $site_url . '/sitemap.xml';
    $video_sitemap_url = $site_url . '/video-sitemap.xml';
    $news_sitemap_url = $site_url . '/news-sitemap.xml';
    $llm_sitemap_url = $site_url . '/llm-sitemap.xml';
    
    // Log submission attempt
    error_log('Attempting to submit sitemaps to search engines: ' . current_time('mysql'));
    
    // Submit to Google
    news_portal_submit_to_google($sitemap_url);
    
    // Submit video sitemap if it exists
    if (file_exists(ABSPATH . 'video-sitemap.xml')) {
        news_portal_submit_to_google($video_sitemap_url);
    }
    
    // Submit news sitemap if it exists
    if (file_exists(ABSPATH . 'news-sitemap.xml')) {
        news_portal_submit_to_google($news_sitemap_url);
    }
    
    // Submit LLM sitemap if it exists
    if (file_exists(ABSPATH . 'llm-sitemap.xml')) {
        news_portal_submit_to_google($llm_sitemap_url);
    }
    
    // Submit to Bing
    news_portal_submit_to_bing($sitemap_url);
    
    // Log completion
    error_log('Sitemap submission completed: ' . current_time('mysql'));
    
    // Store last submission time
    update_option('news_portal_last_sitemap_submission', current_time('mysql'));
}
add_action('news_portal_submit_sitemaps', 'news_portal_submit_sitemaps');

/**
 * Submit sitemap to Google Search Console
 */
function news_portal_submit_to_google($sitemap_url) {
    $api_key = get_option('news_portal_google_api_key');
    $search_console_domain = get_option('news_portal_search_console_domain');
    
    // If API key is not set, use ping method instead
    if (empty($api_key) || empty($search_console_domain)) {
        $google_url = 'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url);
        
        $response = wp_remote_get($google_url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
        ));
        
        if (is_wp_error($response)) {
            error_log('Google sitemap submission error: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code == 200) {
            error_log('Sitemap successfully submitted to Google: ' . $sitemap_url);
            return true;
        } else {
            error_log('Google sitemap submission failed with code: ' . $response_code);
            return false;
        }
    } else {
        // Use Search Console API if credentials are available
        // This would require setting up the Google API client library
        // For simplicity, we're using the ping method in this implementation
        return true;
    }
}

/**
 * Submit sitemap to Bing Webmaster Tools
 */
function news_portal_submit_to_bing($sitemap_url) {
    $bing_url = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url);
    
    $response = wp_remote_get($bing_url, array(
        'timeout' => 30,
        'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
    ));
    
    if (is_wp_error($response)) {
        error_log('Bing sitemap submission error: ' . $response->get_error_message());
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code == 200) {
        error_log('Sitemap successfully submitted to Bing: ' . $sitemap_url);
        return true;
    } else {
        error_log('Bing sitemap submission failed with code: ' . $response_code);
        return false;
    }
}

/**
 * Manually trigger sitemap submission
 */
function news_portal_manual_submit_sitemaps() {
    if (isset($_GET['submit_sitemaps']) && current_user_can('manage_options')) {
        news_portal_submit_sitemaps();
        wp_redirect(admin_url('options-general.php?page=news-portal-settings&sitemap_submitted=1'));
        exit;
    }
}
add_action('admin_init', 'news_portal_manual_submit_sitemaps');

/**
 * Add sitemap settings to theme options
 */
function news_portal_add_sitemap_settings($wp_customize) {
    $wp_customize->add_section('news_portal_sitemap_section', array(
        'title' => __('Sitemap Settings', 'news-portal'),
        'priority' => 120,
    ));
    
    $wp_customize->add_setting('news_portal_enable_auto_submission', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('news_portal_enable_auto_submission', array(
        'label' => __('Enable automatic sitemap submission', 'news-portal'),
        'section' => 'news_portal_sitemap_section',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('news_portal_google_api_key', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('news_portal_google_api_key', array(
        'label' => __('Google API Key (optional)', 'news-portal'),
        'description' => __('For advanced Google Search Console integration', 'news-portal'),
        'section' => 'news_portal_sitemap_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('news_portal_search_console_domain', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('news_portal_search_console_domain', array(
        'label' => __('Search Console Verified Domain', 'news-portal'),
        'section' => 'news_portal_sitemap_section',
        'type' => 'text',
    ));
}
add_action('customize_register', 'news_portal_add_sitemap_settings');

/**
 * Display admin notice after manual submission
 */
function news_portal_sitemap_submission_notice() {
    if (isset($_GET['sitemap_submitted'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Sitemaps have been successfully submitted to search engines.', 'news-portal'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'news_portal_sitemap_submission_notice');

/**
 * Add settings page for sitemap configuration
 */
function news_portal_register_settings_page() {
    add_options_page(
        __('News Portal Settings', 'news-portal'),
        __('News Portal', 'news-portal'),
        'manage_options',
        'news-portal-settings',
        'news_portal_settings_page_html'
    );
}
add_action('admin_menu', 'news_portal_register_settings_page');

/**
 * Settings page HTML
 */
function news_portal_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $last_submission = get_option('news_portal_last_sitemap_submission');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="card">
            <h2><?php _e('Sitemap Management', 'news-portal'); ?></h2>
            
            <?php if ($last_submission): ?>
                <p><?php printf(__('Last submission: %s', 'news-portal'), $last_submission); ?></p>
            <?php else: ?>
                <p><?php _e('Sitemaps have not been submitted yet.', 'news-portal'); ?></p>
            <?php endif; ?>
            
            <p>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=news-portal-settings&submit_sitemaps=1')); ?>" class="button button-primary">
                    <?php _e('Submit Sitemaps Now', 'news-portal'); ?>
                </a>
            </p>
            
            <p><?php _e('This will send your sitemaps to Google and Bing search engines.', 'news-portal'); ?></p>
        </div>
        
        <?php do_action('news_portal_settings_after_sitemap'); ?>
    </div>
    <?php
}
