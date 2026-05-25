const container = document.getElementById('orderDetailContainer');

async function loadOrder() {
  const urlParams = new URLSearchParams(window.location.search);
  const orderId = urlParams.get('id');
  if (!orderId) {
    container.innerHTML = '<p class="error-message">Order not found.</p>';
    return;
  }

  try {
    const res = await fetch(`get_order.php?id=${orderId}`);
    if (!res.ok) throw new Error('Failed to load order');
    const order = await res.json();
    renderOrder(order);
  } catch (err) {
    container.innerHTML = '<p class="error-message">Failed to load order. Please try again later.</p>';
  }
}

function renderOrder(order) {
  const itemsHTML = order.items.map(item => `
    <div class="detail-item">
      <img src="${item.image}" alt="${item.name}" class="detail-item-img">
      <div class="detail-item-info">
        <p class="detail-item-name">${item.name}</p>
        <p class="detail-item-meta">Qty: ${item.qty} × ₱${item.price}</p>
        <p class="detail-item-subtotal">₱${(item.qty * item.price).toFixed(2)}</p>
      </div>
    </div>
  `).join('');

  let actionHTML = '';
  switch (order.status) {
    case 'Filing Form':
      actionHTML = `
        <div class="action-form">
          <h3>Complete Your Order</h3>
          <form action="submit_filing_form.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="order_id" value="${order.id}">
            <div class="input-group">
              <label for="pickupDate">Pickup Date</label>
              <input type="date" id="pickupDate" name="pickup_date" required>
            </div>
            <div class="input-group">
              <label for="specialInstructions">Special Instructions</label>
              <textarea id="specialInstructions" name="special_instructions" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit &amp; Request Approval</button>
          </form>
        </div>
      `;
      break;
    case 'Pending Approval':
      actionHTML = '<p class="status-message">Your order is awaiting approval from The Little Paw Patissier. We will notify you once it is reviewed.</p>';
      break;
    case 'Declined':
      actionHTML = `
        <div class="decline-reason">
          <p><strong>Reason:</strong> ${order.decline_reason || 'N/A'}</p>
        </div>
      `;
      break;
    case 'Pending Payment':
      actionHTML = `
        <div class="payment-instructions">
          <h3>Complete Payment</h3>
          <p>Total: <strong>₱${order.total}</strong></p>
          <img src="images/gcash-qr.png" alt="GCash QR Code" class="gcash-qr">
          <p>Scan the QR code and upload your receipt below.</p>
          <form action="submit_payment.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="order_id" value="${order.id}">
            <div class="input-group">
              <label for="receipt">Upload GCash Receipt</label>
              <input type="file" id="receipt" name="receipt" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
          </form>
        </div>
      `;
      break;
    case 'Pending Confirmation':
      actionHTML = '<p class="status-message">Your payment is being verified. You will receive a confirmation shortly.</p>';
      break;
    case 'Payment Approved':
      actionHTML = `
        <div class="approved-message">
          <p><strong>Payment Approved!</strong></p>
          <p>Please pick up your order by <strong>${order.pickup_date || 'the agreed date'}</strong> at:</p>
          <address>The Little Paw Patissier, [Insert Shop Address Here]</address>
        </div>
      `;
      break;
    case 'Payment Declined':
      actionHTML = `
        <div class="declined-payment-message">
          <p><strong>Payment Incomplete.</strong> Kindly message us at <a href="https://www.facebook.com/TheLittlePawPatissier">Facebook</a> with a screenshot to get your refund.</p>
        </div>
      `;
      break;
    default:
      actionHTML = '<p class="status-message">Unknown order status.</p>';
  }

  container.innerHTML = `
    <div class="order-detail-card">
      <h2 class="detail-heading">Order #${order.id}</h2>
      <div class="detail-status status-${order.status.toLowerCase().replace(/ /g, '-')}">${order.status}</div>
      <div class="detail-date">Placed on ${order.date}</div>
      <div class="detail-items">
        ${itemsHTML}
      </div>
      <div class="detail-total">Total: ₱${order.total}</div>
      ${actionHTML}
    </div>
  `;

  lucide.createIcons();
}

window.addEventListener('DOMContentLoaded', loadOrder);