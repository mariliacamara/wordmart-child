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

    // 🔥 AJAX add to cart
    $(document).on('click', 'a.ajax_add_to_cart.add-to-cart-loop', function (e) {

      e.preventDefault();

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

    // 🔥 AQUI ENTRA A CORREÇÃO DO PLUS / MINUS
    // 🔥 Corrige valor sempre que mudar
        $(document).on('change input', 'li.product input.qty, li.product-grid-item input.qty', function () {

          const $input = $(this);

          let val = parseInt($input.val(), 10);
          let minAttr = parseInt($input.attr('min') || '1', 10);
          let min = Math.max(1, isNaN(minAttr) ? 1 : minAttr);

          let maxAttr = $input.attr('max');
          let max = null;

          // ignora max="-1"
          if (maxAttr && maxAttr !== '-1' && !isNaN(parseInt(maxAttr, 10))) {
            max = parseInt(maxAttr, 10);
          }

          if (!Number.isFinite(val) || val < min) {
            val = min;
          }

          if (max !== null && val > max) {
            val = max;
          }

          $input.val(val);
        });

  });

})();