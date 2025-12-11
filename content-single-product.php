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
          <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
        </div>
      </div>

      <!-- RIGHT: Summary -->
      <div class="col-lg-6 wd-product-summary-col">
        <div class="wd-summary-inner">

          <?php
            // BRAND NAME acima do título + logo ao lado (robusto)
            global $product;

            $attr = woodmart_get_opt( 'brands_attribute' );
            $brand_term = null;
            $brand_logo_url = null;

            // 1) Pegar o termo configurado como brand (atributo)
            if ( $attr ) {
                $terms = wc_get_product_terms(
                    $product->get_id(),
                    $attr,
                    array( 'fields' => 'all' )
                );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    $brand_term = $terms[0];

                    // 2) Tentar várias meta keys onde a imagem pode estar
                    $image_meta = get_term_meta( $brand_term->term_id, 'image', true );       // woodmart sometimes use 'image' array
                    $image_id_meta = get_term_meta( $brand_term->term_id, 'image_id', true ); // some versions
                    $thumb_id_meta = get_term_meta( $brand_term->term_id, 'thumbnail_id', true ); // WP standard

                    // Normalize into a URL
                    if ( $image_meta ) {
                        // sometimes it's array with ['id'] or it's already a URL
                        if ( is_array( $image_meta ) && ! empty( $image_meta['id'] ) ) {
                            $brand_logo_url = wp_get_attachment_image_url( intval( $image_meta['id'] ), 'full' );
                        } elseif ( is_numeric( $image_meta ) ) {
                            $brand_logo_url = wp_get_attachment_image_url( intval( $image_meta ), 'full' );
                        } else {
                            // assume it's a URL string
                            $brand_logo_url = esc_url_raw( $image_meta );
                        }
                    }

                    if ( ! $brand_logo_url && $image_id_meta ) {
                        if ( is_numeric( $image_id_meta ) ) {
                            $brand_logo_url = wp_get_attachment_image_url( intval( $image_id_meta ), 'full' );
                        }
                    }

                    if ( ! $brand_logo_url && $thumb_id_meta ) {
                        if ( is_numeric( $thumb_id_meta ) ) {
                            $brand_logo_url = wp_get_attachment_image_url( intval( $thumb_id_meta ), 'full' );
                        }
                    }

                    // final fallback: sometimes theme stores directly term meta 'image' as attachment array
                    // already covered above
                }
            }

            // 3) Fallback para meta antiga _brand_name (só nome)
            if ( ! $brand_term ) {
                $fallback_name = get_post_meta( get_the_ID(), '_brand_name', true );
                if ( $fallback_name ) {
                    $brand_term = (object) array( 'name' => $fallback_name );
                }
            }
            ?>

            <?php if ( $brand_term ) : ?>
              <div class="my-brand-wrapper">

                <div class="my-title-row">
                  <div>
                    <div class="my-brand-name">
                      <?php echo esc_html( $brand_term->name ); ?>
                    </div>
                    <h1 class="product-title"><?php the_title(); ?></h1>
                  </div>

                  <?php if ( $brand_logo_url ) : ?>
                    <div class="my-brand-logo">
                      <a href="<?php echo esc_url( get_term_link( $brand_term ) ); ?>" aria-label="<?php echo esc_attr( $brand_term->name ); ?>">
                        <img src="<?php echo esc_url( $brand_logo_url ); ?>" alt="<?php echo esc_attr( $brand_term->name ); ?>">
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php else : ?>
              <h1 class="product-title"><?php the_title(); ?></h1>
            <?php endif; ?>


          <!-- Excerpt -->
          <div class="product-excerpt">
            <?php the_excerpt(); ?>
          </div>

          <!-- PRICE -->
          <div class="wd-price-pill">
            <?php woocommerce_template_single_price(); ?>
          </div>

          <!-- ⭐ RATING (FORÇADO) -->
          <div class="my-rating-force">
            <?php
            $avg   = (float) $product->get_average_rating();
            $count = (int) $product->get_rating_count();

            echo '<div class="my-rating-inner">';
            echo wc_get_rating_html( $avg );
            if ( $count > 0 ) {
                echo '<span class="count">(' . $count . ')</span>';
            } else {
                echo '<span class="count no-reviews">Sem avaliações</span>';
            }
            echo '</div>';
            ?>
          </div>

          <!-- Shipping -->
          <div class="wd-delivery">
            <?php
            if ( function_exists( 'woodmart_get_shipping_text' ) ) {
              echo woodmart_get_shipping_text();
            } else {
              echo '<span>Entrega entre 24h a 48h para Portugal continental.</span>';
            }
            ?>
          </div>

          <!-- ADD TO CART -->
          <div class="wd-add-to-cart-wrap">
            <?php woocommerce_template_single_add_to_cart(); ?>
          </div>

          <!-- Variations placeholder -->
          <div class="wd-variations-boxes"></div>

          <!-- META (apenas SKU — sem categorias, sem stock) -->
          <div class="wd-product-meta">
            <?php
            $sku = $product->get_sku();
            if ( $sku ) {
                echo '<div class="product-sku"><strong>REF:</strong> ' . esc_html( $sku ) . '</div>';
            }
            ?>
          </div>

          <!-- Informação adicional -->
          <div class="wd-additional-info-below-sku">
             <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
          </div>

        </div>
      </div>

    </div> <!-- .row -->
  </div> <!-- .wd-product-top -->

  <section class="related-products wd-related-products container">
    <h2 class="wd-related-title">PRODUTOS RELACIONADOS</h2>
    <div class="wd-related-grid">
        <?php
        $related_ids = wc_get_related_products( $product->get_id(), 4 );
        if ( ! empty( $related_ids ) ) {
            $args = array(
                'post_type' => 'product',
                'post__in'  => $related_ids,
                'orderby'   => 'post__in'
            );
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) {
                $loop->the_post();
                wc_get_template_part( 'content', 'product-related' );
            }
            wp_reset_postdata();
        }
        ?>
    </div>
  </section>

