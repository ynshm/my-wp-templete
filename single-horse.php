<?php
/**
 * 競走馬詳細ページのテンプレート
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
            $horse_birth = get_post_meta(get_the_ID(), '_horse_birth', true);
            $horse_gender = get_post_meta(get_the_ID(), '_horse_gender', true);
            $horse_color = get_post_meta(get_the_ID(), '_horse_color', true);
            $horse_sire = get_post_meta(get_the_ID(), '_horse_sire', true);
            $horse_dam = get_post_meta(get_the_ID(), '_horse_dam', true);
            $horse_trainer = get_post_meta(get_the_ID(), '_horse_trainer', true);
            $horse_owner = get_post_meta(get_the_ID(), '_horse_owner', true);
            $horse_wins = get_post_meta(get_the_ID(), '_horse_wins', true);
            $horse_places = get_post_meta(get_the_ID(), '_horse_places', true);

            // 血統タクソノミーの取得
            $bloodlines = get_the_terms(get_the_ID(), 'bloodline');
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('horse-single'); ?>>

            <?php news_portal_breadcrumb(); ?>

            <header class="entry-header horse-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <div class="horse-basic-info">
                    <?php if ($horse_gender) : ?>
                        <span class="horse-gender"><?php echo esc_html($horse_gender); ?></span>
                    <?php endif; ?>

                    <?php if ($horse_birth) : ?>
                        <span class="horse-age">
                            <?php
                            $birth_year = date('Y', strtotime($horse_birth));
                            $current_year = date('Y');
                            $age = $current_year - $birth_year;
                            echo esc_html($age) . '歳';
                            ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($horse_color) : ?>
                        <span class="horse-color"><?php echo esc_html($horse_color); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <div class="horse-content-wrapper">
                <div class="horse-sidebar">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="horse-photo">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="horse-stats-card">
                        <h3>戦績</h3>
                        <?php if ($horse_places && $horse_wins) : ?>
                            <div class="horse-record">
                                <div class="stat-item">
                                    <span class="stat-label">出走</span>
                                    <span class="stat-value"><?php echo esc_html($horse_places); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">勝利</span>
                                    <span class="stat-value highlight"><?php echo esc_html($horse_wins); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">勝率</span>
                                    <span class="stat-value">
                                        <?php echo number_format(($horse_wins / $horse_places) * 100, 1); ?>%
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="horse-main-content">
                    <div class="horse-profile">
                        <h2>プロフィール</h2>
                        <table class="horse-profile-table">
                            <?php if ($horse_birth) : ?>
                            <tr>
                                <th>生年月日</th>
                                <td><?php echo esc_html(date('Y年m月d日', strtotime($horse_birth))); ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if ($horse_sire) : ?>
                            <tr>
                                <th>父</th>
                                <td><?php echo esc_html($horse_sire); ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if ($horse_dam) : ?>
                            <tr>
                                <th>母</th>
                                <td><?php echo esc_html($horse_dam); ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if ($bloodlines && !is_wp_error($bloodlines)) : ?>
                            <tr>
                                <th>血統</th>
                                <td><?php echo esc_html($bloodlines[0]->name); ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if ($horse_trainer) : ?>
                            <tr>
                                <th>調教師</th>
                                <td><?php echo esc_html($horse_trainer); ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if ($horse_owner) : ?>
                            <tr>
                                <th>馬主</th>
                                <td><?php echo esc_html($horse_owner); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="horse-description">
                        <h2>詳細情報</h2>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>

        </article>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
