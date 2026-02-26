(function () {
  'use strict';

  let initialized = false;

  function init() {
    if (initialized) return;

    const plusButtons = document.querySelectorAll('.quantity .plus');
    const minusButtons = document.querySelectorAll('.quantity .minus');

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

      const min = input.min !== '' ? parseFloat(input.min) : 1;
      const max = input.max !== '' ? parseFloat(input.max) : Infinity;
      const step = (input.step && input.step !== 'any') ? parseFloat(input.step) : 1;

      let val = input.value !== '' ? parseFloat(input.value) : min;
      if (Number.isNaN(val)) val = min;

      const isPlus = btn.classList.contains('plus');
      val = isPlus ? val + step : val - step;
      
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
      const newPlus = document.querySelectorAll('.quantity .plus:not([data-qty-listener])');
      const newMinus = document.querySelectorAll('.quantity .minus:not([data-qty-listener])');
      
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
