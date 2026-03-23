<?php 
/* Child theme generated with WPS Child Theme Generator */
            
if ( ! function_exists( 'b7ectg_theme_enqueue_styles' ) ) {            
    add_action( 'wp_enqueue_scripts', 'b7ectg_theme_enqueue_styles' );
    
    function b7ectg_theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    }
}

add_action('wp_enqueue_scripts', function () {
  // parent
  wp_enqueue_style(
    'woodmart-parent',
    get_template_directory_uri() . '/style.css',
    [],
    wp_get_theme(get_template())->get('Version')
  );

  // child (depois do parent)
  wp_enqueue_style(
    'woodmart-child',
    get_stylesheet_directory_uri() . '/style.css',
    ['woodmart-parent'],
    wp_get_theme()->get('Version')
  );
}, 20);

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

function zincomed_child_scripts() {
    wp_enqueue_script(
        'zincomed-cat-toggle',
        get_stylesheet_directory_uri() . '/js/categories-fix.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'zincomed_child_scripts', 9999);

function zincomed_override_categories_script() {
    ?>
    <script type="text/javascript">
    (function($){
      // Espera o DOM e também que o objeto exista
      function overrideAccordion() {
        if (typeof woodmartThemeModule === 'undefined') {
          return false;
        }

        // substitui a função exatamente como combinamos
        woodmartThemeModule.categoriesAccordion = function() {
          if (typeof woodmart_settings !== 'undefined' && woodmart_settings.categories_toggle === 'no') {
            return;
          }

          var $widget = $('.widget_product_categories'),
              $list   = $widget.find('.product-categories'),
              time    = 300;

          $list.find('.cat-parent').each(function() {
            var $this = $(this);

            if ($this.find(' > .wd-cats-toggle').length > 0) return;
            if ($this.find(' > .children').length === 0 || $this.find(' > .children > *').length === 0) return;

            var $link = $this.find('> a').first();
            if ($link.length) {
              $link.before('<div class="wd-cats-toggle"></div>');
            } else {
              $this.prepend('<div class="wd-cats-toggle"></div>');
            }
          });

          $list.off('click.zincomed').on('click.zincomed', '.wd-cats-toggle', function(e) {
            e.preventDefault();
            var $btn     = $(this),
                $subList = $btn.siblings('ul.children').first();

            if (!$subList.length) $subList = $btn.closest('li').find('> .children').first();

            if ($subList.hasClass('list-shown')) {
              $btn.removeClass('toggle-active');
              $subList.stop().slideUp(time).removeClass('list-shown');
            } else {
              $subList.parent().parent().find('> li > .list-shown').stop().slideUp().removeClass('list-shown');
              $subList.parent().parent().find('> li > .toggle-active').removeClass('toggle-active');
              $btn.addClass('toggle-active');
              $subList.stop().slideDown(time).addClass('list-shown');
            }
          });

          if ($list.find('li.current-cat.cat-parent, li.current-cat-parent').length > 0) {
            $list.find('li.current-cat.cat-parent, li.current-cat-parent').find('> .wd-cats-toggle').trigger('click');
          }
        };

        // chama uma vez agora (se já houver markup)
        try { woodmartThemeModule.categoriesAccordion(); } catch(e) {}
        return true;
      }

      // tenta executar depois do DOM pronto e também a cada 300ms até conseguir (máx 10 tentativas)
      $(function(){
        var tries = 0;
        var i = setInterval(function(){
          if (overrideAccordion() || ++tries > 10) clearInterval(i);
        }, 300);
      });

    })(jQuery);
    </script>
    <?php
}
add_action('wp_footer', 'zincomed_override_categories_script', 9999);

// Detecta Mac e adiciona classe no body para ajustes de CSS
add_action('wp_head', function () {
  ?>
  <script>
  (function () {
    var ua = navigator.userAgent || '';
    var isMac = ua.indexOf('Macintosh') !== -1 || ua.indexOf('Mac OS X') !== -1;
    if (isMac) {
      document.documentElement.classList.add('is-mac');
      if (document.body) {
        document.body.classList.add('is-mac');
      } else {
        document.addEventListener('DOMContentLoaded', function () {
          document.body.classList.add('is-mac');
        });
      }
    }
  })();
  </script>
  <?php
}, 1);

// Esconde o campo de quantidade quando só há 1 unidade disponível
add_filter( 'woocommerce_quantity_input_args', function( $args, $product ) {
  if ( $product && $product->managing_stock() && $product->get_stock_quantity() <= 1 ) {
    $args['min_value'] = 1;
    $args['max_value'] = 1;
    $args['input_value'] = 1;
  }
  return $args;
}, 10, 2 );

add_filter( 'woocommerce_is_sold_individually', function( $sold_individually, $product ) {
  if ( $product && $product->managing_stock() && $product->get_stock_quantity() <= 1 ) {
    return true;
  }
  return $sold_individually;
}, 10, 2 );

add_filter( 'woocommerce_product_add_to_cart_text', function() {
  return 'COMPRAR';
});

add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
  return 'COMPRAR';
});

add_filter('woocommerce_product_add_to_cart_text', function ($text, $product) {
  if ( $product && $product->is_type('variable') ) {
    return __('Ver opções', 'woocommerce');
  }
  return $text;
}, 20, 2);

add_action( 'wp_enqueue_scripts', function () {
  wp_enqueue_script(
    'woodmart-autoupdate-cart',
    get_stylesheet_directory_uri() . '/assets/js/autoupdate-cart.js',
    array(), 
    '1.0',
    true
  );
}, 20 );

add_filter( 'woocommerce_my_account_my_orders_actions', function ( $actions, $order ) {

	if ( isset( $actions['pay'] ) ) {
		$actions['pay']['name'] = __( 'Mudar forma de pagamento', 'woocommerce' );
	}

	return $actions;

}, 10, 2 );

add_action( 'wp_enqueue_scripts', function () {
  if ( is_admin() ) return;

  // Script para loop de produtos (não carrega no carrinho/checkout)
  if ( ! is_cart() && ! is_checkout() ) {
    wp_enqueue_script(
      'woodmart-child-loop-qty',
      get_stylesheet_directory_uri() . '/assets/js/loop-qty.js',
      array( 'jquery' ),
      '1.0.1',
      true
    );
  }

  // Script para botões +/- APENAS na página de produto único
  if ( is_product() ) {
    wp_enqueue_script(
      'woodmart-child-single-product-qty',
      get_stylesheet_directory_uri() . '/assets/js/single-product-qty.js',
      array(),
      '1.0.1',
      true
    );
  }
}, 20 );

add_action( 'wp_enqueue_scripts', function() {

	wp_enqueue_script(
		'product-cards',
		get_stylesheet_directory_uri() . '/assets/js/product-cards.js',
		[],
		'1.0',
		true
	);

}, 20 );