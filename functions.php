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


