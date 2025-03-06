
<?php
/**
 * Video Sitemap Generator
 * Creates and manages video sitemaps for search engines
 *
 * @package News_Portal
 */

/**
 * Schedule video sitemap generation
 */
function news_portal_schedule_video_sitemap() {
    if (!wp_next_scheduled('news_portal_generate_video_sitemap')) {
        wp_schedule_event(time(), 'daily', 'news_portal_generate_video_sitemap');
    }
}
add_action('wp', 'news_portal_schedule_video_sitemap');

/**
 * Generate video sitemap
 */
function news_portal_generate_video_sitemap() {
    $post_types = array('post', 'page');
    
    // Add custom post types that may contain videos
    if (post_type_exists('video')) {
        $post_types[] = 'video';
    }
    
    // Prepare XML content
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
           'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";
    
    // Get posts with potential videos
    $args = array(
        'post_type' => $post_types,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Extract video information
            $videos = news_portal_extract_videos(get_the_content());
            
            if (!empty($videos)) {
                $post_url = get_permalink();
                
                // Process each video found in the post
                foreach ($videos as $video) {
                    $xml .= "\t<url>\n";
                    $xml .= "\t\t<loc>" . esc_url($post_url) . "</loc>\n";
                    $xml .= "\t\t<video:video>\n";
                    $xml .= "\t\t\t<video:thumbnail_loc>" . esc_url($video['thumbnail']) . "</video:thumbnail_loc>\n";
                    $xml .= "\t\t\t<video:title>" . esc_html($video['title']) . "</video:title>\n";
                    $xml .= "\t\t\t<video:description>" . esc_html($video['description']) . "</video:description>\n";
                    
                    if (!empty($video['player_loc'])) {
                        $xml .= "\t\t\t<video:player_loc>" . esc_url($video['player_loc']) . "</video:player_loc>\n";
                    } else if (!empty($video['content_loc'])) {
                        $xml .= "\t\t\t<video:content_loc>" . esc_url($video['content_loc']) . "</video:content_loc>\n";
                    }
                    
                    // Add duration if available
                    if (!empty($video['duration'])) {
                        $xml .= "\t\t\t<video:duration>" . intval($video['duration']) . "</video:duration>\n";
                    }
                    
                    // Add publication date
                    $xml .= "\t\t\t<video:publication_date>" . get_the_date('c') . "</video:publication_date>\n";
                    
                    // Add tags if available
                    $tags = get_the_tags();
                    if ($tags) {
                        foreach ($tags as $tag) {
                            $xml .= "\t\t\t<video:tag>" . esc_html($tag->name) . "</video:tag>\n";
                        }
                    }
                    
                    // Close video tag
                    $xml .= "\t\t</video:video>\n";
                    $xml .= "\t</url>\n";
                }
            }
        }
    }
    wp_reset_postdata();
    
    // Close XML
    $xml .= '</urlset>';
    
    // Save sitemap file
    file_put_contents(ABSPATH . 'video-sitemap.xml', $xml);
    
    // Update generation timestamp
    update_option('news_portal_video_sitemap_updated', current_time('mysql'));
}
add_action('news_portal_generate_video_sitemap', 'news_portal_generate_video_sitemap');

/**
 * Extract videos from content
 */
