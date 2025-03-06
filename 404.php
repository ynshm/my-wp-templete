
<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <section class="error-404 not-found">
      <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'custom-theme'); ?></h1>
      </header>

      <div class="page-content">
        <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'custom-theme'); ?></p>

        <?php get_search_form(); ?>

        <div class="widget widget_categories">
          <h2 class="widget-title"><?php esc_html_e('Most Used Categories', 'custom-theme'); ?></h2>
          <ul>
            <?php
            wp_list_categories(
              array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'show_count' => 1,
                'title_li'   => '',
                'number'     => 10,
              )
            );
            ?>
          </ul>
        </div>

        <?php
        /* translators: %1$s: smiley */
        $archive_content = '<p>' . sprintf(esc_html__('Try looking in the monthly archives. %1$s', 'custom-theme'), convert_smilies(':)')) . '</p>';
        the_widget('WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content");

        the_widget('WP_Widget_Tag_Cloud');
        ?>

      </div>
    </section>

  </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
