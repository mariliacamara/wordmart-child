document.addEventListener("click", (e) => {
  const close = e.target.closest(".quick-shop-close a");
  if (!close) return;

  const item = close.closest("li.product-grid-item");
  if (!item) return;

  // remove estados
  item.classList.remove("quick-shop-shown", "quick-shop-loaded");

  // remove wrapper injetado
  const qs = item.querySelector(".quick-shop-wrapper");
  if (qs) qs.remove();
});
