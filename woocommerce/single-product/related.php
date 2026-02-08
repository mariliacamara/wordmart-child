<?php foreach ( $related_products as $related_product ) : ?>
  <?php
    global $product;

    // troca o produto global pro template funcionar
    $product = $related_product;

    $GLOBALS['post'] = get_post( $product->get_id() );
    setup_postdata( $GLOBALS['post'] );
  ?>

  <div class="slide-product owl-carousel-item">
    <?php wc_get_template_part( 'content', 'product-related-custom' ); ?>
  </div>

<?php endforeach; ?>

<?php wp_reset_postdata(); ?>
