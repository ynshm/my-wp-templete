
<?php
/**
 * メインテンプレートファイル
 *
 * @package News_Portal
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php if ( have_posts() ) : ?>

        <?php if ( is_home() && ! is_front_page() ) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            </header>
        <?php elseif ( is_home() ) : ?>
            <!-- ヒーローセクション -->
            <div class="hero-section">
                <div class="hero-slider">
                    <?php
                    // 最新記事5件を取得
                    $hero_args = array(
                        'posts_per_page' => 5,
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => true,
                    );
                    $hero_query = new WP_Query($hero_args);
                    
                    if ($hero_query->have_posts()) :
                        while ($hero_query->have_posts()) : $hero_query->the_post();
                    ?>
                        <div class="hero-slide">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="hero-image">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="hero-content">
                                <?php
                                // カテゴリー表示
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="hero-category">' . esc_html($categories[0]->name) . '</a>';
                                endif;
                                ?>
                                <h2 class="hero-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <div class="hero-meta">
                                    <span class="hero-date"><?php echo get_the_date(); ?></span>
                                </div>
                                <div class="hero-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></div>
                                <a href="<?php the_permalink(); ?>" class="hero-readmore"><?php esc_html_e('続きを読む', 'news-portal'); ?></a>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            
            <?php if (is_active_sidebar('homepage-top')) : ?>
                <div class="homepage-top-widgets">
                    <?php dynamic_sidebar('homepage-top'); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="posts-container" id="posts-container">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content', get_post_type() );
            endwhile;
            ?>
        </div>

        <?php if (get_theme_mod('enable_infinite_scroll', false)) : ?>
            <div class="infinite-scroll-status">
                <div class="infinite-scroll-request"></div>
                <p class="infinite-scroll-last"><?php esc_html_e('End of content', 'news-portal'); ?></p>
                <p class="infinite-scroll-error"><?php esc_html_e('No more pages to load', 'news-portal'); ?></p>
            </div>
            <div class="pagination">
                <?php
                // ブログページでないときのページネーション
                global $wp_query;
                $big = 999999999; // 任意の大きな数字
                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total' => $wp_query->max_num_pages,
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="pagination">
                <?php
                // ブログページでないときのページネーション
                global $wp_query;
                $big = 999999999; // 任意の大きな数字
                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total' => $wp_query->max_num_pages,
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <?php get_template_part( 'template-parts/content', 'none' ); ?>
    <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
?>
