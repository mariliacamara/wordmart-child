document.addEventListener("click", function(e) {
    const btn = e.target.closest(".wd-wishlist-remove");
    if (!btn) return;

    e.preventDefault();

    const productId = btn.dataset.productId;
    const card = btn.closest("li.product");

    fetch(`/wishlist/?remove_from_wishlist=${productId}`, {
        credentials: "same-origin"
    }).then(() => {

        if (card) {
            card.style.opacity = "0";
            card.style.transition = "opacity .2s ease";

            setTimeout(() => {
                card.remove();
            }, 200);
        }

    });
});