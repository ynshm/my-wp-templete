
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
  </header>

  <?php if (has_post_thumbnail()) : ?>
    <div class="post-thumbnail">
      <?php the_post_thumbnail('large'); ?>
    </div>
  <?php endif; ?>

  <div class="entry-content">
    <?php
    the_content();

    wp_link_pages(
      array(
        'before' => '<div class="page-links">' . esc_html__('Pages:', 'custom-theme'),
        'after'  => '</div>',
      )
    );
    ?>
  </div>

  <?php if (get_edit_post_link()) : ?>
    <footer class="entry-footer">
      <?php
      edit_post_link(
        sprintf(
          /* translators: %s: Name of current post */
          esc_html__('Edit %s', 'custom-theme'),
          the_title('<span class="screen-reader-text">"', '"</span>', false)
        ),
        '<span class="edit-link">',
        '</span>'
      );
      ?>
    </footer>
  <?php endif; ?>
</article>
<?php
/**
 * 固定ページ用コンテンツテンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header><!-- .entry-header -->

    <?php news_portal_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'news-portal'),
                'after'  => '</div>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <?php if (get_edit_post_link()) : ?>
        <footer class="entry-footer">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Edit <span class="screen-reader-text">%s</span>', 'news-portal'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
