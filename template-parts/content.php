
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