function news_portal_extract_videos($content) {
    $videos = array();
    
    // Extract YouTube embeds
    if (preg_match_all('/<iframe.*?src="https:\/\/www\.youtube\.com\/embed\/([^"]+)".*?><\/iframe>/i', $content, $youtube_matches)) {
        foreach ($youtube_matches[1] as $index => $youtube_id) {
            // Clean up video ID (remove parameters)
            $youtube_id = strtok($youtube_id, '?');
            
            $videos[] = array(
                'title' => get_the_title(),
                'description' => wp_trim_words(get_the_excerpt(), 30, '...'),
                'thumbnail' => 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg',
                'player_loc' => 'https://www.youtube.com/embed/' . $youtube_id,
                'duration' => '', // YouTube doesn't provide duration in embed code
            );
        }
    }
    
    // Extract Vimeo embeds
    if (preg_match_all('/<iframe.*?src="https:\/\/player\.vimeo\.com\/video\/([^"]+)".*?><\/iframe>/i', $content, $vimeo_matches)) {
        foreach ($vimeo_matches[1] as $index => $vimeo_id) {
            // Clean up video ID (remove parameters)
            $vimeo_id = strtok($vimeo_id, '?');
            
            // For Vimeo, we would ideally fetch the thumbnail and duration via API
            // For simplicity, we're just setting default values here
            $videos[] = array(
                'title' => get_the_title(),
                'description' => wp_trim_words(get_the_excerpt(), 30, '...'),
                'thumbnail' => '', // Ideally would be fetched from Vimeo API
                'player_loc' => 'https://player.vimeo.com/video/' . $vimeo_id,
                'duration' => '', // Would be fetched from Vimeo API
            );
        }
    }
    
    // Extract HTML5 video tags
    if (preg_match_all('/<video.*?>(.*?)<\/video>/is', $content, $video_matches)) {
        foreach ($video_matches[0] as $index => $video_tag) {
            // Extract poster attribute for thumbnail
            $thumbnail = '';
            if (preg_match('/poster="([^"]+)"/', $video_tag, $poster_match)) {
                $thumbnail = $poster_match[1];
            }
            
            // Extract source
            $content_loc = '';
            if (preg_match('/<source.*?src="([^"]+)"/', $video_tag, $source_match)) {
                $content_loc = $source_match[1];
            }
            
            $videos[] = array(
                'title' => get_the_title(),
                'description' => wp_trim_words(get_the_excerpt(), 30, '...'),
                'thumbnail' => $thumbnail,
                'content_loc' => $content_loc,
                'duration' => '', // HTML5 video doesn't provide duration in markup
            );
        }
    }
    
    // Extract videos from custom meta fields if available
    $video_url = get_post_meta(get_the_ID(), '_video_url', true);
    $video_thumbnail = get_post_meta(get_the_ID(), '_video_thumbnail', true);
    $video_duration = get_post_meta(get_the_ID(), '_video_duration', true);
    
    if (!empty($video_url)) {
        $videos[] = array(
            'title' => get_the_title(),
            'description' => wp_trim_words(get_the_excerpt(), 30, '...'),
            'thumbnail' => !empty($video_thumbnail) ? $video_thumbnail : '',
            'content_loc' => $video_url,
            'duration' => !empty($video_duration) ? $video_duration : '',
        );
    }
    
    return $videos;
}

/**
 * Manually generate video sitemap
 */
function news_portal_manual_generate_video_sitemap() {
    if (isset($_GET['generate_video_sitemap']) && current_user_can('manage_options')) {
        news_portal_generate_video_sitemap();
        wp_redirect(admin_url('options-general.php?page=news-portal-settings&video_sitemap_generated=1'));
        exit;
    }
}
add_action('admin_init', 'news_portal_manual_generate_video_sitemap');

/**
 * Add video sitemap generation option to settings page
 */
function news_portal_add_video_sitemap_option() {
    $last_update = get_option('news_portal_video_sitemap_updated');
    ?>
    <div class="card">
        <h2><?php _e('Video Sitemap', 'news-portal'); ?></h2>
        
        <?php if ($last_update): ?>
            <p><?php printf(__('Last generated: %s', 'news-portal'), $last_update); ?></p>
        <?php else: ?>
            <p><?php _e('Video sitemap has not been generated yet.', 'news-portal'); ?></p>
        <?php endif; ?>
        
        <p>
            <a href="<?php echo esc_url(admin_url('options-general.php?page=news-portal-settings&generate_video_sitemap=1')); ?>" class="button">
                <?php _e('Generate Video Sitemap Now', 'news-portal'); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action('news_portal_settings_after_sitemap', 'news_portal_add_video_sitemap_option');

/**
 * Display admin notice after sitemap generation
 */
function news_portal_video_sitemap_notice() {
    if (isset($_GET['video_sitemap_generated'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Video sitemap has been successfully generated.', 'news-portal'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'news_portal_video_sitemap_notice');
