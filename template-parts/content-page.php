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

    <?php if (has_post_thumbnail()) : ?>
    <div class="post-thumbnail">
        <?php the_post_thumbnail('large', array('class' => 'featured-image')); ?>
    </div>
    <?php endif; ?>

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
                    /* translators: %s: Name of current post */
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
</article><!-- #post-## -->