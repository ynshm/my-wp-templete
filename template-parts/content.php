
<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package News_Portal
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('news-article'); ?>>
    <?php if (!is_singular()) : ?>
    <div class="article-inner">
        <?php news_portal_post_thumbnail(); ?>

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
                    <span class="posted-on">
                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        <?php echo get_the_date(); ?>
                    </span>
                    <span class="byline">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <?php the_author_posts_link(); ?>
                    </span>
                    <?php if (has_category()) : ?>
                    <span class="cat-links">
                        <i class="fas fa-folder" aria-hidden="true"></i>
                        <?php the_category(', '); ?>
                    </span>
                    <?php endif; ?>
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                the_excerpt();
                echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Read More', 'news-portal') . ' <i class="fas fa-arrow-right"></i></a>';
            endif;
            ?>
        </div><!-- .entry-content -->

        <?php if (is_singular() && has_tag()) : ?>
        <footer class="entry-footer">
            <div class="tags-links">
                <i class="fas fa-tags" aria-hidden="true"></i>
                <?php the_tags('', ', '); ?>
            </div>
        </footer><!-- .entry-footer -->
        <?php endif; ?>
    </div>
    <?php else : ?>
    <div class="breadcrumbs">
        <?php
        if (function_exists('news_portal_breadcrumb')) {
            news_portal_breadcrumb();
        } else {
            // 基本的なブレッドクラムの実装
            echo '<div class="breadcrumbs-container">';
            echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'news-portal') . '</a>';
            echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
            
            if (is_category() || is_single()) {
                $categories = get_the_category();
                if (!empty($categories)) {
                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                    echo ' <span class="separator"><i class="fas fa-angle-right"></i></span> ';
                }
            }
            
            if (is_single()) {
                the_title();
            } elseif (is_page()) {
                the_title();
            } elseif (is_archive()) {
                echo get_the_archive_title();
            }
            echo '</div>';
        }
        ?>
    </div>

    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

        <?php if ('post' === get_post_type()) : ?>
        <div class="entry-meta">
            <span class="posted-on">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                <?php echo get_the_date(); ?>
                <?php if (get_the_time('U') !== get_the_modified_time('U')) : ?>
                <span class="updated-on">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                    <?php esc_html_e('Updated on', 'news-portal'); ?> <?php echo get_the_modified_date(); ?>
                </span>
                <?php endif; ?>
            </span>
            <span class="byline">
                <i class="fas fa-user" aria-hidden="true"></i>
                <?php the_author_posts_link(); ?>
            </span>
            <?php if (has_category()) : ?>
            <span class="cat-links">
                <i class="fas fa-folder" aria-hidden="true"></i>
                <?php the_category(', '); ?>
            </span>
            <?php endif; ?>
            <?php if (comments_open()) : ?>
            <span class="comments-link">
                <i class="fas fa-comments" aria-hidden="true"></i>
                <?php comments_popup_link(); ?>
            </span>
            <?php endif; ?>
        </div><!-- .entry-meta -->
        <?php endif; ?>
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

    <footer class="entry-footer">
        <?php if (has_tag()) : ?>
        <div class="tags-links">
            <i class="fas fa-tags" aria-hidden="true"></i>
            <?php the_tags('', ', '); ?>
        </div>
        <?php endif; ?>
        
        <?php
        // 関連記事の表示（カスタマイザーで無効化可能）
        if (get_theme_mod('single_post_related', true)) {
            news_portal_related_posts();
        }
        
        // 著者情報の表示（カスタマイザーで無効化可能）
        if (get_theme_mod('single_post_author_box', true)) {
            news_portal_author_box();
        }
        
        // 前後の記事へのリンク（カスタマイザーで無効化可能）
        if (get_theme_mod('single_post_navigation', true)) {
            the_post_navigation(
                array(
                    'prev_text' => '<span class="nav-subtitle"><i class="fas fa-arrow-left"></i> ' . esc_html__('Previous:', 'news-portal') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'news-portal') . ' <i class="fas fa-arrow-right"></i></span> <span class="nav-title">%title</span>',
                )
            );
        }
        ?>
    </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
<?php
/**
 * 投稿一覧表示用のテンプレート
 *
 * @package News_Portal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('news-article'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'medium_large',
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                        'class' => 'lazy-load',
                        'loading' => 'lazy',
                    )
                );
                ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="article-inner">
        <?php
        // カテゴリー表示
        $categories = get_the_category();
        if (!empty($categories)) :
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="entry-category">' . esc_html($categories[0]->name) . '</a>';
        endif;
        ?>

        <header class="entry-header">
            <?php
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <i class="far fa-calendar-alt" aria-hidden="true"></i>
                    <?php echo get_the_date(); ?>
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
            </div><!-- .entry-meta -->
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php the_excerpt(); ?>
        </div><!-- .entry-content -->

        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e('読む続ける', 'news-portal'); ?>
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </footer><!-- .entry-footer -->
    </div><!-- .article-inner -->
</article><!-- #post-<?php the_ID(); ?> -->
