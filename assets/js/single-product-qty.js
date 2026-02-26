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

    // 1) Não deixa o Owl "capturar" cliques/drag quando for em quantity
    function stopOwlSteal(e) {
      const el = e.target;
      if (!el) return;

      // tudo que deve continuar clicável dentro do carousel
      const clickable = el.closest(
        '.quantity, .quantity .plus, .quantity .minus, input.qty, input[type="number"], button, a'
      );

      if (clickable) {
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
      }
    }

    // captura bem cedo
    ['pointerdown','mousedown','touchstart','click'].forEach(evt => {
      document.addEventListener(evt, stopOwlSteal, true);
    });

    // 2) Implementa + e - de forma universal
    function onQtyClick(e) {
      console.log('🔧 Click detected on:', e.target);
      
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

    // Adiciona os event listeners para os botões +/-
    document.addEventListener('click', onQtyClick, true);
    console.log('🔧 Click listener added');

    const relatedSection = document.querySelector('.related-products');
    if (relatedSection) {
      ['pointerdown','mousedown','touchstart','click'].forEach(evt => {
        relatedSection.addEventListener(evt, stopOwlSteal, true);
      });
      console.log('🔧 Related section listeners added');
    }
  }

  // Executa quando DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

// Made with Bob
