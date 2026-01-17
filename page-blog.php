<?php
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$query = new WP_Query([
  'post_type'      => 'post',
  'posts_per_page' => 9,
  'paged'          => $paged,
]);

if ($query->have_posts()) :
  while ($query->have_posts()) : $query->the_post();
?>
<article id="post-<?php the_ID(); ?>"
  <?php post_class('wd-post blog-design-masonry blog-post-loop blog-style-bg wd-add-shadow wd-col'); ?>>

  <div class="wd-post-inner article-inner">

    <div class="wd-post-thumb entry-header">
      <div class="wd-post-img post-img-wrapp">
        <?php
        if (has_post_thumbnail()) {
          the_post_thumbnail('large', [
            'class' => 'attachment-large size-large',
            'loading' => 'lazy',
            'decoding' => 'async'
          ]);
        }
        ?>
      </div>

      <a class="wd-fill" tabindex="-1" href="<?php the_permalink(); ?>"
         aria-label="Link on post <?php the_title_attribute(); ?>"></a>

      <div class="wd-post-date wd-style-with-bg">
        <span class="post-date-day"><?php echo get_the_date('d'); ?></span>
        <span class="post-date-month"><?php echo get_the_date('M'); ?></span>
      </div>
    </div>

    <div class="wd-post-content article-body-container">

      <div class="wd-post-cat wd-style-with-bg meta-post-categories">
        <?php the_category(' '); ?>
      </div>

      <h3 class="wd-post-title wd-entities-title title post-title">
        <a href="<?php the_permalink(); ?>" rel="bookmark">
          <?php the_title(); ?>
        </a>
      </h3>

      <div class="wd-post-meta">
        <div class="wd-post-author">
          <span>Publicado por</span>
          <?php echo get_avatar(get_the_author_meta('ID'), 18); ?>
          <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"
             class="author" rel="author">
            <?php the_author(); ?>
          </a>
        </div>

        <div class="wd-modified-date">
          <time class="updated" datetime="<?php echo get_the_modified_date('c'); ?>">
            <?php echo get_the_modified_date(); ?>
          </time>
        </div>
      </div>

      <div class="wd-post-excerpt entry-content">
        <?php the_excerpt(); ?>
      </div>

      <div class="wd-post-read-more wd-style-link read-more-section">
        <a href="<?php the_permalink(); ?>">Continuar a ler</a>
      </div>

    </div>
  </div>
</article>
<?php
  endwhile;
  wp_reset_postdata();
endif;
?>
