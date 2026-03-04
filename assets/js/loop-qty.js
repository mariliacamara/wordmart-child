(function () {

  if (!window.jQuery) return;

  jQuery(function ($) {

    function getQtyFromCard(buttonEl) {
      const card = buttonEl.closest('li.product, li.product-grid-item');
      if (!card) return 1;

      const input = card.querySelector('input.qty');
      if (!input) return 1;

      const qty = parseInt(input.value, 10);
      return Number.isFinite(qty) && qty > 0 ? qty : 1;
    }

    $(document).on('click', 'a.ajax_add_to_cart.add-to-cart-loop', function (e) {

      e.preventDefault(); // 🔥 Cancela o comportamento do tema

      const $button = $(this);
      const productId = $button.data('product_id');
      const qty = getQtyFromCard(this);

      if (!productId) return;

      $button.addClass('loading');

      $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.ajax_url,
        data: {
          action: 'woocommerce_ajax_add_to_cart',
          product_id: productId,
          quantity: qty
        },
        success: function (response) {

          if (!response) return;

          if (response.error && response.product_url) {
            window.location = response.product_url;
            return;
          }

          // Atualiza fragments (mini cart)
          if (response.fragments) {
            $.each(response.fragments, function (key, value) {
              $(key).replaceWith(value);
            });
          }

          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
        },
        complete: function () {
          $button.removeClass('loading');
        }
      });

    });

  });

})();