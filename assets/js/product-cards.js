(function () {

  const isMobile = () => window.matchMedia('(max-width: 1024px)').matches;

  function patchCards() {

    if (!isMobile()) return;

    document.querySelectorAll('li.product-grid-item').forEach(card => {

      card.classList.remove('wd-quick-shop');

      card.querySelectorAll('[data-quick-shop], [data-quick-view], .wd-quick-view')
        .forEach(el => {
          el.removeAttribute('data-quick-shop');
          el.removeAttribute('data-quick-view');
        });

    });

  }

  document.addEventListener('click', function (e) {

    const card = e.target.closest('li.product-grid-item');
    if (!card) return;

    if (card.classList.contains('product-type-variable')) {

      const btn = e.target.closest('.wd-add-btn, .add_to_cart_button, .wd-add-btn-replace, a.button');
      if (!btn) return;

      const link = card.querySelector('a.product-image-link');
      if (!link) return;

      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();

      window.location.href = link.href;

    }

  }, true);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', patchCards);
  } else {
    patchCards();
  }

  const mo = new MutationObserver(patchCards);
  mo.observe(document.body, { childList: true, subtree: true });

})();