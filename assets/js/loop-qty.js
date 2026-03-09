(function () {

  document.addEventListener('click', function (e) {

    const btn = e.target.closest('.plus, .minus');
    if (!btn) return;

    const qtyWrap = btn.closest('.quantity');
    if (!qtyWrap) return;

    const input = qtyWrap.querySelector('input.qty');
    if (!input) return;

    e.preventDefault();

    const min = input.min ? parseFloat(input.min) : 1;

    // 🔥 aqui está a regra do max=-1
    const maxAttr = input.getAttribute('max');
    const max = (!maxAttr || maxAttr === '-1') ? Infinity : parseFloat(maxAttr);

    const step = input.step ? parseFloat(input.step) : 1;

    let val = parseFloat(input.value) || 0;

    if (btn.classList.contains('plus')) {
      val += step;
    } else {
      val -= step;
    }

    if (val < min) val = min;
    if (val > max) val = max;

    input.value = val;

    input.dispatchEvent(new Event('change', { bubbles: true }));

  });

})();