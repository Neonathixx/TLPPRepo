const LOCAL_STORAGE_KEY = "tlpp_cart";

function getCartData() {
  const stored = localStorage.getItem(LOCAL_STORAGE_KEY);
  return stored ? JSON.parse(stored) : { items: [], discount: 0, promoApplied: false };
}

function saveCartData(cartData) {
  localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(cartData));
}

const cart = getCartData();

const container = document.getElementById("cartItemsContainer");
const subtotalEl = document.getElementById("summarySubtotal");
const discountRow = document.getElementById("discountRow");
const discountEl = document.getElementById("summaryDiscount");
const totalEl = document.getElementById("summaryTotal");
const promoInput = document.getElementById("promoCode");
const applyPromoBtn = document.getElementById("applyPromo");
const promoMessage = document.getElementById("promoMessage");

function renderCart() {
  if (cart.items.length === 0) {
    container.innerHTML = `
      <div class="empty-cart" style="text-align:center; padding:60px 20px;">
        <i data-lucide="shopping-cart" style="width:64px; height:64px; color:var(--brown); margin-bottom:16px;"></i>
        <p style="font-family:'Bangers',system-ui; font-size:32px; color:var(--brown);">Your basket is empty</p>
        <p style="font-family:'Roboto Slab',serif; color:var(--black);">Add some treats from our menu!</p>
      </div>
    `;
    lucide.createIcons();
    updateSummary();
    return;
  }

  container.innerHTML = cart.items
    .map(
      (item) => `
    <div class="cart-item" data-id="${item.id}">
      <div class="cart-item-image">
        <img src="${item.image}" alt="${item.name}">
      </div>
      <div class="cart-item-details">
        <p class="cart-item-name">${item.name}</p>
        <p class="cart-item-desc">${item.desc}</p>
        <button class="cart-item-remove" data-remove="${item.id}">
          <i data-lucide="trash-2"></i> Remove
        </button>
      </div>
      <div class="cart-item-quantity">
        <button class="qty-btn" data-action="decrease" data-id="${item.id}">−</button>
        <span class="qty-value">${item.qty}</span>
        <button class="qty-btn" data-action="increase" data-id="${item.id}">+</button>
      </div>
      <div class="cart-item-subtotal">
        <span class="sub-label">SUBTOTAL</span>
        <span class="sub-price">₱${(item.price * item.qty).toFixed(2)}</span>
      </div>
    </div>
  `
    )
    .join("");

  lucide.createIcons();
  attachEventListeners();
  updateSummary();
}

function attachEventListeners() {
  document.querySelectorAll(".qty-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const id = parseInt(btn.dataset.id);
      const action = btn.dataset.action;
      const item = cart.items.find((i) => i.id === id);
      if (!item) return;
      if (action === "increase") item.qty += 1;
      else if (action === "decrease") {
        item.qty -= 1;
        if (item.qty <= 0) {
          cart.items = cart.items.filter((i) => i.id !== id);
        }
      }
      saveCartData(cart);
      renderCart();
    });
  });

  document.querySelectorAll(".cart-item-remove").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const id = parseInt(btn.dataset.remove);
      cart.items = cart.items.filter((i) => i.id !== id);
      saveCartData(cart);
      renderCart();
    });
  });
}

function updateSummary() {
  const subtotal = cart.items.reduce((sum, item) => sum + item.price * item.qty, 0);
  subtotalEl.textContent = `₱${subtotal.toFixed(2)}`;

  if (cart.promoApplied && cart.discount > 0 && cart.items.length > 0) {
    const discountAmount = (subtotal * cart.discount) / 100;
    discountRow.style.display = "flex";
    discountEl.textContent = `- ₱${discountAmount.toFixed(2)}`;
    totalEl.textContent = `₱${(subtotal - discountAmount).toFixed(2)}`;
  } else {
    discountRow.style.display = "none";
    totalEl.textContent = `₱${subtotal.toFixed(2)}`;
  }
}

// Promo codes – placeholder, no codes work yet
applyPromoBtn.addEventListener("click", () => {
  promoMessage.style.color = "var(--brown)";
  promoMessage.textContent = "Promo codes are coming soon!";
});

// ---------- REAL CHECKOUT ----------
document.getElementById("checkoutBtn").addEventListener("click", async () => {
  if (cart.items.length === 0) {
    alert("Your basket is empty!");
    return;
  }

  try {
    const res = await fetch("checkout.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        items: cart.items,
        total: totalEl.textContent.replace("₱", ""),
      }),
    });

    if (res.status === 401) {
      // Not logged in – redirect to account page
      localStorage.setItem("tlpp_return_to", window.location.href);
      window.location.href = "account.html";
      return;
    }

    if (!res.ok) throw new Error("Checkout failed");

    // Clear local cart and go to orders page
    cart.items = [];
    saveCartData(cart);
    window.location.href = "orders.html";
  } catch (err) {
    alert("Checkout failed. Please try again.");
  }
});

// Initial render
renderCart();