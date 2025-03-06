
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <?php
    if (is_singular()) :
      the_title('<h1 class="entry-title">', '</h1>');
    else :
      the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
    endif;

    if ('post' === get_post_type()) :
      ?>
      <div class="entry-meta">
        <?php
        printf(
          '<span class="posted-on">%1$s</span><span class="byline"> %2$s</span>',
          esc_html(get_the_date()),
          esc_html(get_the_author())
        );
        ?>
      </div>
    <?php endif; ?>
  </header>

  <?php if (has_post_thumbnail()) : ?>
    <div class="post-thumbnail">
      <?php the_post_thumbnail('large'); ?>
    </div>
  <?php endif; ?>

  <div class="entry-content">
    <?php
    if (is_singular()) :
      the_content();
    else :
      the_excerpt();
      ?>
      <p><a href="<?php echo esc_url(get_permalink()); ?>" class="more-link"><?php esc_html_e('Continue reading &rarr;', 'custom-theme'); ?></a></p>
    <?php
    endif;
    ?>
  </div>

  <footer class="entry-footer">
    <?php
    // Display categories and tags if available
    $categories_list = get_the_category_list(', ');
    if ($categories_list) {
      printf('<span class="cat-links">%s</span>', $categories_list);
    }
    
    $tags_list = get_the_tag_list('', ', ');
    if ($tags_list) {
      printf('<span class="tags-links">%s</span>', $tags_list);
    }
    ?>
  </footer>
</article>
<?php
/**
 * 投稿一覧用コンテンツテンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <?php
                news_portal_posted_on();
                news_portal_posted_by();
                ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php news_portal_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'news-portal'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );
        else :
            the_excerpt();
            ?>
            <div class="read-more">
                <a href="<?php the_permalink(); ?>" class="more-link">
                    <?php esc_html_e('Read More', 'news-portal'); ?>
                </a>
            </div>
        <?php
        endif;

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'news-portal'),
                'after'  => '</div>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php news_portal_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
