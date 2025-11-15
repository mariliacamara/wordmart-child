<?php
/**
 * Content Product - Related (Child theme override)
 * Path: wp-content/themes/teu-child-theme/woocommerce/content-product-related.php
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<div <?php wc_product_class( 'product-grid-item product wd-hover-standard', $product ); ?> data-id="<?php echo esc_attr( $product->get_id() ); ?>">

  <div class="product-wrapper">

    <!-- TOP: imagem e quick actions -->
    <div class="product-element-top wd-quick-shop">
      <a href="<?php the_permalink(); ?>" class="product-image-link">
        <?php
        // thumbnail
        echo woocommerce_get_product_thumbnail( '600x600' );
        ?>
      </a>

      <div class="wd-buttons wd-pos-r-t">
        <!-- comparar / quick view / wishlist (mantém as mesmas classes do tema para compatibilidade) -->
        <?php if ( function_exists( 'woodmart_quick_view_button' ) ) : ?>
          <?php // exemplo: woodmart_quick_view_button(); ?>
        <?php endif; ?>
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

      <!-- thin separator -->
      <div class="sep-line" aria-hidden="true"></div>

      <!-- Rating (usa HTML padrão do Woo) -->
      <div class="product-rating">
        <?php
        // show rating (if theme hides via CSS, o teu CSS override garantirá visibilidade)
        echo wc_get_rating_html( $product->get_average_rating() );
        ?>
      </div>

      <!-- Price + CTA -->
      <div class="product-cta-row">
        <div class="product-price">
          <?php echo $product->get_price_html(); ?>
        </div>

        <div class="product-cta">
          <?php
          // botão add to cart (ajax) — garante que funciona para simples/variations
          // Re-usa a markup do tema (fallback)
          if ( $product->is_type( 'simple' ) ) {
              woocommerce_simple_add_to_cart();
          } else {
              // para variações/usual: usa template do Woo
              woocommerce_template_loop_add_to_cart();
          }
          ?>
        </div>
      </div>

    </div> <!-- .product-element-bottom -->

  </div> <!-- .product-wrapper -->
</div>
