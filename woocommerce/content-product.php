<?php
/**
 * Conteúdo do produto na listagem — com contador de quantidade (somente simple)
 * Caminho: wp-content/themes/woodmart-child/woocommerce/content-product.php
 */
defined( 'ABSPATH' ) || exit;

global $product;
global $wd_wishlist_loop;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$is_oos = ! $product->is_in_stock();
?>

<li <?php wc_product_class( 'product-grid-item product wd-hover-standard' . ( $is_oos ? ' is-outofstock' : '' ), $product ); ?> data-id="<?php echo esc_attr( $product->get_id() ); ?>">
	<!-- WISHLIST -->
	<a
		href="#"
		class="wd-wishlist-remove"
		data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
	>
		× Remover
	</a>

	<div class="product-wrapper">
		<!-- TOP -->
		<div class="product-element-top">
			<?php if ( $is_oos ) : ?>
				<div class="oos-svg-badge" aria-hidden="true">
					<?php
						echo file_get_contents(
							get_stylesheet_directory() . '/assets/icons/outofstock.svg'
						);
					?>
				</div>
			<?php endif; ?>

			<div class="wd-buttons wd-pos-r-t<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-buttons' ) ); ?>">
				<?php do_action( 'woodmart_product_action_buttons' ); ?>
			</div>

			<?php
			$price = (float) $product->get_price();
			if ( $price > 64.00 ) : ?>
				<div class="price-badge free-shipping-badge" aria-label="Envio grátis" data-tooltip="Portes grátis">
					<?php echo file_get_contents( get_stylesheet_directory() . '/assets/icons/gratis.svg' ); ?>
				</div>
			<?php endif; ?>

			<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="product-image-link">
				<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			</a>

			<?php
			if ( 'no' === woodmart_loop_prop( 'grid_gallery' ) || ! woodmart_loop_prop( 'grid_gallery' ) ) {
				woodmart_hover_image();
			}
			?>
		</div>

		<!-- BOTTOM -->
		<div class="product-element-bottom">

			<div class="product-brand-title-wrap">
				<?php
				$brand_name = '';
				$attr       = function_exists( 'woodmart_get_opt' ) ? woodmart_get_opt( 'brands_attribute' ) : '';

				if ( $attr ) {
					$terms = wc_get_product_terms( $product->get_id(), $attr, [ 'fields' => 'all' ] );
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$brand_name = $terms[0]->name;
					}
				}

				if ( empty( $brand_name ) ) {
					$meta_brand = get_post_meta( $product->get_id(), '_brand_name', true );
					if ( $meta_brand ) {
						$brand_name = $meta_brand;
					}
				}
				?>

				<?php if ( $brand_name ) : ?>
					<div class="product-brand">
						<span class="product-brand-name"><?php echo esc_html( $brand_name ); ?></span>
					</div>
				<?php endif; ?>

				<h3 class="wd-entities-title">
					<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
						<?php echo wp_kses_post( $product->get_name() ); ?>
					</a>
				</h3>
			</div>

			<!-- RATING -->
			<div class="product-rating-wrapper">
				<div class="product-rating">
					<?php
					$rating = $product->get_average_rating();
					echo $rating ? wc_get_rating_html( $rating ) : '<div class="star-rating"><span style="width:0%"></span></div>';
					?>
				</div>
			</div>

			<!-- PRICE + CTA -->
			<div class="product-cta-row">
				<div class="product-cta">

					<div class="product-cta-price">
						<?php echo $product->get_price_html(); ?>
					</div>

					<?php if ( $is_oos ) : ?>

						<div class="wd-add-btn wd-add-btn-replace">
							<a class="button add-to-cart-loop oos-btn is-disabled" href="#" aria-disabled="true" tabindex="-1">
								<span>SEM STOCK</span>
							</a>
						</div>


					<?php else : ?>

						<?php if ( $product->is_type( 'variable' ) ) : ?>
	
							<div class="wd-add-btn wd-add-btn-replace">
								<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="button product_type_variable">
									<span><?php echo esc_html__( 'Ver opções', 'woocommerce' ); ?></span>
								</a>
							</div>

						<?php else : ?>

							<?php
							$min    = $product->get_min_purchase_quantity();
							$max    = $product->get_max_purchase_quantity();
							$qty_id = 'quantity_' . wp_unique_id();
							?>

							<div class="wd-add-btn wd-add-btn-replace">
								<div class="quantity">
									<input type="button" value="-" class="minus btn" aria-label="Decrease quantity">

									<label class="screen-reader-text" for="<?php echo esc_attr( $qty_id ); ?>">
										<?php echo esc_html( sprintf( __( 'Quantidade de %s', 'woocommerce' ), $product->get_name() ) ); ?>
									</label>

									<input
										type="number"
										id="<?php echo esc_attr( $qty_id ); ?>"
										class="input-text qty text"
										value="<?php echo esc_attr( $min ); ?>"
										min="<?php echo esc_attr( $min ); ?>"
										<?php if ( $max ) : ?>max="<?php echo esc_attr( $max ); ?>"<?php endif; ?>
										name="quantity"
										step="1"
										inputmode="numeric"
										autocomplete="off"
									>

									<input type="button" value="+" class="plus btn" aria-label="Increase quantity">
								</div>

								<?php
								echo apply_filters(
									'woocommerce_loop_add_to_cart_link',
									sprintf(
										'<a href="%s"
												data-quantity="%s"
												data-qty-target="#%s"
												class="button product_type_%s add_to_cart_button ajax_add_to_cart add-to-cart-loop"
												%s>
												<span>%s</span>
										</a>',
										esc_url( $product->add_to_cart_url() ),
										esc_attr( $min ),
										esc_attr( $qty_id ), // 👈 AQUI entra o data-qty-target
										esc_attr( $product->get_type() ),
										wc_implode_html_attributes( array(
											'data-product_id'  => $product->get_id(),
											'data-product_sku' => $product->get_sku(),
											'aria-label'       => $product->add_to_cart_description(),
											'rel'              => 'nofollow',
										) ),
										esc_html__( 'Adicionar', 'woocommerce' )
									),
									$product,
									$product->get_id()
								);
								?>
							</div>

						<?php endif; ?>

					<?php endif; ?>

				</div>
			</div>

		</div>
	</div>
</li>
