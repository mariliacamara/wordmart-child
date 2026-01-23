document.addEventListener("click", (e) => {
  const close = e.target.closest(".quick-shop-close a");
  if (!close) return;

  const product = close.closest(".wd-product");
  if (!product) return;

  // remove estado do quick shop
  product.classList.remove("quick-shop-shown", "quick-shop-loaded");

  // remove o wrapper injetado (evita lixo visual)
  const qs = product.querySelector(".quick-shop-wrapper");
  if (qs) qs.remove();
});
