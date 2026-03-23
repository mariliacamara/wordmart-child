jQuery(function($) {
  let timeout;

  $('.woocommerce-cart-form').on('input change', 'input.qty', function(){
    if (timeout) clearTimeout(timeout);

    timeout = setTimeout(function() {
      const $btn = $('[name="update_cart"]');

      if ($btn.length) {
        $btn.prop('disabled', false);
        $btn.trigger('click');
      }
    }, 500);
  });
});