</div>

<script>
  (function () {
  const SELECT_SELECTOR = 'select#pa_tamanho, select[name="attribute_pa_tamanho"]';

  function buildFromSelect(select) {
    if (!select || select.dataset.converted === '1') return;
    select.dataset.converted = '1';
    select.classList.add('size-converter-select');

    // criar container
    const container = document.createElement('div');
    container.className = 'size-options';
    container.setAttribute('aria-hidden', 'false');

    // for each option
    Array.from(select.options).forEach((opt, idx) => {
      const val = opt.value;
      const text = opt.textContent.trim();
      // pula placeholder / vazio
      if (!val) return;
      const label = document.createElement('label');
      label.className = 'size-label';

      const input = document.createElement('input');
      input.type = 'radio';
      input.name = 'product_size_converted';
      input.value = val;
      input.className = 'size-option';
      input.style.display = 'inline-block';
      input.dataset.originalIndex = idx;

      // marca se o select tiver o valor
      if (select.value === val) {
        input.checked = true;
        input.classList.add('is-checked');
      }

      // conteúdo visível (texto simples). Se quiser preço, modifique aqui.
      const span = document.createElement('span');
      span.textContent = text;

      label.appendChild(input);
      label.appendChild(span);
      container.appendChild(label);

      // evento de clique que sincroniza o select (dispara change)
      input.addEventListener('change', function () {
        // atualiza select
        select.value = val;
        // dispara change para que WooCommerce detecte variação
        const evt = new Event('change', { bubbles: true });
        select.dispatchEvent(evt);

        // atualiza visual de todos
        container.querySelectorAll('.size-option').forEach(i => {
          i.classList.toggle('is-checked', i.checked);
        });
      });

      // também faz clique no label para marcar
      label.addEventListener('click', function (e) {
        // se input já estiver checked, não precisa reenviar
        if (!input.checked) input.checked = true;
        // dispara change manualmente (alguns browsers não disparam automaticamente ao alterar programaticamente)
        const ev = new Event('change', { bubbles: true });
        input.dispatchEvent(ev);
      });
    });

    // inserir container logo após o select dentro da célula .value
    const parentTd = select.closest('td') || select.parentNode;
    parentTd.insertBefore(container, select.nextSibling);

    // adiciona listener no select para atualizar UI se o select mudar por outro script
    select.addEventListener('change', function () {
      const current = select.value;
      container.querySelectorAll('.size-option').forEach(inp => {
        inp.checked = (inp.value === current);
        inp.classList.toggle('is-checked', inp.checked);
      });
    });

    // se houver link de reset, exibe/esconde conforme select value
    const resetLink = parentTd.querySelector('.reset_variations');
    if (resetLink) {
      // inicial
      resetLink.style.visibility = select.value ? 'visible' : 'hidden';
      select.addEventListener('change', () => {
        resetLink.style.visibility = select.value ? 'visible' : 'hidden';
      });
      resetLink.addEventListener('click', (ev) => {
        // ao limpar, remove seleção visual
        setTimeout(() => {
          container.querySelectorAll('.size-option').forEach(i => { i.checked = false; i.classList.remove('is-checked'); });
        }, 20);
      });
    }
  }

  function initOnce() {
    const select = document.querySelector(SELECT_SELECTOR);
    if (select) buildFromSelect(select);
  }

  // inicializa ao carregar DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOnce);
  } else initOnce();

  // MutationObserver para recapturar caso WooCommerce substitua o select dinamicamente
  const target = document.querySelector('table.variations') || document.body;
  const mo = new MutationObserver((mutations) => {
    const sel = document.querySelector(SELECT_SELECTOR);
    if (sel && sel.dataset.converted !== '1') buildFromSelect(sel);
  });
  mo.observe(target, { childList: true, subtree: true });
})();
</script>