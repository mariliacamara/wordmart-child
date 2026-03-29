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

  function injectWishlistRemove() {

    if (!location.pathname.includes('wishlist')) return;

    document.querySelectorAll('li.product-grid-item').forEach(card => {

      if (card.querySelector('.wd-wishlist-remove')) return;

      const id = card.dataset.id;
      if (!id) return;

      const btn = document.createElement('a');

      btn.className = 'wd-wishlist-remove';
      btn.href = '?remove_from_wishlist=' + id;
      btn.textContent = '× Remover';
      btn.setAttribute('aria-label','Remover da wishlist');

      card.prepend(btn);

    });

  }

document.addEventListener('click', function (e) {
  const btn = e.target.closest('.wd-wishlist-remove');
  if (!btn) return;

  e.preventDefault();
  e.stopPropagation();
  if (e.stopImmediatePropagation) e.stopImmediatePropagation();

  const card = btn.closest('li.product, li.product-grid-item');
  const productId = card?.dataset.id || btn.getAttribute('data-product-id');

  if (!productId) {
    console.error('Product ID not found');
    return;
  }

  // Woodmart usa seu próprio sistema de wishlist
  const ajaxurl = window.woodmart_settings?.ajaxurl || '/wp-admin/admin-ajax.php';

  if (typeof jQuery !== 'undefined') {
    
    // Feedback visual
    if (card) {
      card.style.opacity = '0.5';
      card.style.pointerEvents = 'none';
    }

    // Ação correta para Woodmart
    jQuery.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'woodmart_remove_from_wishlist',
        product_id: productId
      },
      success: function(response) {
        if (card) {
          card.style.opacity = '0';
          card.style.transition = 'opacity 0.3s ease';
          setTimeout(() => {
            card.remove();
            
            // Verifica se lista ficou vazia
            const productList = document.querySelector('.products, ul.products');
            if (productList && productList.querySelectorAll('li.product').length === 0) {
              location.reload();
            }
          }, 300);
        }
      },
      error: function() {
        // Fallback: recarrega a página
        location.reload();
      }
    });
    
  } else {
    // Sem jQuery: recarrega a página
    location.reload();
  }

}, true);




  // document.addEventListener('click', function (e) {
  //   const btn = e.target.closest('.wd-wishlist-remove');
  //   if (!btn) return;

  //   const card = btn.closest('li.product, li.product-grid-item');

  //   if (card) {
  //     card.remove();
  //   }
  // });

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

  const mo = new MutationObserver(() => {
    patchCards();
    injectWishlistRemove();
  });
  mo.observe(document.body, { childList: true, subtree: true });

})();