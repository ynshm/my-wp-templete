
<?php
/**
 * サイドバーテンプレート
 *
 * @package News_Portal
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <div class="sidebar-inner">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
        
        <?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
            <section class="widget">
                <h2 class="widget-title"><?php esc_html_e( 'Recent Posts', 'news-portal' ); ?></h2>
                <ul>
                    <?php
                    $recent_posts = wp_get_recent_posts( array(
                        'numberposts' => 5,
                        'post_status' => 'publish'
                    ) );
                    
                    foreach ( $recent_posts as $post ) {
                        echo '<li><a href="' . get_permalink( $post['ID'] ) . '">' . $post['post_title'] . '</a></li>';
                    }
                    wp_reset_postdata();
                    ?>
                </ul>
            </section>
            
            <section class="widget">
                <h2 class="widget-title"><?php esc_html_e( 'Categories', 'news-portal' ); ?></h2>
                <ul>
                    <?php
                    wp_list_categories( array(
                        'title_li' => '',
                        'show_count' => true
                    ) );
                    ?>
                </ul>
            </section>
            
            <section class="widget">
                <h2 class="widget-title"><?php esc_html_e( 'Archives', 'news-portal' ); ?></h2>
                <ul>
                    <?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
                </ul>
            </section>
        <?php endif; ?>
    </div>
</aside><!-- #secondary -->
