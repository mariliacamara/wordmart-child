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
    const btn = e.target.closest('.wd-wishlist-remove');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    if (e.stopImmediatePropagation) e.stopImmediatePropagation();

    const rowId = btn.dataset.rowId || btn.dataset.key;
    if (!rowId) return;

    const card = btn.closest('li.product, li.product-grid-item');

    const ajaxurl = window.woodmart_settings?.ajaxurl || wc_add_to_cart_params?.ajax_url;

    jQuery.post(ajaxurl, {
      action: 'yith_wcwl_remove_from_wishlist',
      remove_from_wishlist: rowId
    });

    if (card) {
      card.remove();
    }

  }, true);

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