<?php
/**
 * レース情報詳細ページのテンプレート
 *
 * @package Horse_Racing_Portal
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();

            // カスタムフィールドの取得
            $race_date = get_post_meta(get_the_ID(), '_race_date', true);
            $race_time = get_post_meta(get_the_ID(), '_race_time', true);
            $race_distance = get_post_meta(get_the_ID(), '_race_distance', true);
            $race_surface = get_post_meta(get_the_ID(), '_race_surface', true);
            $race_prize = get_post_meta(get_the_ID(), '_race_prize', true);
            $race_number = get_post_meta(get_the_ID(), '_race_number', true);
            $race_entries = get_post_meta(get_the_ID(), '_race_entries', true);

            // タクソノミーの取得
            $racecourses = get_the_terms(get_the_ID(), 'racecourse');
            $race_grades = get_the_terms(get_the_ID(), 'race_grade');
            $track_conditions = get_the_terms(get_the_ID(), 'track_condition');
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('race-single'); ?>>

            <?php news_portal_breadcrumb(); ?>

            <header class="entry-header race-header">
                <?php if ($race_grades && !is_wp_error($race_grades)) : ?>
                    <div class="race-grade-badge">
                        <?php echo esc_html($race_grades[0]->name); ?>
                    </div>
                <?php endif; ?>

                <h1 class="entry-title"><?php the_title(); ?></h1>

                <div class="race-meta-info">
                    <?php if ($racecourses && !is_wp_error($racecourses)) : ?>
                        <span class="race-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($racecourses[0]->name); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($race_date) : ?>
                        <span class="race-date">
                            <i class="far fa-calendar"></i>
                            <?php echo esc_html(date('Y年m月d日', strtotime($race_date))); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($race_time) : ?>
                        <span class="race-time">
                            <i class="far fa-clock"></i>
                            <?php echo esc_html($race_time); ?>発走
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="race-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="race-details-grid">
                <div class="race-info-card">
                    <h2>レース情報</h2>
                    <table class="race-info-table">
                        <?php if ($race_distance) : ?>
                        <tr>
                            <th>距離</th>
                            <td><?php echo esc_html($race_distance); ?>m</td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($race_surface) : ?>
                        <tr>
                            <th>コース</th>
                            <td><?php echo esc_html($race_surface); ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($track_conditions && !is_wp_error($track_conditions)) : ?>
                        <tr>
                            <th>馬場状態</th>
                            <td><?php echo esc_html($track_conditions[0]->name); ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($race_prize) : ?>
                        <tr>
                            <th>1着賞金</th>
                            <td><?php echo number_format($race_prize); ?>万円</td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($race_number) : ?>
                        <tr>
                            <th>レース番号</th>
                            <td>第<?php echo esc_html($race_number); ?>レース</td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($race_entries) : ?>
                        <tr>
                            <th>出走頭数</th>
                            <td><?php echo esc_html($race_entries); ?>頭</td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="race-content">
                    <h2>レース解説</h2>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php
            // このレースに関連する予想記事を表示
            $predictions = new WP_Query(array(
                'post_type' => 'prediction',
                'meta_key' => '_prediction_race',
                'meta_value' => get_the_ID(),
                'posts_per_page' => 5,
            ));

            if ($predictions->have_posts()) :
            ?>
                <div class="related-predictions">
                    <h2>このレースの予想記事</h2>
                    <div class="predictions-list">
                        <?php while ($predictions->have_posts()) : $predictions->the_post(); ?>
                            <article class="prediction-item">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="prediction-meta">
                                    <?php
                                    $confidence = get_post_meta(get_the_ID(), '_prediction_confidence', true);
                                    if ($confidence) {
                                        echo '<span class="confidence-stars">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $confidence ? '★' : '☆';
                                        }
                                        echo '</span>';
                                    }
                                    ?>
                                    <span class="prediction-date"><?php echo get_the_date(); ?></span>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php
                wp_reset_postdata();
            endif;
            ?>

        </article>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
