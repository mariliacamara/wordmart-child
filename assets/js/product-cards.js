document.addEventListener("click", function(e) {
    const btn = e.target.closest(".custom-wishlist-remove");
    if (!btn) return;

    e.preventDefault();

    const productId = btn.dataset.productId;
    const card = btn.closest("li.product");

    fetch(`/wishlist/?remove_from_wishlist=${productId}`, {
        credentials: "same-origin"
    }).then(() => {
        card.style.opacity = "0";
        setTimeout(() => card.remove(), 300);
    });
});