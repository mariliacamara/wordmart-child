<?php
/**
 * content-product-related-custom.php
 * Template custom usado APENAS no bloco related products
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

// Mantemos classes do tema no container
?>
<li <?php wc_product_class( 'product-grid-item product wd-hover-standard', $product ); ?> data-id="<?php echo esc_attr( $product->get_id() ); ?>">

  <div class="product-wrapper">

    <!-- TOP: imagem e quick actions -->
    <div class="product-element-top wd-quick-shop">
      <a href="<?php echo esc_url( get_permalink() ); ?>" class="product-image-link">
        <?php
        /**
         * Hook woocommerce_before_shop_loop_item_title.
         *
         * @hooked woodmart_template_loop_product_thumbnails_gallery - 5
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woodmart_template_loop_product_thumbnail - 10
         */
        do_action( 'woocommerce_before_shop_loop_item_title' );
        ?>
      </a>

      <?php
      if ( 'no' === woodmart_loop_prop( 'grid_gallery' ) || ! woodmart_loop_prop( 'grid_gallery' ) ) {
        woodmart_hover_image();
      }
      ?>

      <div class="wd-buttons wd-pos-r-t<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-buttons' ) ); ?>">
        <?php do_action( 'woodmart_product_action_buttons' ); ?>
      </div>
    </div>

    <!-- BOTTOM: conteúdo do card -->
    <div class="product-element-bottom">

      <!-- Brand (se tiver) -->
      <?php
      $brand = get_post_meta( $product->get_id(), '_brand_name', true );
      if ( $brand ) : ?>
        <div class="product-brand"><?php echo esc_html( $brand ); ?></div>
      <?php endif; ?>

      <!-- Título -->
      <h3 class="wd-entities-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

      <!-- Rating (usa HTML padrão do Woo) -->
      <div class="product-rating-wrapper">
          <div class="product-rating">
              <?php
              $rating = $product->get_average_rating();

              if ( ! $rating || $rating == 0 ) {
                  echo '<div class="star-rating" role="img" aria-label="0 de 5"><span style="width:0%"></span></div>';
              } else {
                  echo wc_get_rating_html( $rating );
              }
              ?>
          </div>
      </div>

      <!-- Price + CTA -->
      <div class="product-cta-row">
        <div class="product-cta">
        <?php
        echo '<div class="price-add-row">';

        // Preço
        echo '<div class="price-col">';
        echo $product->get_price_html();
        echo '</div>';

        // Botão adicionar ao carrinho
        echo '<div class="button-col">';
        echo apply_filters(
            'woocommerce_loop_add_to_cart_link',
            sprintf(
                '<a href="%s" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" %s>%s</a>',
                esc_url( $product->add_to_cart_url() ),
                wc_implode_html_attributes( array(
                    'data-product_id'  => $product->get_id(),
                    'data-product_sku' => $product->get_sku(),
                    'aria-label'       => $product->add_to_cart_description(),
                    'rel'              => 'nofollow',
                ) ),
                esc_html__( 'ADICIONAR', 'woocommerce' )
            ),
            $product,
            $product->get_id()
        );
        echo '</div>';

        echo '</div>'; // .price-add-row
        ?>
        </div>
      </div>

    </div> <!-- .product-element-bottom -->

  </div> <!-- .product-wrapper -->
</li>
