console.log("JS-ը միացած է!");

/* ============================================================
   ԶԱՄԲՅՈՒՂԻ AJAX — առանց էջի reload-ի
   ============================================================ */

document.addEventListener("click", function (e) {
    const btn = e.target.closest(".add-to-cart, .del-btn, .minus-btn, .plus-btn");
    if (!btn) return;

    /* Out-of-stock ստուգում */
    if (btn.classList.contains("add-to-cart")) {
        const card = btn.closest(".product");
        if (card && card.classList.contains("out-of-stock")) {
            showNotification("Ապրանքը սպառված է", false);
            return;
        }
    }

    /* Կանգնեցնում է էջի reload-ը */
    e.preventDefault();
    e.stopPropagation();

    btn.disabled = true;

    const productId = btn.getAttribute("data-id");
    console.log("Սեղմված կոճակի ID-ն:", productId);
    /* --- ADD --- */
    if (btn.classList.contains("add-to-cart")) {
    const data = { id: productId };
    if (btn.hasAttribute("data-quantity")) {
        data.quantity = btn.getAttribute("data-quantity");
    }
    sendAction("add.php", data, btn);
    return;
}

    /* --- DELETE --- */
    if (btn.classList.contains("del-btn")) {
        sendAction("remove.php", { id: productId }, btn);
        return;
    }

    /* --- MINUS --- */
    if (btn.classList.contains("minus-btn")) {
        const row = btn.closest(".cart-item");
        const currentQty = parseInt(row.querySelector(".qty-value").textContent, 10);

        if (currentQty <= 1) {
            sendAction("remove.php", { id: productId }, btn);
        } else {
            sendAction("update.php", { id: productId, quantity: currentQty - 1 }, btn);
        }
        return;
    }

    /* --- PLUS --- */
    if (btn.classList.contains("plus-btn")) {
        const row = btn.closest(".cart-item");
        const currentQty = parseInt(row.querySelector(".qty-value").textContent, 10);
        sendAction("update.php", { id: productId, quantity: currentQty + 1 }, btn);
        return;
    }
});

/* ============================================================
   Ուղարկում է AJAX հարցում
   ============================================================ */
function sendAction(url, data, btn) {
    const body = new URLSearchParams(data);

    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (btn.classList.contains("add-to-cart")) {
                showPlusOne(btn);
                bounceCart();

                /* Թարմացնում ենք out-of-stock վիճակը */
                const card = btn.closest(".product");
                if (card) {
                    let avail = parseInt(card.getAttribute("data-available") || "0", 10) - 1;
                    card.setAttribute("data-available", avail);
                    if (avail <= 0) {
                        card.classList.add("out-of-stock");
                        btn.textContent = "Սպառվել է";
                    }
                }
            }
            refreshCart();
            updateCartCount();
        } else {
            showNotification(data.message, false);
        }
        btn.disabled = false;
    })
    .catch(() => {
        showNotification("Սխալ տեղի ունեցավ", false);
        btn.disabled = false;
    });
}

/* ============================================================
   Թարմացնում է զամբյուղի բովանդակությունը
   ============================================================ */
function refreshCart() {
    const cartContent = document.getElementById("cart-content");
    if (!cartContent) return;

    fetch("cart.php")
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newContent = doc.getElementById("cart-content");
            if (newContent) {
                cartContent.innerHTML = newContent.innerHTML;
            }
        });
}

/* ============================================================
   +1 անիմացիա կոճակի վրա
   ============================================================ */
function showPlusOne(btn) {
    const card = btn.closest(".product");
    if (!card) return;

    const el = document.createElement("span");
    el.className = "plus-one";
    el.textContent = "+1";
    card.appendChild(el);

    setTimeout(() => el.remove(), 700);
}

/* ============================================================
   Զամբյուղի bounce անիմացիա
   ============================================================ */
function bounceCart() {
    const cart = document.querySelector(".cart-header");
    if (!cart) return;

    cart.classList.remove("bounce");
    void cart.offsetWidth; /* reflow, որ անիմացիան վերագործարկվի */
    cart.classList.add("bounce");
    setTimeout(() => cart.classList.remove("bounce"), 400);
}

/* ============================================================
   Թարմացնում է զամբյուղի թվաքանակը
   ============================================================ */
function updateCartCount() {
    const countEl = document.querySelector(".cart-count");
    if (!countEl) return;

    fetch("cart.php")
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const items = doc.querySelectorAll("#cart-content .cart-item");
            const newCount = items.length;

            countEl.textContent = newCount;

            countEl.classList.remove("flash");
            void countEl.offsetWidth;
            countEl.classList.add("flash");
            setTimeout(() => countEl.classList.remove("flash"), 600);
        });
}

/* ============================================================
   Notification — զուսպ, աջ-վերևում
   ============================================================ */
function showNotification(message, success) {
    const existing = document.querySelector(".notification");
    if (existing) existing.remove();

    const notification = document.createElement("div");
    notification.className = "notification " + (success ? "success" : "error");
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(function () {
        notification.style.opacity = "0";
        setTimeout(() => notification.remove(), 300);
    }, 2500);
}

/* ============================================================
   Կատեգորիայի ֆիլտր
   ============================================================ */
