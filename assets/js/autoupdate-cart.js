jQuery(function($) {

  function initAutoUpdate() {
    let timeout;

    $('.woocommerce-cart-form').off('input.autoUpdate change.autoUpdate');

    $('.woocommerce-cart-form').on('input.autoUpdate change.autoUpdate', 'input.qty', function() {
      let $input = $(this);
      let value = parseInt($input.val());

      // 🔒 impede 0 ou menor
      if (!value || value < 1) {
        $input.val(1);
        value = 1;
      }

      if (timeout) clearTimeout(timeout);

      timeout = setTimeout(function() {
        const $btn = $('[name="update_cart"]');

        if ($btn.length) {
          $btn.prop('disabled', false);
          $btn.trigger('click');
        }
      }, 500);
    });
  }

  // init inicial
  initAutoUpdate();

  // 🔁 rebind após update AJAX do WooCommerce
  $(document.body).on('updated_wc_div updated_cart_totals', function() {
    initAutoUpdate();
  });

});