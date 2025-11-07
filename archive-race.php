<?php
/**
 * レース一覧アーカイブのテンプレート
 *
 * @package Horse_Racing_Portal
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                if (is_tax('racecourse')) {
                    single_term_title();
                    echo ' のレース一覧';
                } elseif (is_tax('race_grade')) {
                    single_term_title();
                    echo ' レース一覧';
                } else {
                    echo 'レース一覧';
                }
                ?>
            </h1>
        </header>

        <div class="race-filter-bar">
            <?php
            // 競馬場フィルター
            $racecourses = get_terms(array(
                'taxonomy' => 'racecourse',
                'hide_empty' => false,
            ));

            if (!is_wp_error($racecourses) && !empty($racecourses)) :
            ?>
                <div class="filter-group">
                    <label>競馬場で絞り込み:</label>
                    <select id="racecourse-filter" onchange="location = this.value;">
                        <option value="<?php echo get_post_type_archive_link('race'); ?>">すべて</option>
                        <?php foreach ($racecourses as $racecourse) : ?>
                            <option value="<?php echo get_term_link($racecourse); ?>"
                                <?php selected(is_tax('racecourse', $racecourse->slug)); ?>>
                                <?php echo esc_html($racecourse->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <?php if (have_posts()) : ?>

            <div class="race-archive-grid">
                <?php
                while (have_posts()) :
                    the_post();

                    // カスタムフィールド取得
                    $race_date = get_post_meta(get_the_ID(), '_race_date', true);
                    $race_time = get_post_meta(get_the_ID(), '_race_time', true);
                    $race_distance = get_post_meta(get_the_ID(), '_race_distance', true);
                    $race_surface = get_post_meta(get_the_ID(), '_race_surface', true);
                    $race_prize = get_post_meta(get_the_ID(), '_race_prize', true);

                    // タクソノミー取得
                    $racecourses = get_the_terms(get_the_ID(), 'racecourse');
                    $race_grades = get_the_terms(get_the_ID(), 'race_grade');
                ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('race-card'); ?>>
                        <?php if ($race_grades && !is_wp_error($race_grades)) : ?>
                            <div class="race-grade-label">
                                <?php echo esc_html($race_grades[0]->name); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="race-card-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="race-card-content">
                            <h2 class="race-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="race-card-meta">
                                <?php if ($racecourses && !is_wp_error($racecourses)) : ?>
                                    <span class="race-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo esc_html($racecourses[0]->name); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($race_date) : ?>
                                    <span class="race-date">
                                        <i class="far fa-calendar"></i>
                                        <?php echo esc_html(date('m/d', strtotime($race_date))); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($race_time) : ?>
                                    <span class="race-time">
                                        <i class="far fa-clock"></i>
                                        <?php echo esc_html($race_time); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="race-card-details">
                                <?php if ($race_distance && $race_surface) : ?>
                                    <span><?php echo esc_html($race_surface . $race_distance); ?>m</span>
                                <?php endif; ?>

                                <?php if ($race_prize) : ?>
                                    <span class="race-prize">賞金 <?php echo number_format($race_prize); ?>万円</span>
                                <?php endif; ?>
                            </div>

                            <div class="race-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="race-card-link">
                                詳細を見る <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>

            <?php
            // ページネーション
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('&laquo; 前へ', 'news-portal'),
                'next_text' => __('次へ &raquo;', 'news-portal'),
            ));
            ?>

        <?php else : ?>

            <p>現在、表示できるレースがありません。</p>

        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
