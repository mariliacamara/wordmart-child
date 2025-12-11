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

  function buildOptions(select) {
    if (!select) return;
    if (select.dataset.converted === '1') return;
    select.dataset.converted = '1';
    select.classList.add('size-converter-select');

    // cria container visual
    const container = document.createElement('div');
    container.className = 'size-options';

    Array.from(select.options).forEach((opt, idx) => {
      const val = opt.value;
      const text = opt.textContent.trim();
      if (!val) return; // pula placeholder

      // label visual (contém input invisível + span dentro do quadrado)
      const label = document.createElement('label');
      label.className = 'size-label';
      label.setAttribute('data-val', val);

      const input = document.createElement('input');
      input.type = 'radio';
      input.name = 'product_size_converted';
      input.value = val;

      // se select já tem valor selecionado, marca visual
      if (select.value === val) {
        label.classList.add('is-checked');
        input.checked = true;
      }

      const span = document.createElement('span');
      span.textContent = text;

      label.appendChild(input);
      label.appendChild(span);
      container.appendChild(label);

      // clique no label -> sincroniza select e dispara change
      label.addEventListener('click', function (e) {
        // previne comportamento se já está selecionado
        if (!input.checked) {
          input.checked = true;
        }
        // atualiza select
        select.value = val;
        // dispara change para WooCommerce reagir
        const evt = new Event('change', { bubbles: true });
        select.dispatchEvent(evt);

        // atualiza estilos visuais
        container.querySelectorAll('.size-label').forEach(l => l.classList.remove('is-checked'));
        label.classList.add('is-checked');
      });
    });

    return container;
  }

  function restructureTable(select) {
    const table = select.closest('table.variations');
    if (!table) return;

    // evita refazer se já reestruturado
    if (table.dataset.restructured === '1') return;
    table.dataset.restructured = '1';

    const origTr = select.closest('tr');
    if (!origTr) return;

    // extrai a label do <th>
    const th = origTr.querySelector('th.label.cell');
    const labelText = th ? th.textContent.trim() : 'Opção';

    // cria nova tr só com label (th spanning full)
    const labelTr = document.createElement('tr');
    labelTr.className = 'size-label-row';
    const newTh = document.createElement('th');
    newTh.colSpan = 2; // ocupa as 2 colunas (th + td)
    newTh.innerHTML = `<label>${labelText}</label>`;
    labelTr.appendChild(newTh);

    // cria tr com as opções: td ocupa toda largura (ou manter th vazio)
    const optionsTr = document.createElement('tr');
    optionsTr.className = 'size-options-row';
    const emptyTh = document.createElement('th');
    emptyTh.className = 'label cell';
    emptyTh.innerHTML = ''; // mantemos sem texto
    const td = document.createElement('td');
    td.className = 'value cell';

    // move select e reset link para o td
    const reset = origTr.querySelector('.wd-reset-var');
    // remove o tr original
    origTr.parentNode.removeChild(origTr);

    td.appendChild(select); // move o select para dentro do new td
    if (reset) td.appendChild(reset);
    optionsTr.appendChild(emptyTh);
    optionsTr.appendChild(td);

    // insere labelTr + optionsTr no tbody
    const tbody = table.querySelector('tbody') || table;
    tbody.insertBefore(labelTr, tbody.firstChild);
    tbody.insertBefore(optionsTr, labelTr.nextSibling);

    // constrói container de opções e insere
    const container = buildOptions(select);
    if (container) {
      // insere logo após o select dentro do td
      td.insertBefore(container, select.nextSibling);

      // sincronia: quando select muda (por outro script) atualiza visual
      select.addEventListener('change', function () {
        const v = select.value;
        container.querySelectorAll('.size-label').forEach(l => {
          if (l.getAttribute('data-val') === v) {
            l.classList.add('is-checked');
            const inp = l.querySelector('input');
            if (inp) inp.checked = true;
          } else {
            l.classList.remove('is-checked');
            const inp = l.querySelector('input');
            if (inp) inp.checked = false;
          }
        });
      });

      // esconder select visualmente (já adicionamos classe no buildOptions)
      select.classList.add('size-converter-select');
    }
  }

  function init() {
    const select = document.querySelector(SELECT_SELECTOR);
    if (!select) return;
    restructureTable(select);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else init();

  // observer para caso o select seja re-renderizado pelo WooCommerce
  const tableRoot = document.querySelector('table.variations') || document.body;
  const mo = new MutationObserver((mutations) => {
    const sel = document.querySelector(SELECT_SELECTOR);
    if (sel && sel.dataset.converted !== '1') {
      restructureTable(sel);
    }
  });
  mo.observe(tableRoot, { childList: true, subtree: true });
})();

</script>