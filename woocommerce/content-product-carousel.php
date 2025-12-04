<?php
/**
 * Content Product - Carousel (Child theme)
 * Path: wp-content/themes/woodmart-child/woocommerce/content-product-carousel.php
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<div <?php wc_product_class( 'product-grid-item product wd-hover-standard wd-quantity', $product ); ?> data-loop="<?php echo esc_attr( get_row_index ? get_row_index() : '' ); ?>" data-id="<?php echo esc_attr( $product->get_id() ); ?>">

  <div class="product-wrapper">
    <!-- TOP: imagem + quick actions -->
    <div class="product-element-top wd-quick-shop">
      <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="product-image-link" <?php echo ( is_customize_preview() ? 'target="_blank"' : '' ); ?>>
        <?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?>
      </a>

      <div class="wd-buttons wd-pos-r-t">
        <?php if ( function_exists( 'woodmart_quick_view_button' ) ) : ?>
          <!-- mantém compatibilidade com botões do tema -->
          <?php // woodmart_quick_view_button(); ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- TITLE -->
    <h3 class="wd-entities-title"><a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" <?php echo ( is_customize_preview() ? 'target="_blank"' : '' ); ?>><?php echo wp_kses_post( get_the_title( $product->get_id() ) ); ?></a></h3>

    <!-- RATING (centralizado) -->
    <div class="product-rating-wrapper">
      <div class="product-rating">
        <?php
        $rating = $product->get_average_rating();
        if ( ! $rating || $rating == 0 ) {
            echo '<div class="star-rating" role="img" aria-label="Sem avaliações"><span style="width:0%"></span></div>';
        } else {
            echo wc_get_rating_html( $rating );
        }
        ?>
      </div>
    </div>

    123

    <!-- PRICE + CTA -->
    <div class="product-element-bottom">
      <div class="product-cta-row">
        <div class="product-cta">
          <div class="price-add-row">

            <div class="price-col">
              <?php echo $product->get_price_html(); ?>
            </div>

            <div class="button-col">
              <?php
                // add to cart via Woo default filter (keeps atributos do tema)
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
              ?>
            </div>
          </div> <!-- .price-add-row -->
        </div>
      </div>
    </div> <!-- .product-element-bottom -->

  </div><!-- .product-wrapper -->
</div>
