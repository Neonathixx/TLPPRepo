const form = document.getElementById('adminLoginForm');
const errorEl = document.getElementById('adminLoginError');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(form);
  const username = formData.get('username');
  const password = formData.get('password');

  try {
    const res = await fetch('admin_login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });

    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Login failed');
    }

    // Successful login – redirect to admin dashboard
    window.location.href = 'admin.html';
  } catch (err) {
    errorEl.style.display = 'block';
    errorEl.textContent = err.message;
  }
});