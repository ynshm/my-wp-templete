<?php
/**
 * レーススケジュール表示ウィジェット
 *
 * @package Horse_Racing_Portal
 */

class Race_Schedule_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'race_schedule_widget',
            __('レーススケジュール', 'news-portal'),
            array('description' => __('今後のレース開催スケジュールを表示します', 'news-portal'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('今週のレース', 'news-portal');
        $num_races = !empty($instance['num_races']) ? absint($instance['num_races']) : 5;

        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];

        // 今日以降のレースを取得
        $today = date('Y-m-d');
        $races = new WP_Query(array(
            'post_type' => 'race',
            'posts_per_page' => $num_races,
            'meta_key' => '_race_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => '_race_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
            ),
        ));

        if ($races->have_posts()) :
        ?>
            <div class="race-schedule-widget">
                <?php while ($races->have_posts()) : $races->the_post(); ?>
                    <?php
                    $race_date = get_post_meta(get_the_ID(), '_race_date', true);
                    $race_time = get_post_meta(get_the_ID(), '_race_time', true);
                    $racecourses = get_the_terms(get_the_ID(), 'racecourse');
                    $race_grades = get_the_terms(get_the_ID(), 'race_grade');
                    ?>
                    <div class="schedule-item">
                        <?php if ($race_grades && !is_wp_error($race_grades)) : ?>
                            <span class="schedule-grade"><?php echo esc_html($race_grades[0]->name); ?></span>
                        <?php endif; ?>

                        <h4 class="schedule-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>

                        <div class="schedule-meta">
                            <?php if ($race_date) : ?>
                                <span class="schedule-date">
                                    <i class="far fa-calendar"></i>
                                    <?php echo esc_html(date('m月d日', strtotime($race_date))); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($race_time) : ?>
                                <span class="schedule-time">
                                    <i class="far fa-clock"></i>
                                    <?php echo esc_html($race_time); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($racecourses && !is_wp_error($racecourses)) : ?>
                            <div class="schedule-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($racecourses[0]->name); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<p>現在、予定されているレースはありません。</p>';
        endif;

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('今週のレース', 'news-portal');
        $num_races = !empty($instance['num_races']) ? absint($instance['num_races']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('タイトル:', 'news-portal'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_races')); ?>">
                <?php esc_attr_e('表示レース数:', 'news-portal'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('num_races')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('num_races')); ?>" type="number"
                   step="1" min="1" value="<?php echo esc_attr($num_races); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['num_races'] = (!empty($new_instance['num_races'])) ? absint($new_instance['num_races']) : 5;
        return $instance;
    }
}

// ウィジェットを登録
function register_race_schedule_widget() {
    register_widget('Race_Schedule_Widget');
}
add_action('widgets_init', 'register_race_schedule_widget');
