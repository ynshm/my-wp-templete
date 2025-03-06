
<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <?php
    while (have_posts()) :
      the_post();
      get_template_part('template-parts/content', 'page');

      // If comments are open or we have at least one comment, load up the comment template.
      if (comments_open() || get_comments_number()) :
        comments_template();
      endif;
    endwhile; // End of the loop.
    ?>
  </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php
/**
 * 固定ページテンプレート
 *
 * @package News_Portal
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <?php
                while (have_posts()) :
                    the_post();

                    get_template_part('template-parts/content', 'page');

                    // コメント表示
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;

                endwhile;
                ?>
            </div>
            <div class="col-md-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main><!-- #main -->

<?php
get_footer();
