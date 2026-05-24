// account.js – Front-end simulation for Account page
// Remove or replace this file when connecting to a real backend.

const signUpSection = document.querySelector('.sign-up');
const signInSection = document.querySelector('.sign-in');
const userProfileSection = document.querySelector('.user-profile');

if (signUpSection && signInSection && userProfileSection) {
  signUpSection.style.display = 'flex';
  signInSection.style.display = 'none';
  userProfileSection.style.display = 'none';
}

// Switch to Sign In
document.querySelector('.signup-login a').addEventListener('click', (e) => {
  e.preventDefault();
  signUpSection.style.display = 'none';
  signInSection.style.display = 'flex';
  userProfileSection.style.display = 'none';
});

// Switch to Sign Up
document.querySelector('.signin-signup a').addEventListener('click', (e) => {
  e.preventDefault();
  signInSection.style.display = 'none';
  signUpSection.style.display = 'flex';
  userProfileSection.style.display = 'none';
});

// Fake login → show profile
document.querySelector('.signin-form-fields').addEventListener('submit', (e) => {
  e.preventDefault();
  signInSection.style.display = 'none';
  userProfileSection.style.display = 'flex';
});

// Logout → back to Sign In
document.getElementById('logoutBtn').addEventListener('click', () => {
  userProfileSection.style.display = 'none';
  signInSection.style.display = 'flex';
});