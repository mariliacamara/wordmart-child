<?php
/**
 * Fallback content-product-related.php
 * Mantém as classes e hooks padrão do WooCommerce / tema
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<li <?php wc_product_class(); ?>>
    <a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="<?php the_permalink(); ?>">
        <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
        <h2 class="woocommerce-loop-product__title"><?php the_title(); ?></h2>
    </a>

    <div class="product-meta">
        <?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
    </div>

    <div class="product-actions">
        <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
    </div>
</li>
