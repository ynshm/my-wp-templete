
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
<?php
/**
 * 個別投稿表示用のテンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        // カテゴリー表示
        $categories = get_the_category();
        if (!empty($categories)) :
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="entry-category">' . esc_html($categories[0]->name) . '</a>';
        endif;
        
        the_title('<h1 class="entry-title">', '</h1>');
        ?>

        <div class="entry-meta">
            <span class="posted-on">
                <i class="far fa-calendar-alt" aria-hidden="true"></i>
                <time class="entry-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                    <?php echo get_the_date(); ?>
                </time>
            </span>
            <span class="byline">
                <i class="far fa-user" aria-hidden="true"></i>
                <?php the_author_posts_link(); ?>
            </span>
            <?php if (comments_open()) : ?>
            <span class="comments-link">
                <i class="far fa-comment" aria-hidden="true"></i>
                <?php comments_popup_link('0', '1', '%'); ?>
            </span>
            <?php endif; ?>
            <span class="reading-time">
                <i class="far fa-clock" aria-hidden="true"></i>
                <?php
                $content = get_post_field('post_content', get_the_ID());
                $word_count = str_word_count(strip_tags($content));
                $reading_time = ceil($word_count / 200); // 1分あたり200単語と仮定
                printf(_n('%d min read', '%d min read', $reading_time, 'news-portal'), $reading_time);
                ?>
            </span>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->
    
    <!-- おすすめ記事 (記事上部) -->
    <div class="recommended-posts">
        <h4 class="recommended-title"><?php esc_html_e('おすすめの記事', 'news-portal'); ?></h4>
        <div class="recommended-posts-container">
            <?php
            // 同じカテゴリーの人気記事3件を取得
            $current_post_id = get_the_ID();
            $current_categories = get_the_category();
            if (!empty($current_categories)) {
                $category_ids = array();
                foreach ($current_categories as $category) {
                    $category_ids[] = $category->term_id;
                }
                
                $recommended_args = array(
                    'posts_per_page' => 3,
                    'post__not_in' => array($current_post_id),
                    'category__in' => $category_ids,
                    'orderby' => 'comment_count', // コメント数で並べ替え
                    'ignore_sticky_posts' => true,
                );
                
                $recommended_query = new WP_Query($recommended_args);
                
                if ($recommended_query->have_posts()) :
                    while ($recommended_query->have_posts()) : $recommended_query->the_post();
                    ?>
                    <article class="recommended-post">
                        <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="recommended-thumbnail">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </a>
                        <?php endif; ?>
                        <div class="recommended-content">
                            <h5 class="recommended-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h5>
                            <div class="recommended-meta">
                                <span class="recommended-date"><?php echo get_the_date(); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php
                    endwhile;
                endif;
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>

    <?php news_portal_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
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
        
        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'news-portal'),
                'after'  => '</div>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php
        // タグの表示
        $tags_list = get_the_tag_list('<div class="tags-links"><i class="fas fa-tags" aria-hidden="true"></i> ', ', ', '</div>');
        if ($tags_list) {
            echo $tags_list;
        }
        ?>
        
        <!-- ソーシャルシェアボタン -->
        <div class="social-share">
            <div class="social-share-title"><?php esc_html_e('Share this post:', 'news-portal'); ?></div>
            <a href="https://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" class="share-button share-twitter" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-twitter" aria-hidden="true"></i>
                <span class="screen-reader-text"><?php esc_html_e('Share on Twitter', 'news-portal'); ?></span>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" class="share-button share-facebook" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                <span class="screen-reader-text"><?php esc_html_e('Share on Facebook', 'news-portal'); ?></span>
            </a>
            <a href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php the_post_thumbnail_url('large'); ?>&description=<?php the_title(); ?>" class="share-button share-pinterest" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-pinterest-p" aria-hidden="true"></i>
                <span class="screen-reader-text"><?php esc_html_e('Share on Pinterest', 'news-portal'); ?></span>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" class="share-button share-linkedin" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                <span class="screen-reader-text"><?php esc_html_e('Share on LinkedIn', 'news-portal'); ?></span>
            </a>
        </div>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

<?php
// 著者情報ボックスの表示
if (get_theme_mod('single_post_author_box', true)) {
    news_portal_author_box();
}

// 関連記事の表示
if (get_theme_mod('single_post_related', true)) {
    news_portal_related_posts();
}

// 前後の投稿ナビゲーション
if (get_theme_mod('single_post_navigation', true)) :
?>
<nav class="navigation post-navigation" aria-label="<?php esc_attr_e('Posts', 'news-portal'); ?>">
    <h2 class="screen-reader-text"><?php esc_html_e('Post navigation', 'news-portal'); ?></h2>
    <div class="nav-links">
        <?php
        $prev_post = get_previous_post();
        if (!empty($prev_post)) :
        ?>
        <div class="nav-previous">
            <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" rel="prev">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
                <span><?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
            </a>
        </div>
        <?php endif; ?>
        
        <?php
        $next_post = get_next_post();
        if (!empty($next_post)) :
        ?>
        <div class="nav-next">
            <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" rel="next">
                <span><?php echo esc_html(get_the_title($next_post->ID)); ?></span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</nav>
<?php endif; ?>
