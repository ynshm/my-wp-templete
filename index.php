
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
        <?php elseif ( is_home() && is_active_sidebar('homepage-top') ) : ?>
            <div class="homepage-top-widgets">
                <?php dynamic_sidebar('homepage-top'); ?>
            </div>
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
