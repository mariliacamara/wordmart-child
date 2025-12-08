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
      <div class="wd-buttons wd-pos-r-t<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-buttons' ) ); ?>">
        <?php do_action( 'woodmart_product_action_buttons' ); ?>
      </div>
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
    </div>

    <!-- BOTTOM: conteúdo do card -->
    <div class="product-element-bottom">
      <div class="product-brand-title-wrap">

        <!-- Brand (taxonomy attribute -> term name + logo OR fallback _brand_name) -->
        <?php
        $brand_name = '';
        $brand_logo = '';

        $attr = function_exists( 'woodmart_get_opt' ) ? woodmart_get_opt( 'brands_attribute' ) : '';

        if ( $attr ) {
            $terms = wc_get_product_terms( $product->get_id(), $attr, array( 'fields' => 'all' ) );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                $term = $terms[0];
                $brand_name = $term->name;

                // tentar meta image / image_id / thumbnail_id
                $meta_image = get_term_meta( $term->term_id, 'image', true );
                $meta_image_id = get_term_meta( $term->term_id, 'image_id', true );
                $meta_thumb = get_term_meta( $term->term_id, 'thumbnail_id', true );

                if ( $meta_image ) {
                    if ( is_array( $meta_image ) && ! empty( $meta_image['id'] ) ) {
                        $brand_logo = wp_get_attachment_image_url( intval( $meta_image['id'] ), 'full' );
                    } elseif ( is_numeric( $meta_image ) ) {
                        $brand_logo = wp_get_attachment_image_url( intval( $meta_image ), 'full' );
                    } else {
                        $brand_logo = esc_url_raw( $meta_image );
                    }
                }

                if ( ! $brand_logo && $meta_image_id && is_numeric( $meta_image_id ) ) {
                    $brand_logo = wp_get_attachment_image_url( intval( $meta_image_id ), 'full' );
                }

                if ( ! $brand_logo && $meta_thumb && is_numeric( $meta_thumb ) ) {
                    $brand_logo = wp_get_attachment_image_url( intval( $meta_thumb ), 'full' );
                }
            }
        }

        // fallback para meta antiga do produto
        if ( empty( $brand_name ) ) {
            $meta_brand = get_post_meta( $product->get_id(), '_brand_name', true );
            if ( $meta_brand ) {
                $brand_name = $meta_brand;
            }
        }
        ?>

        <div class="product-brand">
          <?php if ( $brand_name ) : ?>
            <span class="product-brand-name"><?php echo esc_html( $brand_name ); ?></span>
          <?php endif; ?>
        </div>

        <!-- Título -->
        <h3 class="wd-entities-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      </div>


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
