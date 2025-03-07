
<?php
/**
 * Google News Sitemap Generator
 * Creates and manages news sitemaps for Google News inclusion
 *
 * @package News_Portal
 */

/**
 * Schedule news sitemap generation
 */
function news_portal_schedule_news_sitemap() {
    if (!wp_next_scheduled('news_portal_generate_news_sitemap')) {
        wp_schedule_event(time(), 'hourly', 'news_portal_generate_news_sitemap');
    }
}
add_action('wp', 'news_portal_schedule_news_sitemap');

/**
 * Generate Google News sitemap
 * Note: For Google News, articles must be published within the last 2 days
 */
function news_portal_generate_news_sitemap() {
    // Get news categories (or use all categories)
    $news_categories = get_option('news_portal_news_categories', array());
    
    // If no specific categories are set, use all
    if (empty($news_categories)) {
        $categories = get_categories(array('hide_empty' => true));
        $news_categories = wp_list_pluck($categories, 'term_id');
    }
    
    // Prepare XML content
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
           'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";
    
    // Get recent posts (last 2 days for Google News)
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 1000, // Google News limit
        'date_query' => array(
            array(
                'after' => '2 days ago',
            ),
        ),
        'category__in' => $news_categories,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Get publication name
            $publication_name = get_bloginfo('name');
            
            // Get publication language
            $language = get_locale();
            $language = substr($language, 0, 2); // Get first 2 characters (e.g., 'en' from 'en_US')
            
            // Get post categories for news genres
            $post_categories = get_the_category();
            $genres = array();
            
            foreach ($post_categories as $category) {
                // Map categories to Google News genres
                switch ($category->slug) {
                    case 'politics':
                    case 'world':
                    case 'nation':
                        $genres[] = 'PressRelease, Politics';
                        break;
                    case 'business':
                    case 'economy':
                    case 'finance':
                        $genres[] = 'PressRelease, Business';
                        break;
                    case 'technology':
                    case 'tech':
                    case 'science':
                        $genres[] = 'PressRelease, Technology';
                        break;
                    case 'entertainment':
                    case 'celebrity':
                        $genres[] = 'PressRelease, Entertainment';
                        break;
                    case 'sports':
                    case 'sport':
                        $genres[] = 'PressRelease, Sports';
                        break;
                    case 'health':
                    case 'wellness':
                        $genres[] = 'PressRelease, Health';
                        break;
                    default:
                        $genres[] = 'Blog';
                        break;
                }
            }
            
            // Remove duplicates
            $genres = array_unique($genres);
            
            // Format post date for Google News
            $post_date = get_the_date('Y-m-d\TH:i:sP');
            
            // Start URL entry
            $xml .= "\t<url>\n";
            $xml .= "\t\t<loc>" . get_permalink() . "</loc>\n";
            $xml .= "\t\t<news:news>\n";
            $xml .= "\t\t\t<news:publication>\n";
            $xml .= "\t\t\t\t<news:name>" . esc_html($publication_name) . "</news:name>\n";
            $xml .= "\t\t\t\t<news:language>" . esc_html($language) . "</news:language>\n";
            $xml .= "\t\t\t</news:publication>\n";
            $xml .= "\t\t\t<news:publication_date>" . esc_html($post_date) . "</news:publication_date>\n";
            $xml .= "\t\t\t<news:title>" . esc_html(get_the_title()) . "</news:title>\n";
            
            if (!empty($genres)) {
                $xml .= "\t\t\t<news:genres>" . esc_html(implode(', ', $genres)) . "</news:genres>\n";
            }
            
            $xml .= "\t\t</news:news>\n";
            $xml .= "\t</url>\n";
        }
    }
    wp_reset_postdata();
    
    // Close XML
    $xml .= '</urlset>';
    
    // Save sitemap file
    file_put_contents(ABSPATH . 'news-sitemap.xml', $xml);
    
    // Update generation timestamp
    update_option('news_portal_news_sitemap_updated', current_time('mysql'));
}
add_action('news_portal_generate_news_sitemap', 'news_portal_generate_news_sitemap');

/**
 * Manually generate news sitemap
 */
function news_portal_manual_generate_news_sitemap() {
    if (isset($_GET['generate_news_sitemap']) && current_user_can('manage_options')) {
        news_portal_generate_news_sitemap();
        wp_redirect(admin_url('options-general.php?page=news-portal-settings&news_sitemap_generated=1'));
        exit;
    }
}
add_action('admin_init', 'news_portal_manual_generate_news_sitemap');

/**
 * Add settings for Google News sitemap
 */
