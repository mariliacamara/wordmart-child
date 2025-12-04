<?php
/**
 * The template for displaying product content within loops
 * Copy this file to: wp-content/themes/your-child-theme/woocommerce/content-product.php
 *
 * Adapted to force order: image -> title -> rating -> price -> quantity+add-to-cart
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$classes = wc_get_product_class( '', $product ); // keep original classes
?>
<div <?php wc_product_class( $classes ); ?>>

    <div class="product-wrapper">

        <!-- TOP: image + quick actions (compare / quick view / wishlist) -->
        <div class="product-element-top wd-quick-shop">
            <a href="<?php the_permalink(); ?>" class="product-image-link">
                <?php
                /**
                 * Show product thumbnail (fallback to placeholder)
                 */
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );
                } else {
                    echo wc_placeholder_img( 'woocommerce_thumbnail' );
                }
                ?>
            </a>

            <div class="wd-buttons wd-pos-r-t">
                <?php
                // keep theme quick buttons if available
                // compare
                if ( function_exists( 'woodmart_get_compare_link' ) ) {
                    // theme specific, otherwise skip
                }
                // quick view & wishlist are usually printed by theme JS; we keep markup hooks minimal
                ?>
            </div>
        </div> <!-- .product-element-top -->

        <!-- BOTTOM: title, rating, price, add to cart -->
        <div class="product-element-bottom">

            <?php
            // Title
            ?>
            <h3 class="wd-entities-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <?php
            // Rating (if any). Use standard Woo template when possible.
            if ( $rating_count = $product->get_rating_count() ) {
                echo '<div class="star-rating" role="img" aria-label="Avaliação ' . esc_attr( wc_format_decimal( $product->get_average_rating(), 2 ) ) . ' de 5">';
                echo wc_get_rating_html( $product->get_average_rating() );
                echo '</div>';
            } else {
                // opcional: placeholder "sem avaliações" (remova se não quiser)
                echo '<div class="star-rating no-reviews"><span class="no-reviews-text">Sem avaliações</span></div>';
            }
            ?>

            <?php
            // Price
            echo '<span class="price">';
                woocommerce_template_loop_price();
            echo '</span>';
            ?>

            <?php
            // Quantity + Add to cart
            // For simple products we show qty input + add button; keep Woo template for compatibility.
            ?>
            <div class="wd-add-btn wd-add-btn-replace">
                <?php
                // If product is single variation or needs form, use standard template
                if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>

                    <div class="quantity">
                        <input type="button" value="-" class="minus">
                        <label class="screen-reader-text" for="quantity_<?php echo esc_attr( uniqid() ); ?>"><?php echo 'Quantidade de ' . get_the_title(); ?></label>
                        <input type="number"
                               id="quantity_<?php echo esc_attr( uniqid() ); ?>"
                               class="input-text qty text"
                               step="1"
                               min="1"
                               max="<?php echo esc_attr( $product->get_max_purchase_quantity() ); ?>"
                               name="quantity"
                               value="1"
                               inputmode="numeric" />
                        <input type="button" value="+" class="plus">
                    </div>

                    <?php
                    // Add to cart link (keeps ajax loop behaviour)
                    echo sprintf(
                        '<a href="%1$s" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-loop" data-product_id="%2$d" data-product_sku="%3$s" aria-label="%4$s" rel="nofollow"><span>%5$s</span></a>',
                        esc_url( esc_url_raw( $product->add_to_cart_url() ) ),
                        absint( $product->get_id() ),
                        esc_attr( $product->get_sku() ),
                        sprintf( esc_attr__( 'Adiciona ao carrinho: “%s”', 'woocommerce' ), esc_attr( get_the_title() ) ),
                        esc_html__( 'Adicionar', 'woocommerce' )
                    );

                else:
                    // fallback: use whatever Woo outputs for complex products
                    woocommerce_template_loop_add_to_cart();
                endif;
                ?>
            </div> <!-- .wd-add-btn -->

        </div> <!-- .product-element-bottom -->

    </div> <!-- .product-wrapper -->

</div> <!-- product -->
