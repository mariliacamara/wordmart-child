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
					/**
					 * Show product images
					 * hooked: woocommerce_show_product_images
					 */
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>
			</div>

			<!-- RIGHT: Summary -->
			<div class="col-lg-6 wd-product-summary-col">
				<div class="wd-summary-inner">

					<div class="wd-product-brand">
						<?php
						// if you have brand taxonomy or ACF, render here. Fallback: product attributes label "Marca"
						echo get_post_meta( get_the_ID(), '_brand_name', true ) ? '<span class="brand-name">'. esc_html( get_post_meta( get_the_ID(), '_brand_name', true ) ) .'</span>' : '';
						?>
					</div>

					<h1 class="product-title"><?php the_title(); ?></h1>

					<div class="product-excerpt">
						<?php the_excerpt(); ?>
					</div>

					<!-- Price in pill -->
					<div class="wd-price-pill">
						<?php woocommerce_template_single_price(); ?>
					</div>

					<!-- Rating -->
					<div class="wd-rating">
						<?php woocommerce_template_single_rating(); ?>
					</div>

					<!-- Shipping / availability text (example) -->
					<div class="wd-delivery">
						<?php if ( function_exists( 'woodmart_get_shipping_text' ) ) {
							// custom theme helper if exists
							echo woodmart_get_shipping_text();
						} else {
							echo '<span>Entrega entre 24h a 48h para Portugal continental.</span>';
						} ?>
					</div>

					<!-- Quantity + Add to cart -->
					<div class="wd-add-to-cart-wrap">
						<?php
						/**
						 * Add to cart form (handles simple & variable products)
						 * hooked: woocommerce_template_single_add_to_cart
						 */
						do_action( 'woocommerce_single_product_summary' ); // we will remove duplicates via hooks below if needed
						?>
					</div>

					<!-- Variation boxes placeholder (if you use swatches plugin or theme options, it will render here) -->
					<div class="wd-variations-boxes">
						<?php
						// If variations are rendered as selects, they will appear here because woocommerce_single_product_summary includes them.
						// For nicer box style, see CSS + JS below or use WoodMart swatches.
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

	<!-- TABS and description / additional info -->
	<div class="wd-product-tabs container">
		<div class="row">
			<div class="col-12">
				<?php
				/**
				 * Product tabs, upsells and related products
				 */
				do_action( 'woocommerce_after_single_product_summary' );
				?>
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_single_product' ); ?>

</div>
