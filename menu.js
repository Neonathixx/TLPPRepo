// menu.js – add to cart with login guard (production)

function getCart() {
  const stored = localStorage.getItem("tlpp_cart");
  return stored ? JSON.parse(stored) : { items: [] };
}

function saveCart(cartObject) {
  localStorage.setItem("tlpp_cart", JSON.stringify(cartObject));
}

// Check login status using the backend
async function isLoggedIn() {
  try {
    const res = await fetch("check_session.php");
    const data = await res.json();
    return data.logged_in === true;
  } catch {
    return false;
  }
}

// Add to cart button handlers
document.querySelectorAll(".btn-add-cart").forEach((btn) => {
  btn.addEventListener("click", async () => {
    // ---------- Login guard ----------
    const loggedIn = await isLoggedIn();
    if (!loggedIn) {
      // Save current page so user returns after login
      localStorage.setItem("tlpp_return_to", window.location.href);
      window.location.href = "account.html";
      return;
    }

    // ---------- Add item ----------
    const id = parseInt(btn.dataset.id);
    const name = btn.dataset.name;
    const price = parseFloat(btn.dataset.price);
    const image = btn.dataset.image;
    const desc = btn.dataset.desc;

    const cart = getCart();
    const existing = cart.items.find((item) => item.id === id);

    if (existing) {
      existing.qty += 1;
    } else {
      cart.items.push({ id, name, price, image, desc, qty: 1 });
    }

    saveCart(cart);

    // Visual feedback
    const originalHTML = btn.innerHTML;
    btn.innerHTML = `<i data-lucide="check"></i> Added!`;
    lucide.createIcons();
    setTimeout(() => {
      btn.innerHTML = originalHTML;
      lucide.createIcons();
    }, 1500);
  });
});