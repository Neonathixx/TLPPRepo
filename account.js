// account.js – handles session check, profile loading, and form toggles

// ---------- Helper functions ----------
async function checkSession() {
  try {
    const res = await fetch('check_session.php');
    const data = await res.json();
    return data.logged_in === true;
  } catch {
    return false;
  }
}

async function loadUserProfile() {
  try {
    const res = await fetch('userprofile.php');
    if (!res.ok) throw new Error('Not authenticated');
    const user = await res.json();

    document.getElementById('profileName').textContent = user.name || 'User';
    document.getElementById('profileEmail').textContent = user.email || '';
    if (user.avatar) {
      document.getElementById('profileAvatar').src = user.avatar;
    }
    document.getElementById('profileOrders').textContent = (user.orders_count || 0) + ' Orders';
    document.getElementById('profilePoints').textContent = (user.points || 0) + ' Treat Points';

    // Show profile, hide forms
    document.querySelector('.sign-up').style.display = 'none';
    document.querySelector('.sign-in').style.display = 'none';
    document.querySelector('.user-profile').style.display = 'flex';
  } catch {
    // If anything fails, fall back to sign‑in
    showSignIn();
  }
}

function showSignIn() {
  document.querySelector('.sign-up').style.display = 'none';
  document.querySelector('.sign-in').style.display = 'flex';
  document.querySelector('.user-profile').style.display = 'none';
}

function showSignUp() {
  document.querySelector('.sign-in').style.display = 'none';
  document.querySelector('.sign-up').style.display = 'flex';
  document.querySelector('.user-profile').style.display = 'none';
}

// ---------- Page load ----------
window.addEventListener('DOMContentLoaded', async () => {
  const loggedIn = await checkSession();
  if (loggedIn) {
    loadUserProfile();
  } else {
    // Default view: sign‑in (you can change to showSignUp() if you prefer)
    showSignIn();
  }
});

// ---------- Toggle links ----------
document.querySelector('.signup-login a').addEventListener('click', (e) => {
  e.preventDefault();
  showSignIn();
});

document.querySelector('.signin-signup a').addEventListener('click', (e) => {
  e.preventDefault();
  showSignUp();
});

// ---------- Logout ----------
document.getElementById('logoutBtn').addEventListener('click', () => {
  // Redirect to logout.php – it will destroy the session and redirect back
  window.location.href = 'logout.php';
});