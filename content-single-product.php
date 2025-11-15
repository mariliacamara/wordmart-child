<?php
defined( 'ABSPATH' ) || exit;
global $product;
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

  <!-- Breadcrumb -->
  <div class="wd-breadcrumbs-wrap container">
    <?php if ( function_exists( 'woodmart_current_breadcrumbs' ) ) : ?>
      <div class="wd-breadcrumbs">
        <?php woodmart_current_breadcrumbs( 'shop' ); ?>
      </div>
    <?php else : ?>
      <?php woocommerce_breadcrumb(); ?>
    <?php endif; ?>
  </div>

  <div class="wd-product-top container">
    <div class="row wd-product-main">

      <!-- LEFT: Gallery (usa o hook padrão para carregar imagens) -->
      <div class="col-lg-6 wd-product-gallery-col">
        <div class="wd-product-gallery">
          <?php
          /**
           * Show product images
           * hooked normally via woocommerce_before_single_product_summary
           */
          do_action( 'woocommerce_before_single_product_summary' );
          ?>
        </div>
      </div>

      <!-- RIGHT: Summary (renderizado manualmente para controlar ordem e evitar duplicados) -->
      <div class="col-lg-6 wd-product-summary-col">
        <div class="wd-summary-inner">

          <!-- Brand -->
          <div class="wd-product-brand">
            <?php
            $brand = get_post_meta( get_the_ID(), '_brand_name', true );
            if ( $brand ) {
              echo '<span class="brand-name">'. esc_html( $brand ) .'</span>';
            }
            ?>
          </div>

          <!-- Title -->
          <h1 class="product-title"><?php the_title(); ?></h1>

          <!-- Short description / excerpt -->
          <div class="product-excerpt">
            <?php the_excerpt(); ?>
          </div>

          <!-- Price (pill) -->
          <div class="wd-price-pill">
            <?php woocommerce_template_single_price(); ?>
          </div>

          <!-- Rating -->
          <div class="wd-rating">
            <?php woocommerce_template_single_rating(); ?>
          </div>

          <!-- Shipping / availability text -->
          <div class="wd-delivery">
            <?php
            if ( function_exists( 'woodmart_get_shipping_text' ) ) {
              echo woodmart_get_shipping_text();
            } else {
              echo '<span>Entrega entre 24h a 48h para Portugal continental.</span>';
            }
            ?>
          </div>

          <!-- Quantity + Add to cart (somente esta parte, sem re-chamar o hook completo) -->
          <div class="wd-add-to-cart-wrap">
            <?php
            // Renderiza o formulário add-to-cart (tratando simples e variáveis)
            if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
              woocommerce_template_single_add_to_cart();
            } else {
              // fallback: tenta incluir template padrão
              wc_get_template( 'single-product/add-to-cart/simple.php' );
            }
            ?>
          </div>

          <!-- Variations boxes (se usares swatches ou personalizações, vai aparecer aqui) -->
          <div class="wd-variations-boxes">
            <?php
            // deixa o espaço preparado para estilos/JS de swatches
            ?>
          </div>

          <!-- REF / meta -->
          <div class="wd-product-meta">
            <?php woocommerce_template_single_meta(); ?>
          </div>

        </div>
      </div>

    </div> <!-- .row -->
  </div> <!-- .wd-product-top -->

  <!-- TABS and description / additional info (usa o hook padrão) -->
  <div class="wd-product-tabs container">
    <div class="row">
      <div class="col-12">
        <?php
        /**
         * Product tabs, upsells and related products
         * hooked via woocommerce_after_single_product_summary
         */
        do_action( 'woocommerce_after_single_product_summary' );
        ?>
      </div>
    </div>
  </div>

  <?php do_action( 'woocommerce_after_single_product' ); ?>

</div>
