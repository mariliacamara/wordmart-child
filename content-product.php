<?php
defined( 'ABSPATH' ) || exit;
global $product;
?>

<div <?php wc_product_class( '', $product ); ?> data-id="<?php echo esc_attr( $product->get_id() ); ?>">

  <div class="product-wrapper">
    <!-- imagem / top -->
    <div class="product-element-top wd-quick-shop">
      <a href="<?php the_permalink(); ?>" class="product-image-link">
        <?php
          // image
          echo woocommerce_get_product_thumbnail();
        ?>
      </a>

      <div class="wd-buttons wd-pos-r-t">
        <?php
        // quick actions (keeps theme buttons if hooked)
        do_action( 'woodmart_product_loop_buttons' ); // se o tema usa action diferente, pode ficar vazio — mas mantém compatibilidade
        ?>
      </div>
    </div>

    <!-- bottom: rating, title, price, add -->
    <div class="product-element-bottom">

      <?php
      // rating (forçado no topo)
      if ( $rating_html = wc_get_rating_html( $product->get_average_rating() ) ) {
          echo '<div class="star-rating" role="img" aria-label="Avaliação ' . esc_attr( $product->get_average_rating() ) . ' de 5">';
          echo $rating_html;
          echo '</div>';
      }
      ?>

      <h3 class="wd-entities-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
      </h3>

      <?php
      // price
      echo '<span class="price">';
      woocommerce_template_loop_price();
      echo '</span>';
      ?>

      <div class="wd-add-btn wd-add-btn-replace">
        <?php
        // quantity (hidden/compact). Ajuste se quiser mostrar.
        if ( $product->is_type( 'simple' ) ) {
            echo '<div class="quantity hidden">';
            echo '<label class="screen-reader-text" for="qty_' . esc_attr( $product->get_id() ) . '">Quantidade de ' . esc_html( $product->get_name() ) . '</label>';
            echo '<input type="hidden" id="qty_' . esc_attr( $product->get_id() ) . '" class="input-text qty text" name="quantity" value="1" />';
            echo '</div>';
        }

        // add to cart button (usa a função padrão do loop do Woo)
        woocommerce_template_loop_add_to_cart();
        ?>
      </div>

    </div> <!-- .product-element-bottom -->
  </div> <!-- .product-wrapper -->

</div>
