
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
        <?php endif; ?>

        <div class="posts-container">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content', get_post_type() );
            endwhile;
            ?>
        </div>

        <?php
        the_posts_navigation(array(
            'prev_text' => '&larr; ' . __('Older posts', 'news-portal'),
            'next_text' => __('Newer posts', 'news-portal') . ' &rarr;',
        ));

    else :
        get_template_part( 'template-parts/content', 'none' );
    endif;
    ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
