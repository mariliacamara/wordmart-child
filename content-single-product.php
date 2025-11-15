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

      <!-- LEFT: Gallery -->
      <div class="col-lg-6 wd-product-gallery-col">
        <div class="wd-product-gallery">
          <?php
          do_action( 'woocommerce_before_single_product_summary' );
          ?>
        </div>
      </div>

      <!-- RIGHT: Summary -->
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

          <!-- Excerpt -->
          <div class="product-excerpt">
            <?php the_excerpt(); ?>
          </div>

          <!-- PRICE -->
          <div class="wd-price-pill">
            <?php woocommerce_template_single_price(); ?>
          </div>

          <!-- ⭐ RATING (FORÇADO) -->
          <div class="my-rating-force">
            <?php
            $avg   = (float) $product->get_average_rating();
            $count = (int) $product->get_rating_count();

            echo '<div class="my-rating-inner">';
            echo wc_get_rating_html( $avg );
            if ( $count > 0 ) {
                echo '<span class="count">(' . $count . ')</span>';
            } else {
                echo '<span class="count no-reviews">Sem avaliações</span>';
            }
            echo '</div>';
            ?>
          </div>

          <!-- Shipping -->
          <div class="wd-delivery">
            <?php
            if ( function_exists( 'woodmart_get_shipping_text' ) ) {
              echo woodmart_get_shipping_text();
            } else {
              echo '<span>Entrega entre 24h a 48h para Portugal continental.</span>';
            }
            ?>
          </div>

          <!-- ADD TO CART -->
          <div class="wd-add-to-cart-wrap">
            <?php woocommerce_template_single_add_to_cart(); ?>
          </div>

          <!-- Variations placeholder -->
          <div class="wd-variations-boxes"></div>

          <!-- META (apenas SKU — sem categorias, sem stock) -->
          <div class="wd-product-meta">
            <?php
            // Mostrar somente SKU (se existir). Não mostrar categorias nem quantidade em stock.
            $sku = $product->get_sku();
            if ( $sku ) {
                echo '<div class="product-sku"><strong>REF:</strong> ' . esc_html( $sku ) . '</div>';
            }
            // Se quiseres mostrar outros campos personalizados, coloca aqui explicitamente.
            ?>
          </div>

        </div>
      </div>

    </div> <!-- .row -->
  </div> <!-- .wd-product-top -->

  <!-- Tabs -->
  <div class="wd-product-tabs container">
    <div class="row">
      <div class="col-12">
        <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
      </div>
    </div>
  </div>

  <?php do_action( 'woocommerce_after_single_product' ); ?>

</div>
