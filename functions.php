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

<?php
// Colar no functions.php do tema filho

// 1) Remove a ação do tema pai (executa AFTER parent)
add_action( 'after_setup_theme', function() {
    // tenta remover a função original do pai (se foi adicionada com o mesmo nome/prioridade)
    remove_action( 'woocommerce_account_dashboard', 'woodmart_my_account_links', 10 );

    // registra a nova função do child
    add_action( 'woocommerce_account_dashboard', 'woodmart_my_account_links_child', 10 );
}, 20 ); // prioridade 20 para garantir que o parent já registrou

// 2) Função customizada no child
if ( ! function_exists( 'woodmart_my_account_links_child' ) ) {
    function woodmart_get_inline_svg_child( $name ) {
        $path = get_stylesheet_directory() . '/assets/icons/' . sanitize_file_name( $name ) . '.svg';
        if ( ! file_exists( $path ) ) {
            // debug log - só enquanto estiver resolvendo (remova depois)
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( "[SVG] not found: $path" );
            }
            return '';
        }
        $svg = file_get_contents( $path );
        // sanitização básica
        $allowed = array(
            'svg' => array( 'xmlns' => true, 'viewBox' => true, 'width' => true, 'height' => true ),
            'path' => array( 'd' => true, 'fill' => true, 'stroke' => true ),
            'g' => array( 'fill' => true ),
            'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true ),
            'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
        );
        $svg = wp_kses( $svg, $allowed );
        $svg = preg_replace( '/fill="(?!none)[^"]*"/i', 'fill="currentColor"', $svg );
        return $svg;
    }

    function woodmart_my_account_links_child() {
        if ( ! function_exists( 'woodmart_get_opt' ) || ! woodmart_get_opt( 'my_account_links' ) ) {
            return;
        }

        $icon_map = array(
            'dashboard'       => 'dashboard',
            'orders'          => 'orders',
            'downloads'       => 'downloads',
            'edit-address'    => 'address',
            'payment-methods' => 'payment',
            'customer-logout' => 'logout',
            'edit-account'    => 'edit-account',
        );

        echo '<div class="wd-my-account-links wd-grid' . esc_attr( woodmart_get_old_classes( ' woodmart-my-account-links' ) ) . '">';

        foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
            $icon_name = isset( $icon_map[ $endpoint ] ) ? $icon_map[ $endpoint ] : $endpoint;
            $svg = woodmart_get_inline_svg_child( $icon_name );
            ?>
            <div class="<?php echo esc_attr( $endpoint ); ?>-link wd-account-link">
                <a class="wd-account-anchor" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
                    <?php if ( $svg ) : ?>
                        <span class="wd-account-icon" aria-hidden="true"><?php echo $svg; ?></span>
                    <?php endif; ?>
                    <span class="wd-account-label"><?php echo esc_html( $label ); ?></span>
                </a>
            </div>
            <?php
        }

        echo '</div>';
    }
}



