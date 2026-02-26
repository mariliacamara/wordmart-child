(function () {
  'use strict';

  // Não executa no carrinho ou checkout
  if (document.body.classList.contains('woocommerce-cart') ||
      document.body.classList.contains('woocommerce-checkout')) {
    return;
  }

  let initialized = false;

  function init() {
    if (initialized) return;

    // Só pega botões que NÃO estão no carrinho
    const plusButtons = document.querySelectorAll('.single-product .quantity .plus, .related-products .quantity .plus');
    const minusButtons = document.querySelectorAll('.single-product .quantity .minus, .related-products .quantity .minus');

    if (plusButtons.length === 0 && minusButtons.length === 0) {
      return;
    }

    initialized = true;

    // Remove qualquer pointer-events: none que possa estar bloqueando
    document.querySelectorAll('.quantity, .quantity *, .single_add_to_cart_button, .cart').forEach(el => {
      el.style.pointerEvents = 'auto';
    });

    function onQtyClick(e) {
      const btn = e.target.closest('.plus, .minus');
      if (!btn) return;

      const qtyWrap = btn.closest('.quantity');
      if (!qtyWrap) return;

      const input = qtyWrap.querySelector('input.qty, input[type="number"]');
      if (!input) return;

      e.preventDefault();
      e.stopPropagation();
      if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();

      const minAttr = input.getAttribute('min');
      const min = minAttr && minAttr !== '' ? Math.max(1, parseFloat(minAttr)) : 1;
      const maxAttr = input.getAttribute('max');
      const max = maxAttr && maxAttr !== '' ? parseFloat(maxAttr) : Infinity;
      const stepAttr = input.getAttribute('step');
      const step = stepAttr && stepAttr !== 'any' && stepAttr !== '' ? parseFloat(stepAttr) : 1;

      let val = input.value !== '' ? parseFloat(input.value) : min;
      if (Number.isNaN(val) || val < 1) val = min;

      const isPlus = btn.classList.contains('plus');
      val = isPlus ? val + step : val - step;
      
      // Garante que nunca fica abaixo de 1
      if (val < 1) val = 1;
      if (val < min) val = min;
      if (val > max) val = max;

      input.value = String(val);
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    plusButtons.forEach(btn => {
      if (!btn.hasAttribute('data-qty-listener')) {
        btn.setAttribute('data-qty-listener', 'true');
        btn.addEventListener('click', onQtyClick, true);
      }
    });

    minusButtons.forEach(btn => {
      if (!btn.hasAttribute('data-qty-listener')) {
        btn.setAttribute('data-qty-listener', 'true');
        btn.addEventListener('click', onQtyClick, true);
      }
    });

    const observer = new MutationObserver(() => {
      const newPlus = document.querySelectorAll('.single-product .quantity .plus:not([data-qty-listener]), .related-products .quantity .plus:not([data-qty-listener])');
      const newMinus = document.querySelectorAll('.single-product .quantity .minus:not([data-qty-listener]), .related-products .quantity .minus:not([data-qty-listener])');
      
      if (newPlus.length > 0 || newMinus.length > 0) {
        newPlus.forEach(btn => {
          btn.setAttribute('data-qty-listener', 'true');
          btn.addEventListener('click', onQtyClick, true);
        });
        
        newMinus.forEach(btn => {
          btn.setAttribute('data-qty-listener', 'true');
          btn.addEventListener('click', onQtyClick, true);
        });
      }
    });

    observer.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  setTimeout(init, 500);
  setTimeout(init, 1500);
})();

// Made with Bob
