const container = document.getElementById('orderListContainer');
const emptyMsg = document.getElementById('emptyOrdersMessage');

async function fetchOrders() {
  try {
    const res = await fetch('get_orders.php');
    if (!res.ok) throw new Error('Failed to fetch orders');
    const orders = await res.json();
    renderOrders(orders);
  } catch (err) {
    console.error(err);
    renderOrders([]);
  }
}

function renderOrders(orders) {
  if (!orders || orders.length === 0) {
    container.innerHTML = '';
    emptyMsg.style.display = 'block';
    return;
  }

  emptyMsg.style.display = 'none';

  container.innerHTML = orders.map(order => {
    const firstItem = order.items && order.items[0];
    const imageHTML = firstItem
      ? `<img src="${firstItem.image}" alt="${firstItem.name}" class="order-card-img">`
      : '';

    return `
      <div class="order-card">
        <div class="order-card-header">
          <div class="order-card-meta">
            <span class="order-id">Order #${order.id}</span>
            <span class="order-date">${order.date}</span>
            <span class="order-status status-${order.status.toLowerCase().replace(/ /g, '-')}">${order.status}</span>
          </div>
          <div class="order-total">₱${order.total}</div>
        </div>
        <div class="order-card-body">
          ${imageHTML}
          <div class="order-card-details">
            <p class="order-item-count">${order.items ? order.items.length : 0} item(s)</p>
            <a href="order-details.html?id=${order.id}" class="btn btn-outline btn-sm">View Details</a>
          </div>
        </div>
      </div>
    `;
  }).join('');

  lucide.createIcons();
}

window.addEventListener('DOMContentLoaded', fetchOrders);