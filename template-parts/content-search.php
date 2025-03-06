
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

    <?php if ('post' === get_post_type()) : ?>
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
      <?php the_post_thumbnail('medium'); ?>
    </div>
  <?php endif; ?>

  <div class="entry-summary">
    <?php the_excerpt(); ?>
  </div>

  <footer class="entry-footer">
    <?php
    $categories_list = get_the_category_list(', ');
    if ($categories_list && custom_theme_categorized_blog()) {
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
 * 検索結果コンテンツテンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

        <?php if ('post' === get_post_type()) : ?>
        <div class="entry-meta">
            <?php
            news_portal_posted_on();
            news_portal_posted_by();
            ?>
        </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php news_portal_post_thumbnail(); ?>

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer">
        <?php news_portal_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
