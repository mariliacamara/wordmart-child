<?php 
/* Child theme generated with WPS Child Theme Generator */
            
if ( ! function_exists( 'b7ectg_theme_enqueue_styles' ) ) {            
    add_action( 'wp_enqueue_scripts', 'b7ectg_theme_enqueue_styles' );
    
    function b7ectg_theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    }
}

/**
 * Ensure child CSS loads AFTER Woodmart and all other theme styles.
 */
add_action( 'wp_enqueue_scripts', function() {

    // Remove any previously enqueued child style (like the generator's)
    wp_dequeue_style( 'child-style' );
    wp_deregister_style( 'child-style' );

    // Re-register LAST
    wp_enqueue_style(
        'child-final-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('woodmart-style'), // garante que vem DEPOIS do Woodmart
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

}, 999 ); // prioridade altíssima para sair por último

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

document.addEventListener("click", (e) => {
  const close = e.target.closest(".quick-shop-close a");
  if (!close) return;

  const item = close.closest("li.product-grid-item");
  if (!item) return;

  // remove estados
  item.classList.remove("quick-shop-shown", "quick-shop-loaded");

  // remove wrapper injetado
  const qs = item.querySelector(".quick-shop-wrapper");
  if (qs) qs.remove();
});
