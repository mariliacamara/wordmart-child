(function () {
  'use strict';

  console.log('🔧 Single Product Qty Script Loaded');

  // Flag para evitar execução múltipla
  let initialized = false;

  // Aguarda o DOM estar pronto
  function init() {
    if (initialized) {
      console.log('🔧 Already initialized, skipping');
      return;
    }
    
    console.log('🔧 Init function called');

    // Verifica se os botões existem
    const plusButtons = document.querySelectorAll('.quantity .plus');
    const minusButtons = document.querySelectorAll('.quantity .minus');
    
    console.log('🔧 Plus buttons found:', plusButtons.length);
    console.log('🔧 Minus buttons found:', minusButtons.length);

    if (plusButtons.length === 0 && minusButtons.length === 0) {
      console.log('🔧 No buttons found yet, will retry');
      return; // Não marca como initialized para tentar novamente
    }

    initialized = true;

    // Remove qualquer pointer-events: none que possa estar bloqueando
    document.querySelectorAll('.quantity, .quantity *, .single_add_to_cart_button, .cart').forEach(el => {
      el.style.pointerEvents = 'auto';
    });
    console.log('🔧 Pointer events forced to auto');

    // 2) Implementa + e - de forma universal
    function onQtyClick(e) {
      const btn = e.target.closest('.plus, .minus');
      if (!btn) return;

      console.log('🔧 Button clicked:', btn.className);

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

      console.log('🔧 Updating value from', input.value, 'to', val);

      input.value = String(val);
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Adiciona listeners DIRETAMENTE nos botões (apenas click)
    plusButtons.forEach(btn => {
      if (!btn.hasAttribute('data-qty-listener')) {
        btn.setAttribute('data-qty-listener', 'true');
        btn.addEventListener('click', onQtyClick, true);
        console.log('🔧 Listener added to plus button');
      }
    });

    minusButtons.forEach(btn => {
      if (!btn.hasAttribute('data-qty-listener')) {
        btn.setAttribute('data-qty-listener', 'true');
        btn.addEventListener('click', onQtyClick, true);
        console.log('🔧 Listener added to minus button');
      }
    });

    console.log('🔧 All listeners added successfully');

    // Observer para botões que possam ser adicionados dinamicamente
    const observer = new MutationObserver(() => {
      const newPlus = document.querySelectorAll('.quantity .plus:not([data-qty-listener])');
      const newMinus = document.querySelectorAll('.quantity .minus:not([data-qty-listener])');
      
      if (newPlus.length > 0 || newMinus.length > 0) {
        console.log('🔧 New buttons detected');
        
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
    console.log('🔧 Mutation observer started');
  }

  // Executa quando DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Tenta novamente após delays (caso os botões sejam adicionados depois)
  setTimeout(init, 500);
  setTimeout(init, 1500);
})();

// Made with Bob
