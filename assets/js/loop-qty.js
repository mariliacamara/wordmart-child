(function () {
  // Pega a qty do card do botão clicado
  function getQtyFromCard(buttonEl) {
    const card = buttonEl.closest('li.product, li.product-grid-item');
    if (!card) return 1;

    const input = card.querySelector('input.qty, input.input-text.qty');
    if (!input) return 1;

    const qty = parseInt(input.value, 10);
    return Number.isFinite(qty) && qty > 0 ? qty : 1;
  }

  // 1) Atualiza data-quantity no clique (fallback)
  document.addEventListener(
    'click',
    (e) => {
      const btn = e.target.closest('a.ajax_add_to_cart.add-to-cart-loop');
      if (!btn) return;

      const qty = getQtyFromCard(btn);
      btn.setAttribute('data-quantity', String(qty));
    },
    true // capture = garante que roda antes de outros handlers
  );

  // 2) Hook oficial do WooCommerce (o que realmente resolve)
  if (window.jQuery) {
    jQuery(function ($) {
      $(document.body).on('adding_to_cart', function (e, $button, data) {
        // só nos botões do teu loop
        if (!$button || !$button.hasClass('add-to-cart-loop')) return;

        const qty = getQtyFromCard($button.get(0));

        // injeta no payload do ajax (isso é o pulo do gato)
        data.quantity = qty;

        // e mantém o atributo coerente
        $button.attr('data-quantity', qty).data('quantity', qty);
      });

      // Botões + e - (se tu usa)
      $(document).on('click', '.quantity .plus, .quantity .minus', function () {
        const $wrap = $(this).closest('.quantity');
        const $input = $wrap.find('input.qty');
        if (!$input.length) return;

        const step = parseInt($input.attr('step') || '1', 10);
        const min  = parseInt($input.attr('min') || '1', 10);
        const maxAttr = $input.attr('max');
        const max  = maxAttr ? parseInt(maxAttr, 10) : null;

        let val = parseInt($input.val() || String(min), 10);
        if (!Number.isFinite(val)) val = min;

        val = $(this).hasClass('plus') ? val + step : val - step;
        if (val < min) val = min;
        if (max !== null && val > max) val = max;

        $input.val(val).trigger('change');
      });
    });
  }
})();
