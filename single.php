
<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <?php
    while (have_posts()) :
      the_post();
      get_template_part('template-parts/content', 'single');

      // 前後の記事ナビゲーション (記事下部)
      echo '<div class="post-navigation-container">';
      echo '<h4 class="post-navigation-title">' . esc_html__('前後の記事', 'news-portal') . '</h4>';
      echo '<div class="post-navigation-wrapper">';
      
      // 前の記事
      $prev_post = get_previous_post();
      if (!empty($prev_post)) :
          ?>
          <div class="post-navigation-item post-navigation-prev">
              <?php if (has_post_thumbnail($prev_post->ID)) : ?>
                  <a href="<?php echo get_permalink($prev_post->ID); ?>" class="post-navigation-thumbnail">
                      <?php echo get_the_post_thumbnail($prev_post->ID, 'thumbnail'); ?>
                  </a>
              <?php endif; ?>
              <div class="post-navigation-content">
                  <span class="post-navigation-label"><?php echo esc_html__('前の記事', 'news-portal'); ?></span>
                  <h5 class="post-navigation-title">
                      <a href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo get_the_title($prev_post->ID); ?></a>
                  </h5>
              </div>
          </div>
          <?php
      endif;
      
      // 次の記事
      $next_post = get_next_post();
      if (!empty($next_post)) :
          ?>
          <div class="post-navigation-item post-navigation-next">
              <?php if (has_post_thumbnail($next_post->ID)) : ?>
                  <a href="<?php echo get_permalink($next_post->ID); ?>" class="post-navigation-thumbnail">
                      <?php echo get_the_post_thumbnail($next_post->ID, 'thumbnail'); ?>
                  </a>
              <?php endif; ?>
              <div class="post-navigation-content">
                  <span class="post-navigation-label"><?php echo esc_html__('次の記事', 'news-portal'); ?></span>
                  <h5 class="post-navigation-title">
                      <a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo get_the_title($next_post->ID); ?></a>
                  </h5>
              </div>
          </div>
          <?php
      endif;
      
      echo '</div>';
      echo '</div>';
      
      // 同じカテゴリーの記事 (記事下部)
      echo '<div class="related-category-posts">';
      echo '<h4 class="related-category-title">' . esc_html__('同じカテゴリーの記事', 'news-portal') . '</h4>';
      echo '<div class="related-category-container">';
      
      $categories = get_the_category();
      if (!empty($categories)) {
          $category_ids = array();
          foreach ($categories as $category) {
              $category_ids[] = $category->term_id;
          }
          
          $related_args = array(
              'posts_per_page' => 4,
              'post__not_in' => array(get_the_ID()),
              'category__in' => $category_ids,
              'orderby' => 'rand',
          );
          
          $related_query = new WP_Query($related_args);
          
          if ($related_query->have_posts()) :
              while ($related_query->have_posts()) : $related_query->the_post();
              ?>
              <article class="related-category-post">
                  <?php if (has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>" class="related-category-thumbnail">
                      <?php the_post_thumbnail('medium'); ?>
                  </a>
                  <?php endif; ?>
                  <h5 class="related-category-post-title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h5>
                  <div class="related-category-meta">
                      <span class="related-category-date"><?php echo get_the_date(); ?></span>
                  </div>
              </article>
              <?php
              endwhile;
              wp_reset_postdata();
          endif;
          
          echo '</div>';
          echo '</div>';
      }
      
      // 前後の記事のナビゲーション
      the_post_navigation(
          array(
              'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'news-portal') . '</span> <span class="nav-title">%title</span>',
              'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'news-portal') . '</span> <span class="nav-title">%title</span>',
          )
      );

      // コメントテンプレートの読み込み
      if (comments_open() || get_comments_number()) :
          comments_template();
      endif;
    endwhile; // End of the loop.
    ?>
  </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
