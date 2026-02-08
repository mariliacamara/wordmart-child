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

          <?php if ( ! $product->is_in_stock() ) : ?>
            <div class="oos-svg-badge" aria-hidden="true">
              <?php echo file_get_contents( get_stylesheet_directory() . '/assets/icons/outofstock.svg' ); ?>
            </div>
          <?php endif; ?>
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

          <!-- META (SKU + Categoria) -->
          <div class="wd-product-meta">
            <?php
            $sku = $product->get_sku();

            if ( $sku ) {
                echo '<div class="product-sku"><strong>REF:</strong> ' . esc_html( $sku ) . '</div>';
            }

            // categorias do produto
            $categories = wc_get_product_category_list( $product->get_id(), ', ' );

            if ( $categories ) {
                echo '<div class="product-category"><strong>Categoria:</strong> ' . $categories . '</div>';
            }
            ?>
          </div>


          <!-- Informação adicional -->
        </div>
      </div>

    </div> <!-- .row -->
  </div> <!-- .wd-product-top -->

  <section class="product-tabs-wrapper">
    <div class="container product-tabs-inner">
      <?php woocommerce_output_product_data_tabs(); ?>
    </div>
  </section>

  <section class="related-products wd-related-products container">
  <h2 class="wd-related-title">PRODUTOS RELACIONADOS</h2>

  <?php
  $limit = 12;
  $related_ids = wc_get_related_products( $product->get_id(), $limit );

  if ( ! empty( $related_ids ) ) :

    $carousel_id = 'carousel-related-' . wp_unique_id();

    $args = array(
      'post_type'      => 'product',
      'post__in'       => $related_ids,
      'orderby'        => 'post__in',
      'posts_per_page' => $limit,
    );

    $loop = new WP_Query( $args );
  ?>

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

      <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <div class="slide-product owl-carousel-item">
          <?php
            // usa teu template de card (o mesmo que você já chama hoje)
            wc_get_template_part( 'content', 'product-related' );
          ?>
        </div>
      <?php endwhile; ?>

      <?php wp_reset_postdata(); ?>

    </div>
  </div>

  <?php endif; ?>
</section>


</div>

