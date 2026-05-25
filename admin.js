// admin.js – Admin panel with session guard

const adminContainer = document.getElementById('adminContainer');

// ---------- Admin session check ----------
async function checkAdminSession() {
  try {
    const res = await fetch('check_admin_session.php');
    const data = await res.json();
    return data.is_admin === true;
  } catch {
    return false;
  }
}

// ---------- Redirect if not admin ----------
if (!(await checkAdminSession())) {
  window.location.href = 'admin-login.html';
}

// ---------- Fetch all orders ----------
async function fetchAllOrders() {
  try {
    const res = await fetch('get_all_orders.php');
    if (!res.ok) throw new Error('Failed to fetch orders');
    const orders = await res.json();
    renderOrdersTable(orders);
  } catch (err) {
    adminContainer.innerHTML = '<p class="error-message">Could not load orders. Please try again later.</p>';
  }
}

function renderOrdersTable(orders) {
  if (!orders || orders.length === 0) {
    adminContainer.innerHTML = '<p class="empty-message">No orders yet.</p>';
    return;
  }

  const tableHTML = `
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${orders.map(order => `
            <tr data-order-id="${order.id}">
              <td><a href="order-details.html?id=${order.id}" class="order-link">#${order.id}</a></td>
              <td>${escapeHtml(order.customer_name || 'Unknown')}</td>
              <td>₱${order.total}</td>
              <td><span class="admin-status status-${order.status.toLowerCase().replace(/ /g, '-')}">${order.status}</span></td>
              <td>${order.date}</td>
              <td class="action-buttons">
                ${getActionButtons(order)}
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `;

  adminContainer.innerHTML = tableHTML;
  attachActionListeners();
}

function getActionButtons(order) {
  if (order.status === 'Pending Approval') {
    return `
      <button class="admin-btn approve-btn" data-action="approve" data-order-id="${order.id}">Approve</button>
      <button class="admin-btn decline-btn" data-action="decline" data-order-id="${order.id}">Decline</button>
    `;
  }
  if (order.status === 'Pending Confirmation') {
    return `
      <button class="admin-btn approve-btn" data-action="confirm-payment" data-order-id="${order.id}">Confirm Payment</button>
      <button class="admin-btn decline-btn" data-action="decline-payment" data-order-id="${order.id}">Decline Payment</button>
    `;
  }
  return '';
}

function attachActionListeners() {
  document.querySelectorAll('.approve-btn[data-action="approve"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.dataset.orderId;
      if (confirm(`Approve Order #${orderId}?`)) {
        updateOrderStatus(orderId, 'approve_order.php');
      }
    });
  });

  document.querySelectorAll('.decline-btn[data-action="decline"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.dataset.orderId;
      const reason = prompt('Enter reason for declining:');
      if (reason) {
        updateOrderStatus(orderId, 'decline_order.php', { reason });
      }
    });
  });

  document.querySelectorAll('.approve-btn[data-action="confirm-payment"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.dataset.orderId;
      if (confirm(`Confirm payment for Order #${orderId}?`)) {
        updateOrderStatus(orderId, 'confirm_payment.php');
      }
    });
  });

  document.querySelectorAll('.decline-btn[data-action="decline-payment"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderId = btn.dataset.orderId;
      const reason = prompt('Enter reason for declining payment:');
      if (reason) {
        updateOrderStatus(orderId, 'decline_payment.php', { reason });
      }
    });
  });
}

async function updateOrderStatus(orderId, endpoint, extraData = {}) {
  try {
    const res = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ order_id: orderId, ...extraData }),
    });

    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Update failed');
    }

    fetchAllOrders();
  } catch (err) {
    alert('Error: ' + err.message);
  }
}

function escapeHtml(str) {
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

// ---------- Initial load ----------
window.addEventListener('DOMContentLoaded', fetchAllOrders);