document.addEventListener("click", function(e) {
    const btn = e.target.closest(".wd-wishlist-remove");
    if (!btn) return;

    e.preventDefault();

    const productId = btn.dataset.productId;
    const wrapper = document.querySelector(".wd-wishlist-content");

    fetch(`/?remove_from_wishlist=${productId}`, {
        credentials: "same-origin"
    }).then(() => {

        fetch(window.location.href, { credentials: "same-origin" })
            .then(r => r.text())
            .then(html => {

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");

                const newContent = doc.querySelector(".wd-wishlist-content");

                if (wrapper && newContent) {
                    wrapper.innerHTML = newContent.innerHTML;
                }

            });

    });
});