const categoryFilter = document.getElementById("categoryFilter");
if (categoryFilter) {
    categoryFilter.addEventListener("change", function () {
        const selected = this.value;

        document.querySelectorAll(".category-block").forEach(block => {
            const cat = block.getAttribute("data-category");

            if (selected === "all") {
                block.style.display = (cat === "all") ? "block" : "none";
            } else {
                block.style.display = (cat === selected) ? "block" : "none";
            }
        });
    });

    /* Սկզբում ցույց տալ միայն «Բոլորը» */
    document.querySelectorAll(".category-block").forEach(block => {
        const cat = block.getAttribute("data-category");
        if (cat && cat !== "all") block.style.display = "none";
    });
}

/* ============================================================
   LIVE SEARCH — dropdown ցուցակ որոնման դաշտի տակ
   ============================================================ */

(function() {
    const input = document.querySelector('input[name="search"]');
    if (!input) return;

    /* Ստեղծում ենք dropdown-ի div-ը, դնում ենք որոնման դաշտի ծնողի մեջ */
    const wrapper = input.closest('.search');
    if (!wrapper) return;

    wrapper.style.position = 'relative';

    const dropdown = document.createElement('div');
    dropdown.className = 'search-dropdown';
    dropdown.style.display = 'none';
    wrapper.appendChild(dropdown);

    let debounceTimer = null;
    let lastQuery = '';

    /* Input-ի վրա լսում ենք ամեն սեղմում */
    input.addEventListener('input', function() {
        const q = input.value.trim();

        clearTimeout(debounceTimer);

        /* Եթե դատարկ է → փակիր dropdown */
        if (q === '') {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            lastQuery = '';
            return;
        }

        /* Եթե նույնն է, ինչ նախորդ → ոչինչ չանենք */
        if (q === lastQuery) return;

        /* 300ms սպասում, հետո ուղարկում */
        debounceTimer = setTimeout(function() {
            lastQuery = q;
            fetchSearchResults(q);
        }, 300);
    });

    /* AJAX հարցում */
    function fetchSearchResults(q) {
        fetch('search-ajax.php?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                renderDropdown(data, q);
            })
            .catch(() => {
                dropdown.style.display = 'none';
            });
    }

    /* Dropdown-ի ցուցադրում */
    function renderDropdown(data, q) {
        /* Եթե արդյունք չկա */
        if (data.count === 0) {
            dropdown.innerHTML = `
                <div class="search-empty">
                    Ոչինչ չի գտնվել «${escapeHtml(q)}»-ի համար
                </div>
            `;
            dropdown.style.display = 'block';
            return;
        }

        /* Կառուցում ենք ցուցակը */
        let html = '';
        data.results.forEach(function(item) {
            const discountHtml = item.discount > 0
                ? `<span class="search-item-discount">-${item.discount} Դ</span>`
                : '';

            html += `
                <a href="index.php?search=${encodeURIComponent(item.name)}" class="search-item">
                    <span class="search-item-name">${highlight(item.name, q)}</span>
                    <span class="search-item-price">
                        ${discountHtml}
                        <span class="search-item-final">${item.final} Դ</span>
                    </span>
                </a>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
    }

    /* Որոնվող բառի նշում կանաչ/ոսկե գույնով */
    function highlight(text, q) {
        if (!q) return escapeHtml(text);
        const escaped = escapeHtml(text);
        const regex = new RegExp('(' + escapeRegex(escapeHtml(q)) + ')', 'gi');
        return escaped.replace(regex, '<mark>$1</mark>');
    }

    /* Ապահովության համար */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /* Փակել dropdown-ը դրսում կլիկ անելիս */
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    /* Փակել Escape-ով */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });
})();

/* ============================================================
   ՔԱՆԱԿԻ ԸՆՏՐԻՉ — product.php
   ============================================================ */
(function() {
    const qtyInput = document.getElementById('qtyInput');
    const minusBtn = document.getElementById('qtyMinus');
    const plusBtn  = document.getElementById('qtyPlus');

    if (!qtyInput || !minusBtn || !plusBtn) return;

    const max = parseInt(qtyInput.getAttribute('data-max'), 10) || 1;

    function updateButtons() {
        const val = parseInt(qtyInput.value, 10);
        minusBtn.disabled = val <= 1;
        plusBtn.disabled  = val >= max;
        minusBtn.style.opacity = val <= 1 ? '0.4' : '1';
        plusBtn.style.opacity  = val >= max ? '0.4' : '1';
    }

    minusBtn.addEventListener('click', function() {
        const val = parseInt(qtyInput.value, 10);
        if (val > 1) {
            qtyInput.value = val - 1;
            updateButtons();
        }
    });

    plusBtn.addEventListener('click', function() {
        const val = parseInt(qtyInput.value, 10);
        if (val < max) {
            qtyInput.value = val + 1;
            updateButtons();
        }
    });

    updateButtons();

    /* Երբ «Ավելացնել զամբյուղ» սեղմվի — ուղարկում ենք նաև քանակը */
    const addBtn = document.getElementById('addBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            /* Սպասում ենք, որ script.js-ի հիմնական handler-ը արձագանքի,
               բայց նաև ավելացնում ենք quantity-ն fetch-ի body-ին */
            const qty = qtyInput.value;
            /* Փոխանցում ենք quantity-ն որպես data attribute,
               որը կկարդա sendAction-ը */
            addBtn.setAttribute('data-quantity', qty);
        });
    }
})();


