
<?php get_header(); ?>

<section id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php if (have_posts()) : ?>

      <header class="page-header">
        <h1 class="page-title">
          <?php
          /* translators: %s: search query. */
          printf(esc_html__('Search Results for: %s', 'custom-theme'), '<span>' . get_search_query() . '</span>');
          ?>
        </h1>
      </header>

      <?php
      /* Start the Loop */
      while (have_posts()) :
        the_post();

        /**
         * Run the loop for the search to output the results.
         * If you want to overload this in a child theme then include a file
         * called content-search.php and that will be used instead.
         */
        get_template_part('template-parts/content', 'search');

      endwhile;

      the_posts_navigation();

    else :

      get_template_part('template-parts/content', 'none');

    endif;
    ?>

  </main>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php
/**
 * 検索結果テンプレート
 *
 * @package News_Portal
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <?php if (have_posts()) : ?>

                    <header class="page-header">
                        <h1 class="page-title">
                            <?php
                            /* translators: %s: search query. */
                            printf(esc_html__('Search Results for: %s', 'news-portal'), '<span>' . get_search_query() . '</span>');
                            ?>
                        </h1>
                    </header><!-- .page-header -->

                    <?php
                    /* Start the Loop */
                    while (have_posts()) :
                        the_post();

                        /**
                         * Run the loop for the search to output the results.
                         * If you want to overload this in a child theme then include a file
                         * called content-search.php and that will be used instead.
                         */
                        get_template_part('template-parts/content', 'search');

                    endwhile;

                    the_posts_navigation();

                else :

                    get_template_part('template-parts/content', 'none');

                endif;
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
