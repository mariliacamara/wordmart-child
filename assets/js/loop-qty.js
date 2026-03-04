(function () {

  function getQtyFromCard(buttonEl) {
    const card = buttonEl.closest('li.product, li.product-grid-item, .product-wrapper');
    if (!card) return 1;

    const input = card.querySelector('input.qty');
    if (!input) return 1;

    const qty = parseInt(input.value, 10);
    return Number.isFinite(qty) && qty > 0 ? qty : 1;
  }

  if (window.jQuery) {
    jQuery(function ($) {

      // 🔥 Intercepta clique no botão AJAX (archive page)
      $(document).on('click', 'a.ajax_add_to_cart.add-to-cart-loop', function () {
        const $button = $(this);
        const qty = getQtyFromCard(this);

        // Atualiza data-quantity
        $button.attr('data-quantity', qty).data('quantity', qty);

        // 🔥 Força quantity na URL (fallback total)
        const href = $button.attr('href');
        if (href) {
          try {
            const url = new URL(href, window.location.origin);
            url.searchParams.set('quantity', qty);
            $button.attr('href', url.toString());
          } catch (e) {
            console.warn('Erro ao ajustar URL de add-to-cart', e);
          }
        }
      });

      // 🔥 Controle robusto dos botões + e − (somente archive)
      $(document).on(
        'click',
        'li.product .quantity .plus, li.product .quantity .minus, li.product-grid-item .quantity .plus, li.product-grid-item .quantity .minus',
        function () {

          const $wrap = $(this).closest('.quantity');
          const $input = $wrap.find('input.qty');
          if (!$input.length) return;

          const step = parseInt($input.attr('step') || '1', 10);
          const minAttr = parseInt($input.attr('min') || '1', 10);
          const min = Math.max(1, isNaN(minAttr) ? 1 : minAttr);
          const maxAttr = $input.attr('max');
          const max = maxAttr && maxAttr !== '-1' ? parseInt(maxAttr, 10) : null;

          let val = parseInt($input.val() || String(min), 10);
          if (!Number.isFinite(val) || val < 1) val = min;

          if ($(this).hasClass('plus')) {
            val += step;
          } else {
            val -= step;
          }

          if (val < 1) val = 1;
          if (val < min) val = min;
          if (max !== null && val > max) val = max;

          $input.val(val).trigger('change');
        }
      );

    });
  }

})();