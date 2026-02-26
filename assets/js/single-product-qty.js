(function () {
  'use strict';

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
      // some themes listen on document with capture; então trava geral
      if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
    }
  }

  // captura bem cedo
  ['pointerdown','mousedown','touchstart','click'].forEach(evt => {
    document.addEventListener(evt, stopOwlSteal, true);
  });

  // 2) Implementa + e - de forma universal (funciona em clones do Owl também)
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

    val = btn.classList.contains('plus') ? val + step : val - step;
    if (val < min) val = min;
    if (val > max) val = max;

    input.value = String(val);
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
  }

  // Adiciona os event listeners para os botões +/-
  ['click', 'pointerdown'].forEach(evt => {
    document.addEventListener(evt, onQtyClick, true);
  });

  const relatedSection = document.querySelector('.related-products');

  if (relatedSection) {
    ['pointerdown','mousedown','touchstart','click'].forEach(evt => {
      relatedSection.addEventListener(evt, stopOwlSteal, true);
    });
  }
})();

// Made with Bob
