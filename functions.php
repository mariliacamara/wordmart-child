<?php 
/* Child theme generated with WPS Child Theme Generator */
            
if ( ! function_exists( 'b7ectg_theme_enqueue_styles' ) ) {            
    add_action( 'wp_enqueue_scripts', 'b7ectg_theme_enqueue_styles' );
    
    function b7ectg_theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );
    }
}

/* Unregistered Widgets */
if ( ! function_exists( 'b7ectg_unregister_widget' ) ) {
    add_action( 'widgets_init', 'b7ectg_unregister_widget' );
    
    function b7ectg_unregister_widget() {
    
        unregister_widget( '0000000000001db50000000000000000' );
    }
}

// Remove default summary rendering if you call individual functions manually
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

// Mostrar rating sempre (mesmo quando não houver reviews)
add_action( 'woocommerce_single_product_summary', 'my_always_show_product_rating', 10 );
function my_always_show_product_rating() {
    global $product;
    if ( ! $product ) return;

    $avg   = (float) $product->get_average_rating(); // média (0..5)
    $count = (int)   $product->get_rating_count();   // número de avaliações

    // wrapper com classes para estilização
    echo '<div class="my-always-rating">';

    if ( $count > 0 ) {
        // mostra média + contador normal
        echo '<div class="rating-stars" aria-label="Avaliação média: ' . esc_attr( $avg ) . ' de 5">';
        echo wc_get_rating_html( $avg ); // HTML padrão das estrelas
        echo '</div>';
        echo '<div class="rating-count">(' . intval( $count ) . ' avaliações)</div>';
    } else {
        // não há avaliações: mostra estrelas vazias + rótulo "Sem avaliações"
        // wc_get_rating_html(0) gera o HTML das estrelas com 0 valor (vazias)
        echo '<div class="rating-stars no-reviews" aria-label="Sem avaliações">';
        echo wc_get_rating_html( 0 );
        echo '</div>';
        echo '<div class="rating-count no-reviews-text">Sem avaliações</div>';
    }

    echo '</div>'; // .my-always-rating
}

/**
 * Override template for Woodmart product carousel widgets.
 */
add_filter( 'wc_get_template_part', function( $template, $slug, $name ) {

    // Só mexe quando é o template de produtos
    if ( $slug === 'content' && $name === 'product' ) {

        // Detecta se estamos em um widget/carrossel do Woodmart
        // O Woodmart carrega carrosséis dentro desses wrappers:
        $is_carousel =
            did_action('woodmart_products_shortcode') ||
            did_action('woodmart_shortcode_products_tab') ||
            did_action('woodmart_shortcode_products') ||
            ( ! empty( $GLOBALS['woodmart_shortcode_is_carousel'] ) );

        if ( $is_carousel ) {

            // Verifica se existe o template customizado no child theme
            $custom = get_stylesheet_directory() . '/woocommerce/content-product-carousel.php';

            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
    }

    return $template;
}, 20, 3 );

if ( ! function_exists( 'woodmart_my_account_links' ) ) {
	function woodmart_my_account_links() {
		if ( ! woodmart_get_opt( 'my_account_links' ) ) {
			return;
		}

		// Função auxiliar: carrega e sanitiza um SVG do tema
		function woodmart_get_inline_svg( $name ) {
			$path = get_template_directory() . '/assets/icons/' . sanitize_file_name( $name ) . '.svg';
			if ( ! file_exists( $path ) ) {
				return ''; // arquivo não encontrado
			}
			$svg = file_get_contents( $path );
			// Sanitização: remove scripts e tags inseguras (básica)
			$allowed_tags = array(
				'svg' => array(
					'xmlns' => true,
					'viewBox' => true,
					'width' => true,
					'height' => true,
					'role' => true,
					'aria-hidden' => true,
					'focusable' => true,
				),
				'path' => array(
					'd' => true,
					'fill' => true,
					'stroke' => true,
					'stroke-width' => true,
					'opacity' => true,
				),
				'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true ),
				'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ),
				'polygon' => array( 'points' => true, 'fill' => true ),
				'g' => array( 'fill' => true, 'transform' => true ),
			);
			$svg = wp_kses( $svg, $allowed_tags );
			// Garantir que o SVG use currentColor para permitir color change via CSS.
			// Substitui fills inline comuns por 'fill="currentColor"' quando apropriado.
			$svg = preg_replace( '/fill="(?!none)[^"]*"/i', 'fill="currentColor"', $svg );
			return $svg;
		}

		// opcional: mapeamento se os nomes dos arquivos não corresponderem aos endpoints
		$icon_map = array(
			'orders'      => 'orders',
			'downloads'   => 'downloads',
			'edit-address'=> 'address',
			'payment-methods' => 'payment',
			'customer-logout'  => 'logout',
			'dashboard'   => 'dashboard',
			'edit-account'=> 'account',
			'orders'      => 'orders',
			'downloads'   => 'downloads',
		);

		?>
		<div class="wd-my-account-links wd-grid<?php echo woodmart_get_old_classes( ' woodmart-my-account-links' ); ?>">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
				$icon_name = isset( $icon_map[ $endpoint ] ) ? $icon_map[ $endpoint ] : $endpoint;
				$svg = woodmart_get_inline_svg( $icon_name );
			?>
				<div class="<?php echo esc_attr( $endpoint ); ?>-link wd-account-link">
					<a class="wd-account-anchor" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
						<?php if ( $svg ) : ?>
							<span class="wd-account-icon" aria-hidden="true"><?php echo $svg; // já sanitizado ?></span>
						<?php endif; ?>
						<span class="wd-account-label"><?php echo esc_html( $label ); ?></span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
	add_action( 'woocommerce_account_dashboard', 'woodmart_my_account_links', 10 );
}


