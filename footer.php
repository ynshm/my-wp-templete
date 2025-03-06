
<?php
/**
 * フッターテンプレート
 *
 * @package News_Portal
 */
?>

    </div><!-- .container -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
      <div class="container">
        <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
        <div class="footer-widgets">
          <div class="footer-widget-area">
            <?php if (is_active_sidebar('footer-1')) : ?>
              <div class="footer-widget">
                <?php dynamic_sidebar('footer-1'); ?>
              </div>
            <?php endif; ?>
            
            <?php if (is_active_sidebar('footer-2')) : ?>
              <div class="footer-widget">
                <?php dynamic_sidebar('footer-2'); ?>
              </div>
            <?php endif; ?>
            
            <?php if (is_active_sidebar('footer-3')) : ?>
              <div class="footer-widget">
                <?php dynamic_sidebar('footer-3'); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="social-links">
          <?php
          if (has_nav_menu('social-menu')) {
            wp_nav_menu(array(
              'theme_location' => 'social-menu',
              'menu_id' => 'social-menu',
              'container' => 'nav',
              'container_class' => 'social-navigation',
              'link_before'     => '<span class="screen-reader-text">',
              'link_after'      => '</span>',
              'depth'           => 1,
            ));
          }
          ?>
        </div>
        
        <div class="site-info">
          <?php
          // フッターメニューの表示
          if (has_nav_menu('footer-menu')) {
            wp_nav_menu(array(
              'theme_location' => 'footer-menu',
              'menu_id' => 'footer-menu',
              'container' => 'nav',
              'container_class' => 'footer-navigation',
              'depth' => 1,
            ));
          }
          
          // コピーライト表示（カスタマイザーから設定可能）
          $copyright = get_theme_mod('footer_copyright', sprintf(__('Copyright © %s %s. All Rights Reserved.', 'news-portal'), date('Y'), get_bloginfo('name')));
          echo '<div class="copyright">' . wp_kses_post($copyright) . '</div>';
          ?>
          
          <div class="footer-credits">
            <?php
            /* translators: %s: CMS name, i.e. WordPress. */
            printf(esc_html__('Proudly powered by %s', 'news-portal'), '<a href="https://wordpress.org/">WordPress</a>');
            ?>
            <span class="sep"> | </span>
            <?php
            /* translators: 1: Theme name, 2: Theme author. */
            printf(esc_html__('Theme: %1$s by %2$s.', 'news-portal'), 'News Portal', '<a href="#">Replit User</a>');
            ?>
          </div>
        </div><!-- .site-info -->
      </div><!-- .container -->
    </footer><!-- #colophon -->
  </div><!-- #page -->

  <button id="scroll-top" class="scroll-top-btn" aria-label="<?php esc_attr_e('Scroll to top', 'news-portal'); ?>">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
  </button>

  <?php wp_footer(); ?>
</body>
</html>
