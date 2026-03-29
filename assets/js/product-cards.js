// document.addEventListener("click", function(e) {
//     const btn = e.target.closest(".wd-wishlist-remove");
//     if (!btn) return;

//     e.preventDefault();

//     const productId = btn.dataset.productId;
//     const wrapper = document.querySelector(".wd-wishlist-content");

//     fetch(`/?remove_from_wishlist=${productId}`, {
//         credentials: "same-origin"
//     }).then(() => {

//         fetch(window.location.href, { credentials: "same-origin" })
//             .then(r => r.text())
//             .then(html => {

//                 const parser = new DOMParser();
//                 const doc = parser.parseFromString(html, "text/html");

//                 const newContent = doc.querySelector(".wd-wishlist-content");

//                 if (wrapper && newContent) {
//                     wrapper.innerHTML = newContent.innerHTML;
//                 }

//             });

//     });
// });

function attachWishlistRemoveUX() {
    document.querySelectorAll(".wd-wishlist-remove").forEach(btn => {

        if (btn.dataset.bound) return;
        btn.dataset.bound = "true";

        btn.addEventListener("click", function() {

            const card = btn.closest("li.product");

            setTimeout(() => {
                if (card) {
                    card.style.transition = "opacity .2s ease";
                    card.style.opacity = "0";

                    setTimeout(() => card.remove(), 200);
                }
            }, 50);

        });

    });
}

// inicial
attachWishlistRemoveUX();

// sempre que o Woodmart atualizar a wishlist
document.body.addEventListener("wdWishlistRefresh", attachWishlistRemoveUX);