function news_portal_news_sitemap_settings($wp_customize) {
    $wp_customize->add_section('news_portal_news_sitemap_section', array(
        'title' => __('Google News Sitemap', 'news-portal'),
        'priority' => 125,
    ));
    
    $wp_customize->add_setting('news_portal_enable_news_sitemap', array(
        'default' => true,
        'sanitize_callback' => 'news_portal_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('news_portal_enable_news_sitemap', array(
        'label' => __('Enable Google News Sitemap', 'news-portal'),
        'section' => 'news_portal_news_sitemap_section',
        'type' => 'checkbox',
    ));
    
    // Get all categories for selection
    $categories = get_categories(array('hide_empty' => false));
    $cat_choices = array();
    
    foreach ($categories as $category) {
        $cat_choices[$category->term_id] = $category->name;
    }
    
    $wp_customize->add_setting('news_portal_news_categories', array(
        'default' => array(),
        'sanitize_callback' => 'news_portal_sanitize_multiple_checkboxes',
    ));
    
    $wp_customize->add_control(new WP_Customize_Multiple_Checkbox_Control($wp_customize, 'news_portal_news_categories', array(
        'label' => __('News Categories', 'news-portal'),
        'description' => __('Select categories to include in Google News sitemap. If none selected, all categories will be included.', 'news-portal'),
        'section' => 'news_portal_news_sitemap_section',
        'choices' => $cat_choices,
    )));
    
    $wp_customize->add_setting('news_portal_publication_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('news_portal_publication_name', array(
        'label' => __('Publication Name', 'news-portal'),
        'description' => __('Name of your publication for Google News.', 'news-portal'),
        'section' => 'news_portal_news_sitemap_section',
        'type' => 'text',
    ));
}
add_action('customize_register', 'news_portal_news_sitemap_settings');

/**
 * Multiple checkbox sanitization callback
 */
function news_portal_sanitize_multiple_checkboxes($values) {
    $multi_values = !is_array($values) ? explode(',', $values) : $values;
    return !empty($multi_values) ? array_map('sanitize_text_field', $multi_values) : array();
}

/**
 * Multiple checkbox control for customizer
 */
if (!class_exists('WP_Customize_Multiple_Checkbox_Control')) {
    class WP_Customize_Multiple_Checkbox_Control extends WP_Customize_Control {
        public $type = 'multiple-checkbox';
        
        public function render_content() {
            if (empty($this->choices)) {
                return;
            }
            
            if (!empty($this->label)) {
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
            }
            
            if (!empty($this->description)) {
                echo '<span class="description customize-control-description">' . esc_html($this->description) . '</span>';
            }
            
            $multi_values = !is_array($this->value()) ? explode(',', $this->value()) : $this->value();
            
            echo '<ul>';
            foreach ($this->choices as $value => $label) {
                echo '<li>';
                echo '<label>';
                echo '<input type="checkbox" value="' . esc_attr($value) . '" ' . checked(in_array($value, $multi_values), true, false) . ' />';
                echo esc_html($label);
                echo '</label>';
                echo '</li>';
            }
            echo '</ul>';
            
            echo '<input type="hidden" ' . $this->get_link() . ' value="' . esc_attr(implode(',', $multi_values)) . '" />';
            
            echo '<script>
                jQuery(document).ready(function($) {
                    $(".customize-control-multiple-checkbox input[type=\'checkbox\']").on("change", function() {
                        var checkbox_values = $(this).parents(".customize-control").find("input[type=\'checkbox\']:checked").map(
                            function() {
                                return this.value;
                            }
                        ).get().join(",");
                        
                        $(this).parents(".customize-control").find("input[type=hidden]").val(checkbox_values).trigger("change");
                    });
                });
            </script>';
                    $(".customize-control-multiple-checkbox input[type=\'checkbox\']").on("change", function() {
                        var checkbox_values = $(this).parents(".customize-control").find("input[type=\'checkbox\']:checked").map(function() {
                            return this.value;
                        }).get().join(",");
                        $(this).parents(".customize-control").find("input[type=hidden]").val(checkbox_values).trigger("change");
                    });
                });
            </script>';
        }
    }
}

/**
 * Add news sitemap generation option to settings page
 */
function news_portal_add_news_sitemap_option() {
    $last_update = get_option('news_portal_news_sitemap_updated');
    ?>
    <div class="card">
        <h2><?php _e('Google News Sitemap', 'news-portal'); ?></h2>
        
        <?php if ($last_update): ?>
            <p><?php printf(__('Last generated: %s', 'news-portal'), $last_update); ?></p>
        <?php else: ?>
            <p><?php _e('Google News sitemap has not been generated yet.', 'news-portal'); ?></p>
        <?php endif; ?>
        
        <p>
            <a href="<?php echo esc_url(admin_url('options-general.php?page=news-portal-settings&generate_news_sitemap=1')); ?>" class="button">
                <?php _e('Generate News Sitemap Now', 'news-portal'); ?>
            </a>
        </p>
        
        <p><?php _e('Note: Google News requires that you be an approved publisher before submission.', 'news-portal'); ?></p>
    </div>
    <?php
}
add_action('news_portal_settings_after_sitemap', 'news_portal_add_news_sitemap_option');

/**
 * Display admin notice after sitemap generation
 */
function news_portal_news_sitemap_notice() {
    if (isset($_GET['news_sitemap_generated'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Google News sitemap has been successfully generated.', 'news-portal'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'news_portal_news_sitemap_notice');
