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

            <article <?php post_class('wd-col wd-post blog-post-loop blog-design-default'); ?>>

              <div class="post-inner">

                <?php if (has_post_thumbnail()) : ?>
                  <div class="entry-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                      <?php the_post_thumbnail('large'); ?>
                    </a>

                    <div class="post-category">
                      <?php the_category(', '); ?>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="entry-content">

                  <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                  </div>

                  <h3 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>

                  <div class="entry-excerpt">
                    <?php the_excerpt(); ?>
                  </div>

                  <div class="entry-footer">
                    <a class="btn btn-style-link btn-color-primary" href="<?php the_permalink(); ?>">
                      CONTINUAR LENDO
                    </a>
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
