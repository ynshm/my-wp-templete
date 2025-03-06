
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    <div class="entry-meta">
      <?php
      printf(
        '<span class="posted-on">%1$s</span><span class="byline"> %2$s</span>',
        esc_html(get_the_date()),
        esc_html(get_the_author())
      );
      ?>
    </div>
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

  <footer class="entry-footer">
    <?php
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
