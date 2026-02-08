<?php
/**
 * Related Products - Carousel (Woodmart-style)
 * Child override: woocommerce/single-product/related.php
 */
defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) ) {
	return;
}

// Quantos produtos buscar (total no carrossel)
$limit = 12;

// Pega IDs de produtos relacionados
$related_ids = wc_get_related_products( $product->get_id(), $limit );

if ( empty( $related_ids ) ) {
	return;
}

// Converte em objetos WC_Product válidos e visíveis
$related_products = array();

foreach ( $related_ids as $rid ) {
	$p = wc_get_product( $rid );
	if ( $p && $p->is_visible() ) {
		$related_products[] = $p;
	}
}

if ( empty( $related_products ) ) {
	return;
}

$is_oos = ! $product->is_in_stock();

// ID único pro container (não conflitar com outros carrosseis)
$carousel_id = 'carousel-related-' . wp_unique_id();
?>

<section class="related products wd-related-products">
	<h2 class="wd-related-title">
		<?php echo esc_html__( 'Produtos relacionados', 'woocommerce' ); ?>
	</h2>

	<div
		id="<?php echo esc_attr( $carousel_id ); ?>"
		class="wd-carousel-container wd-quantity-enabled slider-type-product products wd-carousel-spacing-10 title-line-one"
		data-owl-carousel=""
		data-wrap="no"
		data-hide_pagination_control="no"
		data-hide_prev_next_buttons="no"
		data-desktop="5"
		data-tablet_landscape="4"
		data-tablet="3"
		data-mobile="2"
	>
		<div class="owl-carousel wd-owl owl-items-lg-5 owl-items-md-4 owl-items-sm-3 owl-items-xs-2 product-carrousel">

			<?php foreach ( $related_products as $related_product ) : ?>
				<?php
					// Set global post context pro template funcionar certinho
					$GLOBALS['post'] = get_post( $related_product->get_id() );
					setup_postdata( $GLOBALS['post'] );
				?>

				<div class="slide-product owl-carousel-item">
					<?php
						/**
						 * Usa o teu template custom do related.
						 * Coloca esse arquivo em:
						 * /wp-content/themes/woodmart-child/woocommerce/content-product-related-custom.php
						 */
						wc_get_template(
							'content-product-related-custom.php',
							array( 'product' => $related_product ),
							'',
							get_stylesheet_directory() . '/woocommerce/'
						);
					?>
				</div>

			<?php endforeach; ?>

			<?php wp_reset_postdata(); ?>

		</div>
	</div>
</section>