<script>
(function () {
  const SELECT_SELECTOR = 'select#pa_tamanho, select[name="attribute_pa_tamanho"]';
  const FORM_SELECTOR = 'form.variations_form';

  // util: remove tags HTML
  function stripHtml(html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html || '';
    return tmp.textContent || tmp.innerText || '';
  }

  function getVariationsData(form) {
    if (!form) return null;
    const raw = form.getAttribute('data-product_variations') || form.dataset.productVariations || form.dataset.product_variations;
    if (!raw) return null;
    try {
      // raw pode estar JSON-escaped (com &quot;), tentar unescape
      const jsonText = raw.trim();
      // se começa com '[' então já é JSON
      if (/^\s*\[/.test(jsonText)) {
        return JSON.parse(jsonText);
      } else {
        // tentar substituir &quot; e depois parse
        const unescaped = jsonText.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
        return JSON.parse(unescaped);
      }
    } catch (e) {
      console.warn('Could not parse product_variations JSON', e);
      return null;
    }
  }

  function buildOptions(select, variations) {
    if (!select) return null;
    if (select.dataset.converted === '1') return null;
    select.dataset.converted = '1';
    select.classList.add('size-converter-select');

    const container = document.createElement('div');
    container.className = 'size-options';

    Array.from(select.options).forEach((opt) => {
      const val = opt.value;
      let text = opt.textContent.trim();
      if (!val) return; // placeholder

      // achar variação correspondente nas variations (matching attribute_pa_tamanho)
      let priceText = '';
      if (variations && Array.isArray(variations)) {
        const found = variations.find(v => {
          if (!v.attributes) return false;
          // várias formas de key; preferimos attribute_pa_tamanho
          return v.attributes['attribute_pa_tamanho'] === val || v.attributes['attribute_tamanho'] === val || v.attributes['attribute_pa_tamanho'] === val.replace(/\s+/g, '-');
        });
        if (found) {
          // price_html sometimes like '<span class="price">3.57€</span>'
          if (found.price_html) priceText = stripHtml(found.price_html).trim();
          else if (typeof found.display_price !== 'undefined') {
            // número: formatar com 2 decimais + símbolo euro
            priceText = Number(found.display_price).toFixed(2) + '€';
          }
        }
      }

      // Atualiza texto do option para incluir preço (texto simples)
      if (priceText) {
        // evita duplicar se já tiver o preço
        const baseText = opt.getAttribute('data-base-text') || text;
        opt.setAttribute('data-base-text', baseText);
        opt.text = baseText + ' — ' + priceText;
      }

      // label visual (contém input invisível + span com size + price)
      const label = document.createElement('label');
      label.className = 'size-label';
      label.setAttribute('data-val', val);

      const input = document.createElement('input');
      input.type = 'radio';
      input.name = 'product_size_converted';
      input.value = val;

      if (select.value === val) {
        label.classList.add('is-checked');
        input.checked = true;
      }

      const span = document.createElement('span');
      span.className = 'size-text';
      span.textContent = text;

      // price element dentro do quadrado
      const priceEl = document.createElement('small');
      priceEl.className = 'size-price';
      priceEl.textContent = priceText || '';

      label.appendChild(input);
      label.appendChild(span);
      label.appendChild(priceEl);
      container.appendChild(label);

      label.addEventListener('click', function () {
        if (!input.checked) input.checked = true;
        select.value = val;
        const evt = new Event('change', { bubbles: true });
        select.dispatchEvent(evt);

        container.querySelectorAll('.size-label').forEach(l => l.classList.remove('is-checked'));
        label.classList.add('is-checked');
      });
    });

    return container;
  }

  function placeBelowAddToCart(select) {
    const form = document.querySelector(FORM_SELECTOR);
    const variationsData = getVariationsData(form);
    const targetBox = document.querySelector('.wd-variations-boxes') || (function () {
      const addWrap = document.querySelector('.wd-add-to-cart-wrap');
      if (addWrap && addWrap.parentNode) {
        const fallback = document.createElement('div');
        fallback.className = 'wd-variations-boxes';
        addWrap.parentNode.insertBefore(fallback, addWrap.nextSibling);
        return fallback;
      }
      return document.body;
    })();

    // limpa se já houver
    targetBox.querySelectorAll('.size-label-row, .size-options, .reset-wrapper').forEach(n => n.remove());

    const labelText = (() => {
      const origTh = select.closest('tr') ? select.closest('tr').querySelector('th.label.cell') : null;
      return origTh ? origTh.textContent.trim() : 'Opção';
    })();

    const labelRow = document.createElement('div');
    labelRow.className = 'size-label-row';
    labelRow.textContent = labelText;

    const container = buildOptions(select, variationsData);
    if (!container) return;

    // reset link handling (clona)
    const origTr = select.closest('tr');
    const resetOriginal = origTr ? origTr.querySelector('.wd-reset-var .reset_variations') : null;
    let resetWrapper = null;
    if (resetOriginal) {
      const clone = resetOriginal.cloneNode(true);
      clone.style.visibility = resetOriginal.style.visibility || 'hidden';
      clone.addEventListener('click', function () {
        setTimeout(() => {
          container.querySelectorAll('.size-label').forEach(l => l.classList.remove('is-checked'));
        }, 50);
      });
      resetWrapper = document.createElement('div');
      resetWrapper.className = 'reset-wrapper';
      resetWrapper.appendChild(clone);
    }

    targetBox.appendChild(labelRow);
    targetBox.appendChild(container);
    if (resetWrapper) targetBox.appendChild(resetWrapper);

    // esconder origem
    if (origTr) origTr.style.display = 'none';

    // sincronia quando select muda (por WooCommerce)
    select.addEventListener('change', function () {
      const v = select.value;
      container.querySelectorAll('.size-label').forEach(l => {
        const inp = l.querySelector('input');
        if (l.getAttribute('data-val') === v) {
          l.classList.add('is-checked');
          if (inp) inp.checked = true;
        } else {
          l.classList.remove('is-checked');
          if (inp) inp.checked = false;
        }
      });

      if (resetOriginal) resetOriginal.style.visibility = select.value ? 'visible' : 'hidden';
    });

    // inicial: atualiza reset visibility
    if (resetOriginal) resetOriginal.style.visibility = select.value ? 'visible' : 'hidden';
  }

  function init() {
    const select = document.querySelector(SELECT_SELECTOR);
    if (!select) return;
    placeBelowAddToCart(select);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else init();

  // observer caso WooCommerce re-renderize o select/form
  const root = document.querySelector('form.variations_form') || document.body;
  const mo = new MutationObserver(() => {
    const sel = document.querySelector(SELECT_SELECTOR);
    if (sel && sel.dataset.converted !== '1') {
      placeBelowAddToCart(sel);
    }
  });
  mo.observe(root, { childList: true, subtree: true });
})();
</script>
