
<?php
/**
 * 投稿コンテンツ用テンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('news-article'); ?>>
    <div class="article-inner">
        <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail('medium_large', array('class' => 'featured-image')); ?>
            </a>
        </div>
        <?php endif; ?>

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
                // 投稿日
                echo '<span class="posted-on">';
                echo '<i class="fas fa-calendar"></i> ';
                echo '<time class="entry-date published">' . get_the_date() . '</time>';
                echo '</span>';

                // 著者
                echo '<span class="byline">';
                echo '<i class="fas fa-user"></i> ';
                echo '<span class="author vcard"><a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>';
                echo '</span>';

                // カテゴリー
                if (has_category()) :
                    echo '<span class="cat-links">';
                    echo '<i class="fas fa-folder"></i> ';
                    echo get_the_category_list(', ');
                    echo '</span>';
                endif;
                ?>
            </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                the_excerpt();
                echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Read More', 'news-portal') . ' &raquo;</a>';
            endif;
            ?>
        </div><!-- .entry-content -->

        <?php if (is_singular() && 'post' === get_post_type()) : ?>
        <footer class="entry-footer">
            <?php
            // タグ
            if (has_tag()) :
                echo '<div class="tags-links">';
                echo '<i class="fas fa-tags"></i> ';
                echo get_the_tag_list('', ', ');
                echo '</div>';
            endif;

            // 編集リンク
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. */
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
    </div><!-- .article-inner -->
</article><!-- #post-## -->
