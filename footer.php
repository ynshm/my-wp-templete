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
		<div class="footer-wave">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
				<path fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,202.7C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
			</svg>
		</div>

		<div class="container">
			<div class="footer-widgets">
				<div class="footer-widget-area">
					<?php
					for ($i = 1; $i <= get_theme_mod('footer_widget_columns', 3); $i++) {
						if (is_active_sidebar('footer-' . $i)) {
							echo '<div class="footer-widget">';
							dynamic_sidebar('footer-' . $i);
							echo '</div>';
						}
					}
					?>
				</div>
			</div>

			<div class="footer-middle">
				<div class="footer-logo">
					<?php 
					$custom_logo_id = get_theme_mod('custom_logo');
					$logo = wp_get_attachment_image_src($custom_logo_id, 'full');
					if (has_custom_logo()) {
						echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
					} else {
						echo '<h2>' . get_bloginfo('name') . '</h2>';
					}
					?>
				</div>

				<nav class="footer-navigation">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu',
							'menu_id'        => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>

				<div class="footer-social">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'social-menu',
							'menu_id'        => 'social-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>',
						)
					);
					?>
				</div>
			</div>

			<div class="site-info">
				<div class="copyright">
					<?php echo wp_kses_post(get_theme_mod('footer_copyright', sprintf(__('Copyright © %s %s. All Rights Reserved.', 'news-portal'), date('Y'), get_bloginfo('name')))); ?>
				</div>
				<div class="footer-credits">
					<?php
					/* translators: %s: CMS name, i.e. WordPress. */
					printf(esc_html__('Proudly powered by %s', 'news-portal'), 'WordPress');
					?>
					<span class="sep"> | </span>
					<?php
					/* translators: %1$s: Theme name, %2$s: Theme author. */
					printf(esc_html__('Theme: %1$s by %2$s.', 'news-portal'), 'News Portal', '<a href="https://replit.com/">Replit User</a>');
					?>
				</div>
			</div><!-- .site-info -->
		</div>

		<button id="scroll-to-top" class="scroll-top-btn" aria-label="<?php esc_attr_e('Scroll to top', 'news-portal'); ?>">
			<i class="fas fa-arrow-up"></i>
		</button>
	</footer><!-- #colophon -->
  </div><!-- #page -->

  <button id="scroll-top" class="scroll-top-btn pulse-animation" aria-label="<?php esc_attr_e('Scroll to top', 'news-portal'); ?>">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
  </button>

  <?php wp_footer(); ?>
</body>
</html>