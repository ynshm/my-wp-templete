
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
      <div class="site-info">
        <?php
        /* translators: %s: CMS name, i.e. WordPress. */
        printf(esc_html__('Proudly powered by %s', 'custom-theme'), '<a href="https://wordpress.org/">WordPress</a>');
        ?>
        <span class="sep"> | </span>
        <?php
        /* translators: 1: Theme name, 2: Theme author. */
        printf(esc_html__('Theme: %1$s by %2$s.', 'custom-theme'), 'Custom Theme', '<a href="#">Replit User</a>');
        ?>
      </div>
    </footer>
  </div><!-- #page -->

  <?php wp_footer(); ?>
</body>
</html>
