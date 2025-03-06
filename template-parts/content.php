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

                // コメント数
                if (!post_password_required() && (comments_open() || get_comments_number())) {
                    echo '<span class="comments-link">';
                    echo '<i class="fas fa-comments"></i> ';
                    comments_popup_link(
                        __('0 Comments', 'news-portal'),
                        __('1 Comment', 'news-portal'),
                        __('% Comments', 'news-portal')
                    );
                    echo '</span>';
                }
                ?>
            </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                echo '<div class="entry-summary">';
                the_excerpt();
                echo '</div>';
                echo '<div class="read-more"><a href="' . esc_url(get_permalink()) . '">' . __('Read More', 'news-portal') . ' &raquo;</a></div>';
            endif;
            ?>
        </div><!-- .entry-content -->

        <footer class="entry-footer">
            <?php
            // カテゴリーとタグを表示
            $categories_list = get_the_category_list(', ');
            if ($categories_list) {
                echo '<div class="cat-links"><i class="fas fa-folder"></i> ' . $categories_list . '</div>';
            }

            $tags_list = get_the_tag_list('', ', ');
            if ($tags_list && !is_wp_error($tags_list)) {
                echo '<div class="tags-links"><i class="fas fa-tags"></i> ' . $tags_list . '</div>';
            }
            ?>
        </footer><!-- .entry-footer -->
    </div><!-- .article-inner -->
</article><!-- #post-<?php the_ID(); ?> -->