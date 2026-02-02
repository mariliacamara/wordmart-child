// Atualiza data-quantity do botão com o valor do input antes do Ajax add to cart
document.addEventListener('click', (e) => {
  const btn = e.target.closest('a.ajax_add_to_cart.add-to-cart-loop');
  if (!btn) return;

  const selector = btn.getAttribute('data-qty-target');
  if (!selector) return;

  const input = btn.closest('.wd-add-btn')?.querySelector(selector);
  if (!input) return;

  const qty = parseInt(input.value, 10);
  btn.setAttribute('data-quantity', Number.isFinite(qty) && qty > 0 ? String(qty) : '1');
});

// Botões + e - do teu contador
document.addEventListener('click', (e) => {
  const minus = e.target.closest('.quantity .minus');
  const plus  = e.target.closest('.quantity .plus');
  if (!minus && !plus) return;

  const wrap = e.target.closest('.quantity');
  const input = wrap?.querySelector('input.qty');
  if (!input) return;

  const step = parseInt(input.getAttribute('step') || '1', 10);
  const min  = parseInt(input.getAttribute('min') || '1', 10);
  const maxAttr = input.getAttribute('max');
  const max  = maxAttr ? parseInt(maxAttr, 10) : null;

  let val = parseInt(input.value || String(min), 10);
  if (!Number.isFinite(val)) val = min;

  val = plus ? val + step : val - step;
  if (val < min) val = min;
  if (max !== null && val > max) val = max;

  input.value = String(val);
  input.dispatchEvent(new Event('change', { bubbles: true }));
});
