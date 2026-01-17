<?php
/**
 * Template Name: Blog Grid
 */

get_header();
?>

<div class="container">
  <div class="row">
    <div class="site-content col-lg-12 col-md-12 col-sm-12" role="main">

      <?php
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

      $query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 9,
        'paged'          => $paged,
      ]);
      ?>

      <?php if ($query->have_posts()) : ?>

        <div class="wd-row wd-grid blog-holder blog-loop" style="--wd-col-lg:3;--wd-col-md:3;--wd-col-sm:1;--wd-gap-lg:20px;--wd-gap-sm:10px;">
          <?php while ($query->have_posts()) : $query->the_post(); ?>

          <article id="post-<?php the_ID(); ?>"
							<?php post_class('wd-post blog-design-masonry blog-post-loop blog-style-bg wd-add-shadow wd-col'); ?>>

							<div class="wd-post-inner article-inner">

								<div class="wd-post-thumb entry-header">
									<div class="wd-post-img post-img-wrapp">
										<?php the_post_thumbnail('large'); ?>
									</div>

									<a class="wd-fill" href="<?php the_permalink(); ?>"></a>

									<div class="wd-post-date wd-style-with-bg">
										<span class="post-date-day"><?php echo get_the_date('d'); ?></span>
										<span class="post-date-month"><?php echo get_the_date('M'); ?></span>
									</div>
								</div>

								<div class="wd-post-content article-body-container">

									<div class="wd-post-cat wd-style-with-bg meta-post-categories">
										<?php the_category(', '); ?>
									</div>

									<h3 class="wd-post-title wd-entities-title title post-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>

									<div class="wd-post-excerpt entry-content">
										<?php the_excerpt(); ?>
									</div>

									<div class="wd-post-read-more wd-style-link read-more-section">
										<a href="<?php the_permalink(); ?>">Continuar a ler</a>
									</div>

								</div>
							</div>
						</article>

          <?php endwhile; ?>

        </div>

        <div class="wd-pagination">
          <?php
          echo paginate_links([
            'total' => $query->max_num_pages,
          ]);
          ?>
        </div>

        <?php wp_reset_postdata(); ?>

      <?php else : ?>
        <p>Nenhum post encontrado.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php get_footer(); ?>
