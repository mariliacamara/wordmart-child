(function () {
  'use strict';

  console.log('🔧 Single Product Qty Script Loaded');

  // Aguarda o DOM estar pronto
  function init() {
    console.log('🔧 Init function called');

    // Verifica se os botões existem
    const plusButtons = document.querySelectorAll('.quantity .plus');
    const minusButtons = document.querySelectorAll('.quantity .minus');
    
    console.log('🔧 Plus buttons found:', plusButtons.length);
    console.log('🔧 Minus buttons found:', minusButtons.length);

    // Remove qualquer pointer-events: none que possa estar bloqueando
    document.querySelectorAll('.quantity, .quantity *, .single_add_to_cart_button, .cart').forEach(el => {
      el.style.pointerEvents = 'auto';
    });
    console.log('🔧 Pointer events forced to auto');

    // 2) Implementa + e - de forma universal
    function onQtyClick(e) {
      console.log('🔧 Click detected on:', e.target, 'classList:', e.target.classList);
      
      const btn = e.target.closest('.plus, .minus');
      if (!btn) {
        console.log('🔧 Not a plus/minus button');
        return;
      }

      console.log('🔧 Button found:', btn.className);

      const qtyWrap = btn.closest('.quantity');
      if (!qtyWrap) {
        console.log('🔧 No quantity wrapper found');
        return;
      }

      const input = qtyWrap.querySelector('input.qty, input[type="number"]');
      if (!input) {
        console.log('🔧 No input found');
        return;
      }

      console.log('🔧 Input found, current value:', input.value);

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

      console.log('🔧 New value:', val);

      input.value = String(val);
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
      
      console.log('🔧 Value updated successfully');
    }

    // Adiciona listeners DIRETAMENTE nos botões (mais agressivo)
    plusButtons.forEach(btn => {
      console.log('🔧 Adding direct listener to plus button');
      btn.addEventListener('click', onQtyClick, true);
      btn.addEventListener('mousedown', onQtyClick, true);
      btn.addEventListener('pointerdown', onQtyClick, true);
    });

    minusButtons.forEach(btn => {
      console.log('🔧 Adding direct listener to minus button');
      btn.addEventListener('click', onQtyClick, true);
      btn.addEventListener('mousedown', onQtyClick, true);
      btn.addEventListener('pointerdown', onQtyClick, true);
    });

    // Também adiciona no document como fallback
    document.addEventListener('click', onQtyClick, true);
    document.addEventListener('mousedown', onQtyClick, true);
    document.addEventListener('pointerdown', onQtyClick, true);
    
    console.log('🔧 All listeners added');

    // Observer para botões que possam ser adicionados dinamicamente
    const observer = new MutationObserver(() => {
      const newPlus = document.querySelectorAll('.quantity .plus:not([data-listener])');
      const newMinus = document.querySelectorAll('.quantity .minus:not([data-listener])');
      
      if (newPlus.length > 0 || newMinus.length > 0) {
        console.log('🔧 New buttons detected, adding listeners');
        
        newPlus.forEach(btn => {
          btn.setAttribute('data-listener', 'true');
          btn.addEventListener('click', onQtyClick, true);
          btn.addEventListener('mousedown', onQtyClick, true);
          btn.addEventListener('pointerdown', onQtyClick, true);
        });
        
        newMinus.forEach(btn => {
          btn.setAttribute('data-listener', 'true');
          btn.addEventListener('click', onQtyClick, true);
          btn.addEventListener('mousedown', onQtyClick, true);
          btn.addEventListener('pointerdown', onQtyClick, true);
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

  // Também tenta executar após um delay (caso o WooCommerce adicione os botões depois)
  setTimeout(init, 1000);
  setTimeout(init, 2000);
})();

// Made with Bob
